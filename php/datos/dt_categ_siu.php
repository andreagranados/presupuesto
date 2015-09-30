<?php
class dt_categ_siu extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT codigo_siu, descripcion FROM categ_siu ORDER BY descripcion";
		return toba::db('presupuesto')->consultar($sql);
	}

}

?>