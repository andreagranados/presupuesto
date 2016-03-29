<?php
class dt_mocovi_costo_categoria extends toba_datos_tabla
{
     static function get_costo_categorias_periodo_actual() {
        $sql = "select cc.codigo_siu,costo_basico  from 
                mocovi_costo_categoria cc 
                inner join mocovi_periodo_presupuestario p 
                on cc.id_periodo=p.id_periodo and actual is true
                ";

        $costos_categoria = toba::db()->consultar($sql);

        $costos = array();
         /*costodiacategoria= (basico + zona)*13/360*/
        foreach ($costos_categoria as $costo) {
            $costos[$costo['codigo_siu']] = $costo['costo_basico']*1.4*13/360;
        }
        return $costos;
    }
	function get_listado($where=null)
	{
            if(!is_null($where)){
            $where='Where '. $where;
            }else{
                $where='';
            }
		$sql = "SELECT
			t_mcc.id_costo_categoria,
			t_mpp.anio as id_periodo_nombre,
			t_cs.descripcion as codigo_siu_nombre,
			t_mcc.costo_basico,
			t_mcc.costo_diario
		FROM
			mocovi_costo_categoria as t_mcc	LEFT OUTER JOIN mocovi_periodo_presupuestario as t_mpp ON (t_mcc.id_periodo = t_mpp.id_periodo)
			LEFT OUTER JOIN categ_siu as t_cs ON (t_mcc.codigo_siu = t_cs.codigo_siu)
                        
                    $where";
		return toba::db('presupuesto')->consultar($sql);
	}

}
