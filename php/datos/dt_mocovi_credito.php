<?php
class dt_mocovi_credito extends toba_datos_tabla
{
        function setear_archivo($id_credito,$nombre){
            $sql="update mocovi_credito set documento='".$nombre."' where id_credito=".$id_credito;
            toba::db('presupuesto')->consultar($sql);
        }
	function get_listado($where=null)
	{
            if(!is_null($where)){
                $where="where $where";
            }else{
                $where='';
            }
		$sql = "SELECT
			t_mc.id_credito,
			t_mpp.anio as id_periodo_nombre,
			t_ua.descripcion as id_unidad_nombre,
			t_e.descripcion as id_escalafon_nombre,
			t_mtc.tipo as id_tipo_credito_nombre,
			t_mc.descripcion,
			t_mc.credito,
			t_mp.nombre as id_programa_nombre,
                        t_mtp.tipo as tipo_programa,
                        sub.fecha as fecha
		FROM
			mocovi_credito as t_mc	LEFT OUTER JOIN mocovi_periodo_presupuestario as t_mpp ON (t_mc.id_periodo = t_mpp.id_periodo)
			LEFT OUTER JOIN unidad_acad as t_ua ON (t_mc.id_unidad = t_ua.sigla)
			LEFT OUTER JOIN escalafon as t_e ON (t_mc.id_escalafon = t_e.id_escalafon)
			LEFT OUTER JOIN mocovi_tipo_credito as t_mtc ON (t_mc.id_tipo_credito = t_mtc.id_tipo_credito)
			LEFT OUTER JOIN mocovi_programa as t_mp ON (t_mc.id_programa = t_mp.id_programa)
                        LEFT OUTER JOIN mocovi_tipo_programa as t_mtp ON (t_mp.id_tipo_programa = t_mtp.id_tipo_programa)
                        LEFT OUTER JOIN ( select id_credito,max(auditoria_fecha) as fecha from public_auditoria.logs_mocovi_credito la
                                          group by la.id_credito ) sub on (sub.id_credito=t_mc.id_credito)
                        
$where

		ORDER BY descripcion";
		return toba::db('presupuesto')->consultar($sql);
	}




