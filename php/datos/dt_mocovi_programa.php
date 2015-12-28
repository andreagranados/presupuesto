<?php
class dt_mocovi_programa extends toba_datos_tabla
{
	function get_listado($where=null)
	{
            if(!is_null($where)){
                $where='Where '.$where;
            }else{
                $where='';
            }
		$sql = "SELECT
			t_mp.id_programa,
                        t_mp.id_unidad,
			t_ua.descripcion as id_unidad_nombre,
			t_mtp.tipo as id_tipo_programa_nombre,
			t_mp.nombre,
			t_mp.area,
			t_mp.sub_area,
                        t_mp.sub_sub_area,
                        t_mp.fuente,
                        t_mp.imputacion
		FROM
			mocovi_programa as t_mp	LEFT OUTER JOIN unidad_acad as t_ua ON (t_mp.id_unidad = t_ua.sigla)
			LEFT OUTER JOIN mocovi_tipo_programa as t_mtp ON (t_mp.id_tipo_programa = t_mtp.id_tipo_programa)
                        
                        $where
		ORDER BY nombre";
		return toba::db('presupuesto')->consultar($sql);
	}

	function get_descripciones($id_unidad=NULL)
	{
            
           if(!is_null($id_unidad)){
                $where='Where id_unidad=\''.$id_unidad.'\'';
            }else{
                $where='';
            }
		$sql = "SELECT id_programa, nombre FROM mocovi_programa"
                        . " $where "
                        . "ORDER BY nombre";
                //echo $sql;
		return toba::db('presupuesto')->consultar($sql);
	}

}