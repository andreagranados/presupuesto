<?php

require_once 'dt_mocovi_costo_categoria.php';
require_once 'dt_mocovi_credito.php';
/* ejemplo no conecta a mapuche */

class consultas_mapuche {

    /**
     * 	totales
     */
    function get_totales($where = null) {
        if (is_null($where)) {
            $where = '';
        } else {
            $where = ' and ' . $where;
        }

        $sql = "SELECT  codigo_unidad, codigo_escalafon, 
       codigo_categoria_siu,
       sum(cu.credito) credito,
       sum(dias_trabajados_total)*cc.costo pagado,
       sum(dias_a_trabajar_total)*cc.costo a_pagar,
       (sum(dias_trabajados_total)+sum(dias_a_trabajar_total))*cc.costo  ejecutado,
       sum(cu.credito) -(sum(dias_trabajados_total)+sum(dias_a_trabajar_total))*cc.costo resultado
                        FROM proxy_datos_mapuche p, unidad u, credito cu,categoria c, costo_categoria cc,
                        periodo pp
                where
                p.codigo_categoria_siu=c.codigo_siu and c.id_categoria=cc.id_categoria and cc.id_periodo=3
                and  cc.id_periodo=pp.id_periodo and pp.activo_para_carga_presupuestando is true and
                p.codigo_unidad=u.codigo and u.id_unidad=cu.id_unidad and cu.id_periodo=pp.id_periodo $where
group by codigo_unidad, codigo_escalafon, 
       codigo_categoria_siu, cc.costo";
        return toba::db()->consultar($sql);
    }

    function get_a_trabajar($where = null) {
        $fecha_desde = "'2014-02-01'";
        $param['fecha_hasta'] = "'2015-01-31'";
        if (is_null($where)) {
            $where = '';
        } else {
            $where = ' where ' . $where;
        }
        $sql = "select dias.nro_legaj, dias.nro_cargo, dias.codigo_escalafon,dias.codc_carac, dias.codc_categ as codigo_siu, dias.codc_uacad, 
dias.alta, dias.baja,
  baja-alta+1 as dias_anual,
       CASE WHEN licencia.dias_lic IS NULL THEN 0
       ELSE licencia.dias_lic END as dias_lic,
       CASE WHEN licencia.dias_lic IS NULL THEN baja-alta+1
       ELSE baja-alta+1-licencia.dias_lic END as dias_a_trabajar 
from
(
select a.nro_legaj,a.nro_cargo,codc_carac,a.codc_categ,codc_uacad, b.tipo_escal as codigo_escalafon,
      CASE WHEN a.fec_alta<$fecha_desde THEN $fecha_desde
            ELSE a.fec_alta
       END as alta,
       CASE WHEN a.fec_baja>{$param['fecha_hasta']} or a.fec_baja is null THEN {$param['fecha_hasta']}
            ELSE a.fec_baja 
       END as baja
     
     
from mapuche.dh03 a, mapuche.dh11 b
where fec_alta <= {$param['fecha_hasta']} and (fec_baja >= $fecha_desde or fec_baja is null)
and a.chkstopliq=0
and a.codc_categ=b.codc_categ) dias left outer join


(select  CASE WHEN sum(hasta-desde) is null THEN 0 ELSE sum(hasta-desde)+1 END as dias_lic ,nro_cargo from 
   (select a.nro_cargo, CASE WHEN c.fec_desde<$fecha_desde THEN $fecha_desde ELSE c.fec_desde END as desde,
   CASE WHEN c.fec_hasta>{$param['fecha_hasta']} THEN {$param['fecha_hasta']} ELSE c.fec_hasta END as hasta
                                                                               
   from mapuche.dh03 a, mapuche.dh11 b,mapuche.dh05 c,mapuche.dl02 d, mapuche.dl01 e
   where 
   --a.fec_alta <= {$param['fecha_hasta']} and (a.fec_baja >= $fecha_desde or a.fec_baja is null)   --cargo activo  dentro del periodo 2014
   --a.nro_cargo=reg.cargo
   --a.chkstopliq=0
   and   (a.nro_legaj=c.nro_legaj or a.nro_cargo=c.nro_cargo )--tiene una licencia del cargo o del legajo
   and c.nrovarlicencia = d.nrovarlicencia 
   and (d.es_remunerada=false )--sin goce  dias*porcentaje
   and a.codc_categ=b.codc_categ
   and d.nrodefiniclicencia=e.nrodefiniclicencia
   and c.fec_desde<={$param['fecha_hasta']} and c.fec_hasta>=$fecha_desde
   --((c.fec_desde>=$fecha_desde and c.fec_hasta<={$param['fecha_hasta']}) or (c.fec_hasta>=$fecha_desde and c.fec_hasta<={$param['fecha_hasta']}) or (c.fec_desde>=$fecha_desde and c.fec_desde<={$param['fecha_hasta']})) 
   ) auxi
   group by nro_cargo) licencia

on dias.nro_cargo=licencia.nro_cargo $where
    limit 10
";

        $datos_mapuche = toba::db('mapuche')->consultar($sql);

        /**
         * TODO: VER SELECCI�N DE DATOS DEL PERIODO ACTUAL
         */
//       $sql="select c.codigo_siu,costo from costo_categoria cc inner join categoria c on c.id_categoria=cc.id_categoria";
//       
//       $costos_categoria=  toba::db()->consultar($sql);
//       
//       $costos=  array();
//       foreach ($costos_categoria as $costo) {
//           $costos[$costo['codigo_siu']]=$costo['costo'];
//       }
        $costos = dt_costo_categoria::get_costo_categorias_periodo_actual();


        $salida = array();
        foreach ($datos_mapuche as $fila) {
            if (isset($costos[$fila['codigo_siu']])) {
                $fila['costo_a_trabajar'] = $fila['dias_a_trabajar'] * $costos[$fila['codigo_siu']];
            } else {
                $fila['costo_a_trabajar'] = 0;
            }
            $salida[] = $fila;
        }


        //ei_arbol($costos_categoria);

        return $salida;
    }

