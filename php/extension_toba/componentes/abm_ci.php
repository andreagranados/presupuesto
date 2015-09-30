<?php
class abm_ci extends toba_ci
{
    /*agregar al atributo nombre_tabla la tabla sobre la que trabaja el ci */
    //private $nombre_tabla='';
       
    function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos($this->dep('datos')->tabla($this->nombre_tabla)->get_listado());
	}

        function evt__nuevo($datos)
	{
		$this->set_pantalla('pant_edicion');
	}
        
	function evt__cuadro__seleccion($datos)
	{
                $this->set_pantalla('pant_edicion');
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->tabla($this->nombre_tabla)->get());
		}
                
	}

	function evt__formulario__alta($datos)
	{
                /*
                 * todo: el periodo por defecto
                 */
		$this->dep('datos')->tabla($this->nombre_tabla)->set($datos);
		$this->dep('datos')->sincronizar();
                $this->resetear();
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla($this->nombre_tabla)->set($datos);
		$this->dep('datos')->sincronizar();
                $this->resetear();
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_todo();
                toba::notificacion()->agregar('El registro se ha eliminado correctamente', 'info');
		$this->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
                $this->set_pantalla('pant_cuadro');
	}

}