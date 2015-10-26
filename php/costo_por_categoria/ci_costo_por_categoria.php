<?php
class ci_costo_por_categoria extends abm_ci
{
    protected $nombre_tabla="mocovi_costo_categoria";
    /* TODO Ver Caso del costo por 366*/
    public function evt__formulario__alta($datos) {
        $datos['costo_diario']=$datos['costo_basico']*13*1.4/366;
        parent::evt__formulario__alta($datos);
    }
    
    public function evt__formulario__modificacion($datos) {
        $datos['costo_diario']=$datos['costo_basico']*13*1.4/366;
        parent::evt__formulario__modificacion($datos);
    }
}