    protected function get_parametros_periodo() {
        $sql = 'select * from mocovi_periodo_presupuestario
                where actual is true';
        $datos_mapuche = toba::db()->consultar($sql);
        $param = array();
        foreach ($datos_mapuche as $fila) {
            $param['fecha_ultima_liq'] = "'" . $fila['fecha_ultima_liquidacion'] . "'"; //por ahora para tomar 360
            $param['fecha_hasta'] = "'" . $fila['fecha_fin'] . "'";
            $param['id_liqui_ini'] = $fila['id_liqui_ini'];
            $param['id_liqui_fin'] = $fila['id_liqui_fin'];
            $param['id_liqui_1sac'] = $fila['id_liqui_1sac'];
            $param['id_liqui_2sac'] = $fila['id_liqui_2sac'];
            $param['anio'] = $fila['anio'];
            $param['fecha_desde'] = "'" . $fila['fecha_inicio']."'";
        }

        if (count($param) == 0) {
            throw new Exception("No hay un periodo Actual");
        }
//        $param['fecha_ultima_liq'] = "'2015-01-31'"; //por ahora para tomar 360
//        $param['fecha_hasta'] = "'2015-01-30'";
        /*   $param['id_liqui_ini'] = 453;
          $param['id_liqui_fin'] = 453;
          $param['id_liqui_1sac'] = 445;
          $param['id_liqui_2sac'] = 452; */
        return $param;
    }

    public function get_dias_cargo($where) {
        /*
         * todo: buscar datos de preriodo activo
         */

        $param = $this->get_parametros_periodo();
        if (is_null($where)) {
            $where = '';
        } else {
            $where = ' where ' . $where;
        }

        $sql = "
   select desc_appat, desc_nombr, 

   total.* 
   from(" . $this->dias_total($param) . ") total
    inner join mapuche.dh01 on total.nro_legaj=dh01.nro_legaj
            $where
                order by total.nro_legaj
             limit 1000   "
        ;


        $datos_mapuche = toba::db('mapuche')->consultar($sql);

        /**
         * TODO: VER SELECCI�N DE DATOS DEL PERIODO ACTUAL
         */
//       $sql="select c.codigo_siu,costo from costo_categoria cc inner join categoria c on c.id_categoria=cc.id_categoria";
//       
//       $costos_categoria=  toba::db()->consultar($sql);
//       
//       $costos=  array();
//       foreach ($costos_categoria as $costo) {
//           $costos[$costo['codigo_siu']]=$costo['costo'];
//       }


        $costos = dt_mocovi_costo_categoria::get_costo_categorias_periodo_actual();
        $salida = array();
        foreach ($datos_mapuche as $fila) {
            $fila['codc_categ'] = trim($fila['codc_categ']);
            $fila['dias_total'] = $fila['dias_a_trabajar_total'] + $fila['dias_trabajados_total'];
            if (isset($costos[$fila['codc_categ']])) {


                $costodia = $costos[$fila['codc_categ']];
                $fila['pagado'] = $fila['dias_trabajados_total'] * $costodia;
                $fila['a_pagar'] = $fila['dias_a_trabajar_total'] * $costodia;
                $fila['ejecutado'] = $fila['dias_total'] * $costodia;
                $fila['factor'] = $costodia;
            } else {
                $fila['ejecutado'] = 0;
                $fila['pagado'] = 0;
                $fila['a_pagar'] = 0;
                $fila['factor'] = 0;
            }
            $salida[] = $fila;
        }


        //ei_arbol($costos_categoria);

        return $salida;
    }

