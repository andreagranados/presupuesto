<?php
class dt_mocovi_periodo_presupuestario extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_mpp.id_periodo,
			t_mpp.anio,
			t_mpp.fecha_inicio,
			t_mpp.fecha_fin,
			t_mpp.fecha_ultima_liquidacion,
			t_mpp.actual,
			t_mpp.id_liqui_ini,
			t_mpp.id_liqui_fin,
			t_mpp.id_liqui_1sac,
			t_mpp.id_liqui_2sac,
			t_mpp.presupuestando,
			t_mpp.activo_para_carga_presupuestando
		FROM
			mocovi_periodo_presupuestario as t_mpp";
		return toba::db('presupuesto')->consultar($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT id_periodo, id_periodo FROM mocovi_periodo_presupuestario ORDER BY id_periodo";
		return toba::db('presupuesto')->consultar($sql);
	}


}
?>