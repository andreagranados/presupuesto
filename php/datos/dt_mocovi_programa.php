<?php
class dt_mocovi_programa extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_mp.id_programa,
                        t_mp.id_unidad,
			t_ua.descripcion as id_unidad_nombre,
			t_mtp.tipo as id_tipo_programa_nombre,
			t_mp.nombre,
			t_mp.area,
			t_mp.sub_area
		FROM
			mocovi_programa as t_mp	LEFT OUTER JOIN unidad_acad as t_ua ON (t_mp.id_unidad = t_ua.sigla)
			LEFT OUTER JOIN mocovi_tipo_programa as t_mtp ON (t_mp.id_tipo_programa = t_mtp.id_tipo_programa)
		ORDER BY nombre";
		return toba::db('presupuesto')->consultar($sql);
	}

	function get_descripciones()
	{
		$sql = "SELECT id_programa, nombre FROM mocovi_programa ORDER BY nombre";
		return toba::db('presupuesto')->consultar($sql);
	}

}
?>