    static function get_credito_periodo_actual($param) {
        $sql = "
select a.*,b.designaciones
from (
select c.id_unidad as unidad,c.id_escalafon as escalafon,pp.area,pp.sub_area,pp.nombre, 
            sum(credito) as credito
            from mocovi_credito c
                
                inner join mocovi_periodo_presupuestario p on c.id_periodo=p.id_periodo and actual is true
                inner join mocovi_programa pp on c.id_programa=pp.id_programa
                group by c.id_unidad,c.id_escalafon,pp.area,pp.sub_area,pp.nombre
)a
inner join 
(
select uni_acad as unidad, escalafon,area, sub_area,  sum(costo) as designaciones, count(nro_cargo) as cantidad
from (

 select distinct uni_acad,area,sub_area,b.id_designacion,'D' as escalafon, docente_nombre,legajo,nro_cargo,anio_acad, b.desde, b.hasta,cat_mapuche, cat_mapuche_nombre,cat_estat,dedic,carac,id_departamento, id_area,id_orientacion, emite_norma, b.nro_norma,b.tipo_norma,nro_540,b.observaciones,estado,programa,porc,costo_diario,check_presup,licencia,dias_des,dias_lic,case when (dias_des-dias_lic)>=0 then ((dias_des-dias_lic)*costo_diario*porc/100) else 0 end as costo
                             from (
                             select area,sub_area,a.id_designacion,a.docente_nombre,a.legajo,a.nro_cargo,a.anio_acad, a.desde, a.hasta,a.cat_mapuche, a.cat_mapuche_nombre,a.cat_estat,a.dedic,a.carac,a.id_departamento, a.id_area,a.id_orientacion, a.uni_acad, a.emite_norma, a.nro_norma,a.tipo_norma,a.nro_540,a.observaciones,a.estado,programa,porc,a.costo_diario,check_presup,licencia,a.dias_des,sum(a.dias_lic) as dias_lic
                            from ((SELECT distinct  area,sub_area,t_d.id_designacion, trim(t_d1.apellido)||', '||t_d1.nombre as docente_nombre, t_d1.legajo, t_d.nro_cargo, t_d.anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, t_cs.descripcion as cat_mapuche_nombre, t_d.cat_estat, t_d.dedic, t_c.descripcion as carac,t_d3.descripcion as id_departamento,t_a.descripcion as id_area, t_o.descripcion as id_orientacion, t_d.uni_acad, t_m.quien_emite_norma as emite_norma, t_n.nro_norma, t_x.nombre_tipo as tipo_norma, t_d.nro_540, t_d.observaciones, t_t.id_programa, m_p.nombre as programa, t_t.porc,m_c.costo_diario, case when t_d.check_presup=0 then 'NO' else 'SI' end as check_presup,'NO' as licencia,t_d.estado,
                        0 as dias_lic, case when t_d.desde<={$param['fecha_desde']} then ( case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_d.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null) then ((({$param['fecha_hasta']})-t_d.desde+1)) else ((t_d.hasta-t_d.desde+1)) end ) end as dias_des 
                            FROM designacion as t_d LEFT OUTER JOIN categ_siu as t_cs ON (t_d.cat_mapuche = t_cs.codigo_siu) 
                            LEFT OUTER JOIN categ_estatuto as t_ce ON (t_d.cat_estat = t_ce.codigo_est) 
                            LEFT OUTER JOIN norma as t_n ON (t_d.id_norma = t_n.id_norma) 
                            LEFT OUTER JOIN tipo_emite as t_m ON (t_n.emite_norma = t_m.cod_emite) 
                            LEFT OUTER JOIN tipo_norma_exp as t_x ON (t_x.cod_tipo = t_n.tipo_norma) 
                            LEFT OUTER JOIN departamento as t_d3 ON (t_d.id_departamento = t_d3.iddepto) 
                            LEFT OUTER JOIN area as t_a ON (t_d.id_area = t_a.idarea) 
                            LEFT OUTER JOIN orientacion as t_o ON (t_d.id_orientacion = t_o.idorient and t_o.idarea=t_a.idarea)
                            LEFT OUTER JOIN imputacion as t_t ON (t_d.id_designacion = t_t.id_designacion) 
                            LEFT OUTER JOIN mocovi_programa as m_p ON (t_t.id_programa = m_p.id_programa) 
                            LEFT OUTER JOIN mocovi_periodo_presupuestario m_e ON ( m_e.anio={$param['anio']})
                            LEFT OUTER JOIN mocovi_costo_categoria as m_c ON (t_d.cat_mapuche = m_c.codigo_siu and m_c.id_periodo=m_e.id_periodo),
                            docente as t_d1,
                            caracter as t_c,
                            unidad_acad as t_ua 
                            
                        WHERE t_d.id_docente = t_d1.id_docente
                            AND t_d.carac = t_c.id_car 
                            AND t_d.uni_acad = t_ua.sigla 
                            AND t_d.tipo_desig=1 
                            AND not exists(SELECT * from novedad t_no
                                            where t_no.id_designacion=t_d.id_designacion
                                            and (t_no.tipo_nov=1 or t_no.tipo_nov=2 or t_no.tipo_nov=4 or t_no.tipo_nov=5)))
                        UNION
                        (SELECT distinct m_p.area,m_p.sub_area,t_d.id_designacion, trim(t_d1.apellido)||', '||t_d1.nombre as docente_nombre, t_d1.legajo, t_d.nro_cargo, t_d.anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, t_cs.descripcion as cat_mapuche_nombre, t_d.cat_estat, t_d.dedic, t_c.descripcion as carac, t_d3.descripcion as id_departamento,t_a.descripcion as id_area, t_o.descripcion as id_orientacion, t_d.uni_acad, t_m.quien_emite_norma as emite_norma, t_n.nro_norma, t_x.nombre_tipo as tipo_norma, t_d.nro_540, t_d.observaciones, t_t.id_programa, m_p.nombre as programa, t_t.porc,m_c.costo_diario, case when t_d.check_presup=0 then 'NO' else 'SI' end as check_presup,'NO' as licencia,t_d.estado,
                            0 as dias_lic, case when t_d.desde<={$param['fecha_desde']} then ( case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_d.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null) then ((({$param['fecha_hasta']})-t_d.desde+1)) else ((t_d.hasta-t_d.desde+1)) end ) end as dias_des 
                            FROM designacion as t_d LEFT OUTER JOIN categ_siu as t_cs ON (t_d.cat_mapuche = t_cs.codigo_siu) 
                            LEFT OUTER JOIN categ_estatuto as t_ce ON (t_d.cat_estat = t_ce.codigo_est) 
                            LEFT OUTER JOIN norma as t_n ON (t_d.id_norma = t_n.id_norma) 
                            LEFT OUTER JOIN tipo_emite as t_m ON (t_n.emite_norma = t_m.cod_emite) 
                            LEFT OUTER JOIN tipo_norma_exp as t_x ON (t_x.cod_tipo = t_n.tipo_norma) 
                            LEFT OUTER JOIN tipo_emite as t_te ON (t_d.emite_cargo_gestion = t_te.cod_emite)
                            LEFT OUTER JOIN departamento as t_d3 ON (t_d.id_departamento = t_d3.iddepto) 
                            LEFT OUTER JOIN area as t_a ON (t_d.id_area = t_a.idarea) 
                            LEFT OUTER JOIN orientacion as t_o ON (t_d.id_orientacion = t_o.idorient and t_o.idarea=t_a.idarea)
                            LEFT OUTER JOIN imputacion as t_t ON (t_d.id_designacion = t_t.id_designacion) 
                            LEFT OUTER JOIN mocovi_programa as m_p ON (t_t.id_programa = m_p.id_programa) 
                            LEFT OUTER JOIN mocovi_periodo_presupuestario m_e ON (m_e.anio={$param['anio']})
                            LEFT OUTER JOIN mocovi_costo_categoria as m_c ON (t_d.cat_mapuche = m_c.codigo_siu and m_c.id_periodo=m_e.id_periodo),
                            docente as t_d1,
                            caracter as t_c,
                            unidad_acad as t_ua,
                            novedad as t_no 
                            
                        WHERE t_d.id_docente = t_d1.id_docente
                            AND t_d.carac = t_c.id_car 
                            AND t_d.uni_acad = t_ua.sigla 
                            AND t_d.tipo_desig=1 
                            AND t_no.id_designacion=t_d.id_designacion
                            AND (((t_no.tipo_nov=2 or t_no.tipo_nov=5 ) AND (t_no.tipo_norma is null or t_no.tipo_emite is null or t_no.norma_legal is null))
                                  OR (t_no.tipo_nov=1 or t_no.tipo_nov=4))
                             )
                        UNION
                               (SELECT distinct m_p.area,m_p.sub_area,t_d.id_designacion, trim(t_d1.apellido)||', '||t_d1.nombre as docente_nombre, t_d1.legajo, t_d.nro_cargo, t_d.anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, t_cs.descripcion as cat_mapuche_nombre, t_d.cat_estat, t_d.dedic, t_c.descripcion as carac,t_d3.descripcion as id_departamento,t_a.descripcion as id_area, t_o.descripcion as id_orientacion, t_d.uni_acad, t_m.quien_emite_norma as emite_norma, t_n.nro_norma, t_x.nombre_tipo as tipo_norma, t_d.nro_540, t_d.observaciones, t_t.id_programa, m_p.nombre as programa, t_t.porc,m_c.costo_diario, case when t_d.check_presup=0 then 'NO' else 'SI' end as check_presup,'NO' as licencia,t_d.estado,
                        sum(case when (t_no.desde>{$param['fecha_hasta']} or (t_no.hasta is not null and t_no.hasta<{$param['fecha_desde']})) then 0 else (case when t_no.desde<={$param['fecha_desde']} then ( case when (t_no.hasta is null or t_no.hasta>={$param['fecha_hasta']} ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_no.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_no.hasta is null or t_no.hasta>={$param['fecha_hasta']} ) then ((({$param['fecha_hasta']})-t_no.desde+1)) else ((t_no.hasta-t_no.desde+1)) end ) end )end ) as dias_lic,
                        case when t_d.desde<={$param['fecha_desde']} then ( case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_d.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null) then ((({$param['fecha_hasta']})-t_d.desde+1)) else ((t_d.hasta-t_d.desde+1)) end ) end as dias_des 
                            FROM designacion as t_d LEFT OUTER JOIN categ_siu as t_cs ON (t_d.cat_mapuche = t_cs.codigo_siu) 
                            LEFT OUTER JOIN categ_estatuto as t_ce ON (t_d.cat_estat = t_ce.codigo_est) 
                            LEFT OUTER JOIN norma as t_n ON (t_d.id_norma = t_n.id_norma) 
                            LEFT OUTER JOIN tipo_emite as t_m ON (t_n.emite_norma = t_m.cod_emite) 
                            LEFT OUTER JOIN tipo_norma_exp as t_x ON (t_x.cod_tipo = t_n.tipo_norma) 
                            LEFT OUTER JOIN tipo_emite as t_te ON (t_d.emite_cargo_gestion = t_te.cod_emite)
                            LEFT OUTER JOIN departamento as t_d3 ON (t_d.id_departamento = t_d3.iddepto) 
                            LEFT OUTER JOIN area as t_a ON (t_d.id_area = t_a.idarea) 
                            LEFT OUTER JOIN orientacion as t_o ON (t_d.id_orientacion = t_o.idorient and t_o.idarea=t_a.idarea)
                            LEFT OUTER JOIN imputacion as t_t ON (t_d.id_designacion = t_t.id_designacion) 
                            LEFT OUTER JOIN mocovi_programa as m_p ON (t_t.id_programa = m_p.id_programa) 
                            LEFT OUTER JOIN mocovi_periodo_presupuestario m_e ON (m_e.anio={$param['anio']})
                            LEFT OUTER JOIN mocovi_costo_categoria as m_c ON (t_d.cat_mapuche = m_c.codigo_siu and m_c.id_periodo=m_e.id_periodo),
                            docente as t_d1,
                            caracter as t_c,
                            unidad_acad as t_ua,
                            novedad as t_no 
                            
                        WHERE t_d.id_docente = t_d1.id_docente
                            	AND t_d.carac = t_c.id_car 
                            	AND t_d.uni_acad = t_ua.sigla 
                           	AND t_d.tipo_desig=1 
                           	AND t_no.id_designacion=t_d.id_designacion 
                           	AND (t_no.tipo_nov=2 or t_no.tipo_nov=5) 
                           	AND t_no.tipo_norma is not null 
                           	AND t_no.tipo_emite is not null 
                           	AND t_no.norma_legal is not null
                        GROUP BY m_p.area,m_p.sub_area,t_d.id_designacion,docente_nombre,t_d1.legajo,t_d.nro_cargo,anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, cat_mapuche_nombre, cat_estat, dedic,t_c.descripcion , t_d3.descripcion , t_a.descripcion , t_o.descripcion ,t_d.uni_acad, t_m.quien_emite_norma, t_n.nro_norma, t_x.nombre_tipo , t_d.nro_540, t_d.observaciones, m_p.nombre, t_t.id_programa, t_t.porc,m_c.costo_diario,  check_presup, licencia,t_d.estado   	
                             )
                      ) a
                          
                          
                           WHERE a.desde <= {$param['fecha_hasta']} and (a.hasta >= {$param['fecha_desde']} or a.hasta is null)
                            -- and uni_acad ='FAIF'
                             GROUP BY area,sub_area,a.id_designacion,a.docente_nombre,a.legajo,a.nro_cargo,a.anio_acad, a.desde, a.hasta,a.cat_mapuche, a.cat_mapuche_nombre,a.cat_estat,a.dedic,a.carac,a.id_departamento, a.id_area,a.id_orientacion, a.uni_acad, a.emite_norma, a.nro_norma,a.tipo_norma,a.nro_540,a.observaciones,estado,programa,porc,a.costo_diario,check_presup,licencia,dias_des
                            ) b 


)aux
group by uni_acad,escalafon,area,sub_area
)b
on a.unidad=b.unidad and b.escalafon=a.escalafon and b.area=a.area and b.sub_area=a.sub_area

