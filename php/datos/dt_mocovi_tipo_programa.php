<?php
class dt_mocovi_tipo_programa extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_tipo_programa, tipo FROM mocovi_tipo_programa ORDER BY tipo";
		return toba::db('presupuesto')->consultar($sql);
	}

	function get_listado()
	{
		$sql = "SELECT
			t_mtp.id_tipo_programa,
			t_mtp.tipo
		FROM
			mocovi_tipo_programa as t_mtp
		ORDER BY tipo";
		return toba::db('presupuesto')->consultar($sql);
	}

}
?>