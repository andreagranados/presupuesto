<?php
class dt_unidad_acad extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT trim(sigla) as sigla, descripcion FROM unidad_acad ORDER BY descripcion";
		return toba::db('presupuesto')->consultar($sql);
	}

        
        function get_descripciones2()
	{
		$sql = "SELECT sigla as id_unidad, descripcion FROM unidad_acad ORDER BY descripcion";
		return toba::db('presupuesto')->consultar($sql);
	}

}
?>