order by a.unidad,a.escalafon,a.area,a.sub_area

               ";
       //todo: designaciones y reservas por programa 
        $credito_unidad = toba::db()->consultar($sql);

        $credito = array();
        /* costodiacategoria= (basico + zona)*13/360 */
        foreach ($credito_unidad as $row) {
            $credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['credito'] = $row['credito'];
            $credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['nombre'] = $row['nombre'];
            $credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['designaciones'] = $row['designaciones'];
            $credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['reservas'] = 2;//$row['reservas'];
        }
        return $credito;
    }
    
    static function get_credito_designaciones_periodo_actual($where=null,$param=null) {
         //$param = $this->get_parametros_periodo();
        //todo: falta setear parametros del periodo actual
        $sql = "
            --toma periodo 2020
select uni_acad as unidad, legajo,escalafon,area, sub_area, 
string_agg(cat_mapuche, ' ') as cat_mocovi,
--'' as cat_mocovi,
sum(costo) as designaciones, count(nro_cargo) as cantidad
from (

 select distinct uni_acad,area,sub_area,b.id_designacion,'D' as escalafon, docente_nombre,legajo,nro_cargo,anio_acad, b.desde, b.hasta,cat_mapuche, cat_mapuche_nombre,cat_estat,dedic,carac,id_departamento, id_area,id_orientacion, emite_norma, b.nro_norma,b.tipo_norma,nro_540,b.observaciones,estado,programa,porc,costo_diario,check_presup,licencia,dias_des,dias_lic,case when (dias_des-dias_lic)>=0 then ((dias_des-dias_lic)*costo_diario*porc/100) else 0 end as costo
                             from (
                             select area,sub_area,a.id_designacion,a.docente_nombre,a.legajo,a.nro_cargo,a.anio_acad, a.desde, a.hasta,a.cat_mapuche, a.cat_mapuche_nombre,a.cat_estat,a.dedic,a.carac,a.id_departamento, a.id_area,a.id_orientacion, a.uni_acad, a.emite_norma, a.nro_norma,a.tipo_norma,a.nro_540,a.observaciones,a.estado,programa,porc,a.costo_diario,check_presup,licencia,a.dias_des,sum(a.dias_lic) as dias_lic
                            from ((SELECT distinct  area,sub_area,t_d.id_designacion, trim(t_d1.apellido)||', '||t_d1.nombre as docente_nombre, t_d1.legajo, t_d.nro_cargo, t_d.anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, t_cs.descripcion as cat_mapuche_nombre, t_d.cat_estat, t_d.dedic, t_c.descripcion as carac,t_d3.descripcion as id_departamento,t_a.descripcion as id_area, t_o.descripcion as id_orientacion, t_d.uni_acad, t_m.quien_emite_norma as emite_norma, t_n.nro_norma, t_x.nombre_tipo as tipo_norma, t_d.nro_540, t_d.observaciones, t_t.id_programa, m_p.nombre as programa, t_t.porc,m_c.costo_diario, case when t_d.check_presup=0 then 'NO' else 'SI' end as check_presup,'NO' as licencia,t_d.estado,
                        0 as dias_lic, case when t_d.desde<={$param['fecha_desde']} then ( case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_d.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null) then ((({$param['fecha_hasta']})-t_d.desde+1)) else ((t_d.hasta-t_d.desde+1)) end ) end as dias_des 
                            FROM designacion as t_d LEFT OUTER JOIN categ_siu as t_cs ON (t_d.cat_mapuche = t_cs.codigo_siu) 
                            LEFT OUTER JOIN categ_estatuto as t_ce ON (t_d.cat_estat = t_ce.codigo_est) 
                            LEFT OUTER JOIN norma as t_n ON (t_d.id_norma = t_n.id_norma) 
                            LEFT OUTER JOIN tipo_emite as t_m ON (t_n.emite_norma = t_m.cod_emite) 
                            LEFT OUTER JOIN tipo_norma_exp as t_x ON (t_x.cod_tipo = t_n.tipo_norma) 
                            LEFT OUTER JOIN departamento as t_d3 ON (t_d.id_departamento = t_d3.iddepto) 
                            LEFT OUTER JOIN area as t_a ON (t_d.id_area = t_a.idarea) 
                            LEFT OUTER JOIN orientacion as t_o ON (t_d.id_orientacion = t_o.idorient and t_o.idarea=t_a.idarea)
                            LEFT OUTER JOIN imputacion as t_t ON (t_d.id_designacion = t_t.id_designacion) 
                            LEFT OUTER JOIN mocovi_programa as m_p ON (t_t.id_programa = m_p.id_programa) 
                            LEFT OUTER JOIN mocovi_periodo_presupuestario m_e ON ( m_e.anio={$param['anio']})
                            LEFT OUTER JOIN mocovi_costo_categoria as m_c ON (t_d.cat_mapuche = m_c.codigo_siu and m_c.id_periodo=m_e.id_periodo),
                            docente as t_d1,
                            caracter as t_c,
                            unidad_acad as t_ua 
                            
                        WHERE t_d.id_docente = t_d1.id_docente
                            AND t_d.carac = t_c.id_car 
                            AND t_d.uni_acad = t_ua.sigla 
                            AND t_d.tipo_desig=1 
                            AND not exists(SELECT * from novedad t_no
                                            where t_no.id_designacion=t_d.id_designacion
                                            and (t_no.tipo_nov=1 or t_no.tipo_nov=2 or t_no.tipo_nov=4 or t_no.tipo_nov=5)))
                        UNION
                        (SELECT distinct m_p.area,m_p.sub_area,t_d.id_designacion, trim(t_d1.apellido)||', '||t_d1.nombre as docente_nombre, t_d1.legajo, t_d.nro_cargo, t_d.anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, t_cs.descripcion as cat_mapuche_nombre, t_d.cat_estat, t_d.dedic, t_c.descripcion as carac, t_d3.descripcion as id_departamento,t_a.descripcion as id_area, t_o.descripcion as id_orientacion, t_d.uni_acad, t_m.quien_emite_norma as emite_norma, t_n.nro_norma, t_x.nombre_tipo as tipo_norma, t_d.nro_540, t_d.observaciones, t_t.id_programa, m_p.nombre as programa, t_t.porc,m_c.costo_diario, case when t_d.check_presup=0 then 'NO' else 'SI' end as check_presup,'NO' as licencia,t_d.estado,
                            0 as dias_lic, case when t_d.desde<={$param['fecha_desde']} then ( case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_d.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null) then ((({$param['fecha_hasta']})-t_d.desde+1)) else ((t_d.hasta-t_d.desde+1)) end ) end as dias_des 
                            FROM designacion as t_d LEFT OUTER JOIN categ_siu as t_cs ON (t_d.cat_mapuche = t_cs.codigo_siu) 
                            LEFT OUTER JOIN categ_estatuto as t_ce ON (t_d.cat_estat = t_ce.codigo_est) 
                            LEFT OUTER JOIN norma as t_n ON (t_d.id_norma = t_n.id_norma) 
                            LEFT OUTER JOIN tipo_emite as t_m ON (t_n.emite_norma = t_m.cod_emite) 
                            LEFT OUTER JOIN tipo_norma_exp as t_x ON (t_x.cod_tipo = t_n.tipo_norma) 
                            LEFT OUTER JOIN tipo_emite as t_te ON (t_d.emite_cargo_gestion = t_te.cod_emite)
                            LEFT OUTER JOIN departamento as t_d3 ON (t_d.id_departamento = t_d3.iddepto) 
                            LEFT OUTER JOIN area as t_a ON (t_d.id_area = t_a.idarea) 
                            LEFT OUTER JOIN orientacion as t_o ON (t_d.id_orientacion = t_o.idorient and t_o.idarea=t_a.idarea)
                            LEFT OUTER JOIN imputacion as t_t ON (t_d.id_designacion = t_t.id_designacion) 
                            LEFT OUTER JOIN mocovi_programa as m_p ON (t_t.id_programa = m_p.id_programa) 
                            LEFT OUTER JOIN mocovi_periodo_presupuestario m_e ON (m_e.anio={$param['anio']})
                            LEFT OUTER JOIN mocovi_costo_categoria as m_c ON (t_d.cat_mapuche = m_c.codigo_siu and m_c.id_periodo=m_e.id_periodo),
                            docente as t_d1,
                            caracter as t_c,
                            unidad_acad as t_ua,
                            novedad as t_no 
                            
                        WHERE t_d.id_docente = t_d1.id_docente
                            AND t_d.carac = t_c.id_car 
                            AND t_d.uni_acad = t_ua.sigla 
                            AND t_d.tipo_desig=1 
                            AND t_no.id_designacion=t_d.id_designacion
                            AND (((t_no.tipo_nov=2 or t_no.tipo_nov=5 ) AND (t_no.tipo_norma is null or t_no.tipo_emite is null or t_no.norma_legal is null))
                                  OR (t_no.tipo_nov=1 or t_no.tipo_nov=4))
                             )
                        UNION
                               (SELECT distinct m_p.area,m_p.sub_area,t_d.id_designacion, trim(t_d1.apellido)||', '||t_d1.nombre as docente_nombre, t_d1.legajo, t_d.nro_cargo, t_d.anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, t_cs.descripcion as cat_mapuche_nombre, t_d.cat_estat, t_d.dedic, t_c.descripcion as carac,t_d3.descripcion as id_departamento,t_a.descripcion as id_area, t_o.descripcion as id_orientacion, t_d.uni_acad, t_m.quien_emite_norma as emite_norma, t_n.nro_norma, t_x.nombre_tipo as tipo_norma, t_d.nro_540, t_d.observaciones, t_t.id_programa, m_p.nombre as programa, t_t.porc,m_c.costo_diario, case when t_d.check_presup=0 then 'NO' else 'SI' end as check_presup,'NO' as licencia,t_d.estado,
                        sum(case when (t_no.desde>{$param['fecha_hasta']} or (t_no.hasta is not null and t_no.hasta<{$param['fecha_desde']})) then 0 else (case when t_no.desde<={$param['fecha_desde']} then ( case when (t_no.hasta is null or t_no.hasta>={$param['fecha_hasta']} ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_no.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_no.hasta is null or t_no.hasta>={$param['fecha_hasta']} ) then ((({$param['fecha_hasta']})-t_no.desde+1)) else ((t_no.hasta-t_no.desde+1)) end ) end )end ) as dias_lic,
                        case when t_d.desde<={$param['fecha_desde']} then ( case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null ) then (((cast({$param['fecha_hasta']} as date)-cast({$param['fecha_desde']} as date))+1)) else ((t_d.hasta-{$param['fecha_desde']})+1) end ) else (case when (t_d.hasta>={$param['fecha_hasta']} or t_d.hasta is null) then ((({$param['fecha_hasta']})-t_d.desde+1)) else ((t_d.hasta-t_d.desde+1)) end ) end as dias_des 
                            FROM designacion as t_d LEFT OUTER JOIN categ_siu as t_cs ON (t_d.cat_mapuche = t_cs.codigo_siu) 
                            LEFT OUTER JOIN categ_estatuto as t_ce ON (t_d.cat_estat = t_ce.codigo_est) 
                            LEFT OUTER JOIN norma as t_n ON (t_d.id_norma = t_n.id_norma) 
                            LEFT OUTER JOIN tipo_emite as t_m ON (t_n.emite_norma = t_m.cod_emite) 
                            LEFT OUTER JOIN tipo_norma_exp as t_x ON (t_x.cod_tipo = t_n.tipo_norma) 
                            LEFT OUTER JOIN tipo_emite as t_te ON (t_d.emite_cargo_gestion = t_te.cod_emite)
                            LEFT OUTER JOIN departamento as t_d3 ON (t_d.id_departamento = t_d3.iddepto) 
                            LEFT OUTER JOIN area as t_a ON (t_d.id_area = t_a.idarea) 
                            LEFT OUTER JOIN orientacion as t_o ON (t_d.id_orientacion = t_o.idorient and t_o.idarea=t_a.idarea)
                            LEFT OUTER JOIN imputacion as t_t ON (t_d.id_designacion = t_t.id_designacion) 
                            LEFT OUTER JOIN mocovi_programa as m_p ON (t_t.id_programa = m_p.id_programa) 
                            LEFT OUTER JOIN mocovi_periodo_presupuestario m_e ON (m_e.anio={$param['anio']})
                            LEFT OUTER JOIN mocovi_costo_categoria as m_c ON (t_d.cat_mapuche = m_c.codigo_siu and m_c.id_periodo=m_e.id_periodo),
                            docente as t_d1,
                            caracter as t_c,
                            unidad_acad as t_ua,
                            novedad as t_no 
                            
                        WHERE t_d.id_docente = t_d1.id_docente
                            	AND t_d.carac = t_c.id_car 
                            	AND t_d.uni_acad = t_ua.sigla 
                           	AND t_d.tipo_desig=1 
                           	AND t_no.id_designacion=t_d.id_designacion 
                           	AND (t_no.tipo_nov=2 or t_no.tipo_nov=5) 
                           	AND t_no.tipo_norma is not null 
                           	AND t_no.tipo_emite is not null 
                           	AND t_no.norma_legal is not null
                        GROUP BY m_p.area,m_p.sub_area,t_d.id_designacion,docente_nombre,t_d1.legajo,t_d.nro_cargo,anio_acad, t_d.desde, t_d.hasta, t_d.cat_mapuche, cat_mapuche_nombre, cat_estat, dedic,t_c.descripcion , t_d3.descripcion , t_a.descripcion , t_o.descripcion ,t_d.uni_acad, t_m.quien_emite_norma, t_n.nro_norma, t_x.nombre_tipo , t_d.nro_540, t_d.observaciones, m_p.nombre, t_t.id_programa, t_t.porc,m_c.costo_diario,  check_presup, licencia,t_d.estado   	
                             )
                      ) a
                          
                          
                           WHERE a.desde <= {$param['fecha_hasta']} and (a.hasta >= {$param['fecha_desde']} or a.hasta is null)
                            -- and uni_acad ='FAIF'
                             GROUP BY area,sub_area,a.id_designacion,a.docente_nombre,a.legajo,a.nro_cargo,a.anio_acad, a.desde, a.hasta,a.cat_mapuche, a.cat_mapuche_nombre,a.cat_estat,a.dedic,a.carac,a.id_departamento, a.id_area,a.id_orientacion, a.uni_acad, a.emite_norma, a.nro_norma,a.tipo_norma,a.nro_540,a.observaciones,estado,programa,porc,a.costo_diario,check_presup,licencia,dias_des
                            ) b 


)aux
group by uni_acad,legajo,escalafon,area,sub_area
order by uni_acad,legajo,escalafon,area,sub_area                      

               ";
       //todo: designaciones y reservas por programa 
        $credito_unidad = toba::db()->consultar($sql);

        $credito = array();
        /* costodiacategoria= (basico + zona)*13/360 */
        foreach ($credito_unidad as $row) {
            //$credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['credito'] = $row['credito'];
            //$credito[$row['unidad']][$row['legajo']][$row['escalafon']][$row['area']][$row['sub_area']]['docente_nombre'] = $row['nombre'];
            $credito[trim($row['unidad'])][$row['legajo']][$row['escalafon']][$row['area']][$row['sub_area']]['designaciones'] = $row['designaciones'];
            $credito[trim($row['unidad'])][$row['legajo']][$row['escalafon']][$row['area']][$row['sub_area']]['cat_mocovi'] = $row['cat_mocovi'];
            //$credito[$row['unidad']][$row['legajo']][$row['escalafon']][$row['area']][$row['sub_area']]['designaciones'] = $row['cantidad'];
            //$credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['reservas'] = 2;//$row['reservas'];
        }
        //print_r($credito);exit;
        return $credito;
    }
    
}