<?php
class ci_control_cargo extends abm_ci
{
      protected $s__where;
      protected $s__datos_filtro;
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/**
	 * Permite cambiar la configuraci�n del cuadro previo a la generaci�n de la salida
	 * El formato de carga es de tipo recordset: array( array('columna' => valor, ...), ...)
	 */
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
             $datos = toba::consulta_php('consultas')->get_dias_cargo($this->s__where);
            // ei_arbol($datos);
            $cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	/**
	 * Permite cambiar la configuraci�n del formulario previo a la generaci�n de la salida
	 * El formato del carga debe ser array(<campo> => <valor>, ...)
	 */
	function conf__filtro(toba_ei_filtro $filtro)
	{
             if (isset($this->s__datos_filtro))
                    $filtro->set_datos($this->s__datos_filtro);
	}

	/**
	 * Atrapa la interacci�n del usuario con el bot�n asociado
	 * @param array $datos Estado del componente al momento de ejecutar el evento. El formato es el mismo que en la carga de la configuraci�n
	 */
	function evt__filtro__filtrar($datos)
	{
             $this->s__where = $this->dep('filtro')->get_sql_where();
               $this->s__datos_filtro = $datos;
	}

	/**
	 * Atrapa la interacci�n del usuario con el bot�n asociado
	 */
	function evt__filtro__cancelar()
	{
	}

}
?>