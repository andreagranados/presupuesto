<?php

class abm_ci extends toba_ci {
    /* agregar al atributo nombre_tabla la tabla sobre la que trabaja el ci */

    //private $nombre_tabla='';
    protected $s__where=null;
    protected $s__datos_filtro=null;

    function conf__cuadro(toba_ei_cuadro $cuadro) {
        if (!is_null($this->s__where)) {
            $datos = $this->dep('datos')->tabla($this->nombre_tabla)->get_listado($this->s__where);
        } else {
            $datos = $this->dep('datos')->tabla($this->nombre_tabla)->get_listado();
        }
        $cuadro->set_datos($datos);
    }

    //-----------------------------------------------------------------------------------
    //---- filtro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    /**
     * Permite cambiar la configuración del formulario previo a la generación de la salida
     * El formato del carga debe ser array(<campo> => <valor>, ...)
     */
    function conf__filtro(toba_ei_filtro $filtro) {
        if (isset($this->s__datos_filtro))
            $filtro->set_datos($this->s__datos_filtro);
    }

    /**
     * Atrapa la interacción del usuario con el botón asociado
     * @param array $datos Estado del componente al momento de ejecutar el evento. El formato es el mismo que en la carga de la configuración
     */
    function evt__filtro__filtrar($datos) {
        $this->s__where = $this->dep('filtro')->get_sql_where();
        $this->s__datos_filtro = $datos;
    }

    /**
     * Atrapa la interacción del usuario con el botón asociado
     */
    function evt__filtro__cancelar() {
        
    }

    function evt__nuevo($datos) {
        $this->set_pantalla('pant_edicion');
    }

    function evt__cuadro__seleccion($datos) {
        $this->set_pantalla('pant_edicion');
        $this->dep('datos')->cargar($datos);
    }

    //---- Formulario -------------------------------------------------------------------

    function conf__formulario(toba_ei_formulario $form) {
        if ($this->dep('datos')->esta_cargada()) {
            $form->set_datos($this->dep('datos')->tabla($this->nombre_tabla)->get());
        }
    }

    function evt__formulario__alta($datos) {
        /*
         * todo: el periodo por defecto
         */
        $this->dep('datos')->tabla($this->nombre_tabla)->set($datos);
        $this->dep('datos')->sincronizar();
        $this->resetear();
    }

    function evt__formulario__modificacion($datos) {
        $this->dep('datos')->tabla($this->nombre_tabla)->set($datos);
        $this->dep('datos')->sincronizar();
        $this->resetear();
    }

    function evt__formulario__baja() {
        $this->dep('datos')->eliminar_todo();
        toba::notificacion()->agregar('El registro se ha eliminado correctamente', 'info');
        $this->resetear();
    }

    function evt__formulario__cancelar() {
        $this->resetear();
        
    }

    function resetear() {
        $this->dep('datos')->resetear();
        $this->set_pantalla('pant_cuadro');
    }

}
