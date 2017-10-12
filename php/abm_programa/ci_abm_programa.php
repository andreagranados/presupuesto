<?php

class ci_abm_programa extends abm_ci {

    public $nombre_tabla = 'mocovi_programa';
    protected $s__where=null;
    protected $s__datos_filtro=null;

    //---- Cuadro -----------------------------------------------------------------------
        /**
     * Permite cambiar la configuración del cuadro previo a la generación de la salida
     * El formato de carga es de tipo recordset: array( array('columna' => valor, ...), ...)
     */
    
    function actualiza_imputacion($datos){
                $datos['imputacion']= str_pad($datos['programa'],2,'0',STR_PAD_LEFT).'-'.
                str_pad($datos['sub_programa'],2,'0',STR_PAD_LEFT).'-'.
                str_pad($datos['actividad'],2,'0',STR_PAD_LEFT).'-'.
                str_pad($datos['area'],3,'0',STR_PAD_LEFT).'.'.
                str_pad($datos['sub_area'],3,'0',STR_PAD_LEFT).'.'.
                str_pad($datos['sub_sub_area'],3,'0',STR_PAD_LEFT).'-'.
                str_pad($datos['fuente'],2,'0',STR_PAD_LEFT);
                return $datos;
                
    }
    
    function evt__formulario__alta($datos) {
        parent::evt__formulario__alta($this->actualiza_imputacion($datos));
    }

    function evt__formulario__modificacion($datos) {
        parent::evt__formulario__modificacion($this->actualiza_imputacion($datos));
    }

}