    /**
     * devuelve el query para mapuche con datos de dias_a_trabajar, dias_licencia por cargo
     * dentro del preriodo ingresado en $param $param['fecha_ultima_liq'] $param['fecha_hasta']
     * @param array $param
     * @return strig 
     */
    protected function dias_a_trabajar_total($param) {
        $sql = "

        select dias.nro_legaj, dias.nro_cargo,dias.codc_carac, dias.codc_categ, dias.codc_uacad, dias.tipo_escal,
                dias.alta , dias.baja, dias.fec_alta, dias.fec_baja,
                baja-alta+1-(baja-alta+1)%30 as dias_a_trabajar,
                CASE WHEN licencia.dias_lic IS NULL THEN 0 ELSE licencia.dias_lic END as dias_licencia,
                CASE WHEN licencia.dias_lic IS NULL THEN baja-alta+1-(baja-alta+1)%30
                     WHEN licencia.dias_lic>baja-alta+1-(baja-alta+1)%30 THEN 0
                        ELSE baja-alta+1-(baja-alta+1)%30-licencia.dias_lic END as dias_a_trabajar_total 
        from

        --dias a trabajar
                (select a.nro_legaj,a.nro_cargo,fec_alta,fec_baja,codc_carac,a.codc_categ,codc_uacad ,b.tipo_escal,
                      CASE WHEN a.fec_alta<{$param['fecha_ultima_liq']} THEN {$param['fecha_ultima_liq']} ELSE a.fec_alta
                       END as alta,
                       CASE WHEN a.fec_baja>{$param['fecha_hasta']} or a.fec_baja is null THEN {$param['fecha_hasta']} ELSE a.fec_baja 
                       END as baja

                from mapuche.dh03 a, mapuche.dh11 b, mapuche.dh01 c
                where a.nro_legaj=c.nro_legaj and c.tipo_estad<>'P' and 
                fec_alta <= {$param['fecha_hasta']} and (fec_baja >= {$param['fecha_ultima_liq']} or fec_baja is null)
                and a.chkstopliq=0
                and a.codc_categ=b.codc_categ) dias 

        left outer join

        -- dias licencia
                (select  nro_cargo, CASE WHEN sum(hasta-desde) is null THEN 0 ELSE sum(hasta-desde)+1 END as dias_lic from 
                        (select a.nro_cargo,
                        CASE WHEN c.fec_desde<{$param['fecha_ultima_liq']} THEN {$param['fecha_ultima_liq']} ELSE c.fec_desde END as desde,
                        CASE WHEN c.fec_hasta>{$param['fecha_hasta']} THEN {$param['fecha_hasta']} ELSE c.fec_hasta END as hasta
                        from mapuche.dh03 a, mapuche.dh11 b,mapuche.dh05 c,mapuche.dl02 d --, mapuche.dl01 e
                        where 
                        --a.fec_alta <= {$param['fecha_hasta']} and (a.fec_baja >= {$param['fecha_ultima_liq']} or a.fec_baja is null)   --cargo activo  dentro del periodo 2014
                        --a.nro_cargo=reg.cargo
                        a.chkstopliq=0
                        and   (a.nro_legaj=c.nro_legaj and c.nro_cargo is null or a.nro_cargo=c.nro_cargo    )--tiene una licencia del cargo o del legajo
                        and c.nrovarlicencia = d.nrovarlicencia 
                        and (d.es_remunerada=false )--sin goce  dias*porcentaje
                        and a.codc_categ=b.codc_categ
                        --and d.nrodefiniclicencia=e.nrodefiniclicencia
                        and c.fec_desde<={$param['fecha_hasta']} and c.fec_hasta>={$param['fecha_ultima_liq']}
                        --((c.fec_desde>={$param['fecha_ultima_liq']} and c.fec_hasta<={$param['fecha_hasta']}) or (c.fec_hasta>={$param['fecha_ultima_liq']} and c.fec_hasta<={$param['fecha_hasta']}) or (c.fec_desde>={$param['fecha_ultima_liq']} and c.fec_desde<={$param['fecha_hasta']})) 
                        ) auxi
                   group by nro_cargo) licencia

        on dias.nro_cargo=licencia.nro_cargo
                        ";
        return $sql;
    }

