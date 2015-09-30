<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class presupuesto_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
                'abm_ci' => 'extension_toba/componentes/abm_ci.php',
		'presupuesto_ci' => 'extension_toba/componentes/presupuesto_ci.php',
		'presupuesto_cn' => 'extension_toba/componentes/presupuesto_cn.php',
		'presupuesto_datos_relacion' => 'extension_toba/componentes/presupuesto_datos_relacion.php',
		'presupuesto_datos_tabla' => 'extension_toba/componentes/presupuesto_datos_tabla.php',
		'presupuesto_ei_arbol' => 'extension_toba/componentes/presupuesto_ei_arbol.php',
		'presupuesto_ei_archivos' => 'extension_toba/componentes/presupuesto_ei_archivos.php',
		'presupuesto_ei_calendario' => 'extension_toba/componentes/presupuesto_ei_calendario.php',
		'presupuesto_ei_codigo' => 'extension_toba/componentes/presupuesto_ei_codigo.php',
		'presupuesto_ei_cuadro' => 'extension_toba/componentes/presupuesto_ei_cuadro.php',
		'presupuesto_ei_esquema' => 'extension_toba/componentes/presupuesto_ei_esquema.php',
		'presupuesto_ei_filtro' => 'extension_toba/componentes/presupuesto_ei_filtro.php',
		'presupuesto_ei_firma' => 'extension_toba/componentes/presupuesto_ei_firma.php',
		'presupuesto_ei_formulario' => 'extension_toba/componentes/presupuesto_ei_formulario.php',
		'presupuesto_ei_formulario_ml' => 'extension_toba/componentes/presupuesto_ei_formulario_ml.php',
		'presupuesto_ei_grafico' => 'extension_toba/componentes/presupuesto_ei_grafico.php',
		'presupuesto_ei_mapa' => 'extension_toba/componentes/presupuesto_ei_mapa.php',
		'presupuesto_servicio_web' => 'extension_toba/componentes/presupuesto_servicio_web.php',
		'presupuesto_comando' => 'extension_toba/presupuesto_comando.php',
		'presupuesto_modelo' => 'extension_toba/presupuesto_modelo.php',
	);
}
?>