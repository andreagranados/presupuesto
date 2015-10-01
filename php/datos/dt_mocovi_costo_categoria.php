<?php
class dt_mocovi_costo_categoria extends toba_datos_tabla
{
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