    /**
     * devuelve el query para mapuche con datos de dias_trabajados, dias_retroactivos por cargo
     * dentro del preriodo ingresado en $param $param['id_liqui_ini'], $param['id_liqui_fin']
     * @param array $param
     * @return strig 
     */
    protected function dias_trabajados_total($param) {
        $sql = "select  t.nro_legaj,t.nro_cargo,
            --t.codn_area,t.codn_subar,
            c.fec_alta,c.fec_baja,c.codc_categ,c.codc_uacad ,b.tipo_escal,
            t.dias_trabajados as dias_trabajados,
            t.dias_retro as dias_retro,
            t.dias_trabajados+t.dias_retro as dias_trabajados_total,
            t.bruto as bruto,
            t.aportes as aportes,
            t.bruto+t.aportes as costo
        from
            (select trabajados.nro_legaj,trabajados.nro_cargo,
            trabajados.dias_trab AS dias_trabajados,
            case when dias_retro is Null THEN 0 ELSE dias_retro END AS dias_retro,
            CASE WHEN bruto is Null THEN 0 ELSE bruto END AS bruto,
            CASE WHEN aportes.aportes is Null THEN 0 ELSE aportes.aportes END AS aportes

            from       
                        (select a.nro_legaj,a.nro_cargo,
                        sum(a.dias_trab) as dias_trab,
                        sum(a.dias_retro) as dias_retro,
                        sum(a.tot_haber) as bruto  --ver desglosar bruto de aguinaldo
                        from mapuche.dhr2 a, mapuche.dh22 b
                        where a.nro_liqui=b.nro_liqui
                        and a.nro_liqui>={$param['id_liqui_ini']}
                        and a.nro_liqui<={$param['id_liqui_fin']}
                        and a.nro_liqui<>{$param['id_liqui_1sac']} --sin contar dias de aguinaldo
                        and a.nro_liqui<>{$param['id_liqui_2sac']}
                        
                        group by a.nro_legaj,a.nro_cargo
                        )trabajados

full outer join (select a.nro_legaj,a.nro_cargo, sum(a.impp_conce ) as aportes
                        from mapuche.dh21h a,  mapuche.dh12 c
                        where --a.nro_liqui=b.nro_liqui
                        --and ((b.per_liano=2014 and b.per_limes>=2) or (b.per_liano=2015 and b.per_limes=1))
                         a.nro_liqui>={$param['id_liqui_ini']}
                        and a.nro_liqui<={$param['id_liqui_fin']}
                    
                        --and a.nro_liqui=b.nro_liqui
                        and a.codn_conce=c.codn_conce
                        and c.nro_orimp>0
                        and c.tipo_conce='A'
                        group by a.nro_legaj,a.nro_cargo
                        )aportes
                        on trabajados.nro_cargo=aportes.nro_cargo 
) t 

inner join dh03 c on t.nro_cargo=c.nro_cargo

inner join dh11 b on  c.codc_categ=b.codc_categ

--group by t.nro_legaj,t.nro_cargo,c.codc_categ,c.codc_uacad ,b.tipo_escal
          ";
        return $sql;
    }

