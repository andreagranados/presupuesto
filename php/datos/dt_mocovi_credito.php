<?php
class dt_mocovi_credito extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_mc.id_credito,
			t_mpp.id_periodo as id_periodo_nombre,
			t_ua.descripcion as id_unidad_nombre,
			t_e.descripcion as id_escalafon_nombre,
			t_mtc.tipo as id_tipo_credito_nombre,
			t_mc.descripcion,
			t_mc.credito,
			t_mp.nombre as id_programa_nombre
		FROM
			mocovi_credito as t_mc	LEFT OUTER JOIN mocovi_periodo_presupuestario as t_mpp ON (t_mc.id_periodo = t_mpp.id_periodo)
			LEFT OUTER JOIN unidad_acad as t_ua ON (t_mc.id_unidad = t_ua.sigla)
			LEFT OUTER JOIN escalafon as t_e ON (t_mc.id_escalafon = t_e.id_escalafon)
			LEFT OUTER JOIN mocovi_tipo_credito as t_mtc ON (t_mc.id_tipo_credito = t_mtc.id_tipo_credito)
			LEFT OUTER JOIN mocovi_programa as t_mp ON (t_mc.id_programa = t_mp.id_programa)
		ORDER BY descripcion";
		return toba::db('presupuesto')->consultar($sql);
	}

}

?>