    protected function dias_total($param) {
        $sql = "
select 
tot.nro_legaj,
tot.nro_cargo,
tot.fec_alta,
tot.fec_baja,
case when tot.codc_uacad='SESO' then 'FADE' when tot.codc_uacad='IBMP' then 'ESCM' else tot.codc_uacad end as codc_uacad,
--tot.codc_uacad,
tot.tipo_escal,
tot.codc_categ,
round(CAST (tot.dias_trabajados*porc_ipres/100  AS numeric),2) AS dias_trabajados,
round(CAST (tot.dias_retroactivos*porc_ipres/100 AS numeric),2) AS dias_retroactivos,
round(CAST (tot.dias_trabajados_total*porc_ipres/100 AS numeric),2) AS dias_trabajados_total,
round(CAST (tot.bruto*porc_ipres/100 AS numeric),2) AS bruto,
round(CAST (tot.aportes*porc_ipres/100 AS numeric),2) AS aportes,
round(CAST (tot.costo*porc_ipres/100 AS numeric),2) AS costo,
round(CAST (tot.dias_a_trabajar*porc_ipres/100 AS numeric),2) AS dias_a_trabajar,
round(CAST (tot.dias_licencia*porc_ipres/100 AS numeric),2) AS dias_licencia,
round(CAST (tot.dias_a_trabajar_total *porc_ipres/100 AS numeric),2) AS dias_a_trabajar_total


,dh24.codn_fuent,dh24.codn_area,dh24.codn_subar,porc_ipres from (            
select 

CASE WHEN a_trabajar.nro_legaj is Null THEN ya_trabajados.nro_legaj ELSE a_trabajar.nro_legaj END AS nro_legaj,
CASE WHEN a_trabajar.nro_cargo is Null THEN ya_trabajados.nro_cargo ELSE a_trabajar.nro_cargo END AS nro_cargo,
--ya_trabajados.codn_area,ya_trabajados.codn_subar,
CASE WHEN a_trabajar.fec_alta is Null THEN ya_trabajados.fec_alta ELSE a_trabajar.fec_alta END AS fec_alta,
CASE WHEN a_trabajar.fec_baja is Null THEN ya_trabajados.fec_baja ELSE a_trabajar.fec_baja END AS fec_baja,
CASE WHEN a_trabajar.codc_uacad is Null THEN ya_trabajados.codc_uacad ELSE a_trabajar.codc_uacad END AS codc_uacad,
CASE WHEN a_trabajar.tipo_escal is Null THEN ya_trabajados.tipo_escal ELSE a_trabajar.tipo_escal END AS tipo_escal,
CASE WHEN a_trabajar.codc_categ is Null THEN ya_trabajados.codc_categ ELSE a_trabajar.codc_categ END AS codc_categ,
CASE WHEN ya_trabajados.dias_trabajados is Null THEN 0 ELSE ya_trabajados.dias_trabajados END AS dias_trabajados,
CASE WHEN ya_trabajados.dias_retro is Null THEN 0 ELSE ya_trabajados.dias_retro END AS dias_retroactivos,
CASE WHEN ya_trabajados.dias_trabajados_total is Null THEN 0 ELSE ya_trabajados.dias_trabajados_total END AS dias_trabajados_total,
CASE WHEN ya_trabajados.bruto is Null THEN 0 ELSE ya_trabajados.bruto END AS bruto,
CASE WHEN ya_trabajados.aportes is Null THEN 0 ELSE ya_trabajados.aportes END AS aportes,
CASE WHEN ya_trabajados.costo is Null THEN 0 ELSE ya_trabajados.costo END AS costo,
CASE WHEN a_trabajar.dias_a_trabajar is Null THEN 0 ELSE a_trabajar.dias_a_trabajar END AS dias_a_trabajar,
CASE WHEN a_trabajar.dias_licencia is Null THEN 0 ELSE a_trabajar.dias_licencia END AS dias_licencia,
CASE WHEN a_trabajar.dias_a_trabajar_total is Null THEN 0 ELSE a_trabajar.dias_a_trabajar_total END AS dias_a_trabajar_total

from 
--dias a trabajar
(" . $this->dias_a_trabajar_total($param) . "
 ) a_trabajar full outer join
--dias trabajados
(" . $this->dias_trabajados_total($param) . ") ya_trabajados

on a_trabajar.nro_cargo=ya_trabajados.nro_cargo
) tot
inner join dh24 on tot.nro_cargo=dh24.nro_cargo 
--and (tot.codn_area=dh24.codn_area and tot.codn_subar=dh24.codn_subar or tot.codn_area is Null)
";
        return $sql;
    }

    public function get_dias_categoria($where) {
        /*
         * todo: buscar datos de preriodo activo
         */

        $param = $this->get_parametros_periodo();
        if (is_null($where)) {
            $where = '';
        } else {
            $where = ' where ' . $where;
        }

        $sql = "
    select codc_uacad,tipo_escal,codc_categ,codn_fuent,codn_area,codn_subar,
    count(nro_cargo) as cantidad,
    sum(dias_trabajados) as dias_trabajados,
    sum(dias_retroactivos) as dias_retroactivos,
    sum(dias_trabajados_total) as dias_trabajados_total,
    sum(dias_a_trabajar) as dias_a_trabajar,
    sum(dias_licencia) as dias_licencia,
    sum(dias_a_trabajar_total) as dias_a_trabajar_total,
    sum(dias_a_trabajar_total)+sum(dias_trabajados_total) as dias_total,
    sum(bruto) as bruto,
    sum(aportes) as aportes,
    sum(costo) as costo
   from(" . $this->dias_total($param) . ") total
            $where
    group by codc_uacad,tipo_escal,codn_area,codn_subar,codc_categ,codn_fuent
    order by codc_uacad,tipo_escal,codn_area,codn_subar,codc_categ,codn_fuent
"
        ;
        
        $datos_mapuche = toba::db('mapuche')->consultar($sql);


        $costos = dt_mocovi_costo_categoria::get_costo_categorias_periodo_actual();
        $salida = array();
        foreach ($datos_mapuche as $fila) {
            $fila['codc_categ'] = trim($fila['codc_categ']);
            $fila['dias_total'] = $fila['dias_a_trabajar_total'] + $fila['dias_trabajados_total'];
            if (isset($costos[$fila['codc_categ']])) {

                $costodia = $costos[$fila['codc_categ']];
                $fila['pagado'] = $fila['dias_trabajados_total'] * $costodia;
                $fila['a_pagar'] = $fila['dias_a_trabajar_total'] * $costodia;
                $fila['ejecutado'] = $fila['dias_total'] * $costodia;
                $fila['factor'] = $costodia;
            } else {
                $fila['ejecutado'] = 0;
                $fila['pagado'] = 0;
                $fila['a_pagar'] = 0;
                $fila['factor'] = 0;
            }
            $salida[] = $fila;
        }


        //ei_arbol($costos_categoria);

        return $salida;
    }

    public function get_credito_escalafon($where) {
        /* se obtienen los dias agrupados por categoria */
        $dias_categoria = $this->get_dias_categoria($where);
        /* se obtiene el cr�dito por unidad, escalafon, programa (area,subarea) */
        $credito_unidad = dt_mocovi_credito::get_credito_periodo_actual();
        $salida = array();
        $codigo_unidad = '';
        $codigo_escalafon = '';
        $codigo_area = '';
        $codigo_sub_area = '';
        //print_r($credito_unidad);        print_r($dias_categoria);exit;
        foreach ($dias_categoria as $fila) {
           /* if($fila['codc_uacad']=='SESO') {
                $fila['codc_uacad']='FADE';
                $codigo_unidad='FADE';
                $codigo_escalafon = $fila['tipo_escal'];
                $codigo_area = $fila['codn_area'];
                $codigo_sub_area = $fila['codn_subar']; 
              
          
            }*/
            //if($fila['codc_uacad']=='IBMP') $fila['codc_uacad']='ESCM';
            if ($codigo_unidad != $fila['codc_uacad'] || $codigo_escalafon != $fila['tipo_escal'] || $codigo_area != $fila['codn_area'] || $codigo_sub_area != $fila['codn_subar']) {
                if ($codigo_unidad != '') {
                    if (isset($credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area])) {
                        $fila_salida['credito'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['credito'];
                        $fila_salida['programa'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['nombre'];
                        $fila_salida['designaciones'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['designaciones'];
                        $fila_salida['reservas'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['reservas'];
                        
                    } else {
                        $fila_salida['credito'] = 0;
                        $fila_salida['programa'] = 'Otros';
                        $fila_salida['designaciones'] = 0;
                        $fila_salida['reservas'] = 0;
                        
                    }
                    $fila_salida['resultado'] = $fila_salida['credito'] - $fila_salida['ejecutado'];
                    $fila_salida['diferencia'] =  $fila_salida['designaciones'] - $fila_salida['ejecutado'];
                    $salida[$codigo_unidad . $codigo_escalafon . $codigo_area . $codigo_sub_area] = $fila_salida;
                }
                $codigo_unidad = $fila['codc_uacad'];
                $codigo_escalafon = $fila['tipo_escal'];
                $codigo_area = $fila['codn_area'];
                $codigo_sub_area = $fila['codn_subar'];

                $fila_salida = $fila;
                unset($fila_salida['codc_categ']);
            } else {
                $fila_salida['ejecutado'] += $fila['ejecutado'];
                $fila_salida['pagado'] += $fila['pagado'];
                $fila_salida['a_pagar'] += $fila['a_pagar'];
                $fila_salida['cantidad'] += $fila['cantidad'];
                $fila_salida['bruto'] += $fila['bruto'];
                $fila_salida['aportes'] += $fila['aportes'];
                $fila_salida['costo'] += $fila['costo'];
                $fila_salida['dias_total'] += $fila['dias_total'];
            }
        }
        if ($codigo_unidad != '') {
            if (isset($credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area])) {
                $fila_salida['credito'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['credito'];
                $fila_salida['programa'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['nombre'];
                $fila_salida['designaciones'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['designaciones'];
                $fila_salida['reservas'] = $credito_unidad[$codigo_unidad][$codigo_escalafon][$codigo_area][$codigo_sub_area]['reservas'];
            } else {
                $fila_salida['credito'] = 0;
                $fila_salida['programa'] = 'Otros';
                $fila_salida['designaciones'] = 0;
                $fila_salida['reservas'] = 0;
            }
            $fila_salida['resultado'] = $fila_salida['credito'] - $fila_salida['ejecutado'];
            $fila_salida['diferencia'] =  $fila_salida['designaciones'] - $fila_salida['ejecutado'] ;
            $salida[$codigo_unidad . $codigo_escalafon . $codigo_area . $codigo_sub_area] = $fila_salida;
        }
/*
        foreach ($credito_unidad as $codigo_unidad => $filaunidad) {
            foreach ($filaunidad as $codigo_escalafon => $filaescalafon) {
                foreach ($filaescalafon as $codigo_area => $filaarea) {
                    foreach ($filaarea as $codigo_sub_area => $value) {
                        if (!isset($salida[$codigo_unidad . $codigo_escalafon . $codigo_area . $codigo_sub_area])) {
                            $fila_salida = [];
                            $fila_salida['codc_uacad']=$codigo_unidad;
                            $fila_salida['credito'] = $value['credito'];
                            $fila_salida['programa'] = $value['nombre'];
                            $fila_salida['ejecutado'] =0;
                            $fila_salida['resultado'] = $fila_salida['credito'] - $fila_salida['ejecutado'];
                            $salida[$codigo_unidad . $codigo_escalafon . $codigo_area . $codigo_sub_area] = $fila_salida;
                            //darlo de alta
                        }
                    }
                }
            }
        }
*/
        return $salida;
    }

    /*
     * agrupa SESO  en FADE
     * y cambia IBMP por ESCM
     */

    function get_credito_escalafon_agrupado($where) {
        
        $salida = $this->get_credito_escalafon($where);
        //print_r($salida);exit;
        /*if (isset($salida['SESOD290'])) {
          $seso = $salida['SESOD290'];
          //if(isset($salida['RECT']))    $rect = $salida['RECT'];
          //if ($seso['tipo_escal'] == 'D' || $seso['tipo_escal'] == 'N') {
          $salida['FADED290']['ejecutado'] += $seso['ejecutado'];
          $salida['FADED290']['pagado'] += $seso['pagado'];
          $salida['FADED290']['a_pagar'] += $seso['a_pagar'];
          $salida['FADED290']['cantidad'] += $seso['cantidad'];
          $salida['FADED290']['resultado'] += $seso['resultado'];
          $salida['FADED290']['bruto'] += $seso['bruto'];
          $salida['FADED290']['aportes'] += $seso['aportes'];
          $salida['FADED290']['costo'] += $seso['costo'];
          $salida['FADED290']['dias_total'] += $seso['dias_total'];
          unset($salida['SESOD290']);

          //            $salida['FADE']['ejecutado'] += $rect['ejecutado'];
          //            $salida['FADE']['pagado'] += $rect['pagado'];
          //            $salida['FADE']['a_pagar'] += $rect['a_pagar'];
          //            $salida['FADE']['cantidad'] += $rect['cantidad'];
          //            $salida['FADE']['resultado'] += $rect['resultado'];
          //            $salida['FADE']['bruto'] += $rect['bruto'];
          //            $salida['FADE']['aportes'] += $rect['aportes'];
          //            $salida['FADE']['costo'] += $rect['costo'];
          //            $salida['FADE']['dias_total'] += $rect['dias_total'];
          //            unset($salida['RECT']);
          
        }*/
        return $salida;
    }

    
        public function get_credito_legajo($where) {
        /* se obtienen los dias agrupados por categoria */
        $dias_cargo = $this->get_dias_cargo($where);
        /* se obtiene el cr�dito por unidad, escalafon, programa (area,subarea) */
        $param = $this->get_parametros_periodo();
        $credito_unidad = dt_mocovi_credito::get_credito_designaciones_periodo_actual($where,$param);
        $salida = array();
        $codigo_unidad = '';
        $codigo_escalafon = '';
        $codigo_area = '';
        $codigo_sub_area = '';
        $nro_legaj= '';
        //print_r($credito_unidad);        print_r($dias_categoria);exit;
        foreach ($dias_cargo as $fila) {
           /* if($fila['codc_uacad']=='SESO') {
                $fila['codc_uacad']='FADE';
                $codigo_unidad='FADE';
                $codigo_escalafon = $fila['tipo_escal'];
                $codigo_area = $fila['codn_area'];
                $codigo_sub_area = $fila['codn_subar']; 
              
          
            }*/
            //if($fila['codc_uacad']=='IBMP') $fila['codc_uacad']='ESCM';
            
            if ($nro_legaj != $fila['nro_legaj'] ||$codigo_unidad != $fila['codc_uacad'] || $codigo_escalafon != $fila['tipo_escal'] || $codigo_area != $fila['codn_area'] || $codigo_sub_area != $fila['codn_subar']) {
                if ($nro_legaj != '') {
                    if (isset($credito_unidad[$codigo_unidad][$nro_legaj][$codigo_escalafon][$codigo_area][$codigo_sub_area])) {
                        //$fila_salida['credito'] = $credito_unidad[$codigo_unidad][$nro_legaj][$codigo_escalafon][$codigo_area][$codigo_sub_area]['credito'];
                        $fila_salida['designaciones'] = $credito_unidad[$codigo_unidad][$nro_legaj][$codigo_escalafon][$codigo_area][$codigo_sub_area]['designaciones'];
                        $fila_salida['cat_mocovi']= $credito_unidad[$codigo_unidad][$nro_legaj][$codigo_escalafon][$codigo_area][$codigo_sub_area]['cat_mocovi'];
                        
                    } else {
                        //$fila_salida['credito'] = 0;
                        $fila_salida['designaciones'] = 0;
                        $fila_salida['cat_mocovi'] = '';
                        
                    }
                    //$fila_salida['resultado'] = $fila_salida['credito'] - $fila_salida['ejecutado'];
                    $fila_salida['diferencia'] =  $fila_salida['designaciones'] - $fila_salida['ejecutado'];
                    $salida[$codigo_unidad . $nro_legaj.$codigo_escalafon . $codigo_area . $codigo_sub_area] = $fila_salida;
                }
                $nro_legaj= $fila['nro_legaj'];
                $codigo_unidad = $fila['codc_uacad'];
                $codigo_escalafon = $fila['tipo_escal'];
                $codigo_area = $fila['codn_area'];
                $codigo_sub_area = $fila['codn_subar'];
                
                $fila_salida = $fila;
                //unset($fila_salida['codc_categ']);
            } else {
                $fila_salida['ejecutado'] += $fila['ejecutado'];
                $fila_salida['pagado'] += $fila['pagado'];
                $fila_salida['a_pagar'] += $fila['a_pagar'];
                $fila_salida['codc_categ'] .= ' '.$fila['codc_categ'];
                //$fila_salida['cantidad']++;
                $fila_salida['bruto'] += $fila['bruto'];
                $fila_salida['aportes'] += $fila['aportes'];
                $fila_salida['costo'] += $fila['costo'];
                $fila_salida['dias_total'] += $fila['dias_total'];
            }
        }
        if ($nro_legaj!= '') {
            if (isset($credito_unidad[$codigo_unidad][$nro_legaj][$codigo_escalafon][$codigo_area][$codigo_sub_area])) {
               
              
                $fila_salida['designaciones'] = $credito_unidad[$codigo_unidad][$nro_legaj][$codigo_escalafon][$codigo_area][$codigo_sub_area]['designaciones'];
                $fila_salida['cat_mocovi']= $credito_unidad[$codigo_unidad][$nro_legaj][$codigo_escalafon][$codigo_area][$codigo_sub_area]['cat_mocovi'];
            } else {
                //$fila_salida['credito'] = 0;
                
                $fila_salida['designaciones'] = 0;
                 $fila_salida['cat_mocovi'] = '';
            }
            //$fila_salida['resultado'] = $fila_salida['credito'] - $fila_salida['ejecutado'];
            $fila_salida['diferencia'] =  $fila_salida['designaciones'] - $fila_salida['ejecutado'] ;
            $salida[$codigo_unidad . $codigo_escalafon . $codigo_area . $codigo_sub_area] = $fila_salida;
        }
/*
        foreach ($credito_unidad as $codigo_unidad => $filaunidad) {
            foreach ($filaunidad as $codigo_escalafon => $filaescalafon) {
                foreach ($filaescalafon as $codigo_area => $filaarea) {
                    foreach ($filaarea as $codigo_sub_area => $value) {
                        if (!isset($salida[$codigo_unidad . $codigo_escalafon . $codigo_area . $codigo_sub_area])) {
                            $fila_salida = [];
                            $fila_salida['codc_uacad']=$codigo_unidad;
                            $fila_salida['credito'] = $value['credito'];
                            $fila_salida['programa'] = $value['nombre'];
                            $fila_salida['ejecutado'] =0;
                            $fila_salida['resultado'] = $fila_salida['credito'] - $fila_salida['ejecutado'];
                            $salida[$codigo_unidad . $codigo_escalafon . $codigo_area . $codigo_sub_area] = $fila_salida;
                            //darlo de alta
                        }
                    }
                }
            }
        }
*/ //print_r($salida);exit;
        return $salida;
    }
    
    
    function get_credito_legajo_agrupado($where) {
        
        $salida = $this->get_credito_legajo($where);

        return $salida;
    }
    
}
