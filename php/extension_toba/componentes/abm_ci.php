<?php

class abm_ci extends toba_ci {
    /* agregar al atributo nombre_tabla la tabla sobre la que trabaja el ci */

    //private $nombre_tabla='';
    protected $s__where=null;
    protected $s__datos_filtro=null;
    protected $s__selecciona=null;

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
     * Permite cambiar la configuraci�n del formulario previo a la generaci�n de la salida
     * El formato del carga debe ser array(<campo> => <valor>, ...)
     */
    function conf__filtro(toba_ei_filtro $filtro) {
        if (isset($this->s__datos_filtro))
            $filtro->set_datos($this->s__datos_filtro);
    }

    /**
     * Atrapa la interacci�n del usuario con el bot�n asociado
     * @param array $datos Estado del componente al momento de ejecutar el evento. El formato es el mismo que en la carga de la configuraci�n
     */
    function evt__filtro__filtrar($datos) {
        $this->s__where = $this->dep('filtro')->get_sql_where();
        $this->s__datos_filtro = $datos;
    }

    /**
     * Atrapa la interacci�n del usuario con el bot�n asociado
     */
    function evt__filtro__cancelar() {
        
    }

    function evt__nuevo($datos) {
        $this->s__selecciona=false;//si presiono el boton nuevo
        $this->set_pantalla('pant_edicion');
    }

    function evt__cuadro__seleccion($datos) {
        $this->s__selecciona=true;//si presiono el boton edicion
        $this->set_pantalla('pant_edicion');
        $this->dep('datos')->cargar($datos);
    }

    //---- Formulario -------------------------------------------------------------------

    function conf__formulario(toba_ei_formulario $form) {
        
        if ($this->dep('datos')->esta_cargada()) {
            $datos=$this->dep('datos')->tabla($this->nombre_tabla)->get();
            if($this->nombre_tabla=='mocovi_credito'){
                if($this->s__selecciona){//presiono el seleccionar entonces desactivo la ua que recibe
                    $form->desactivar_efs(array('id_unidad_recibe','id_programa_recibe'));
                }
                if(isset($datos['documento'])){
                    $nomb_ra='/designa/1.0/creditos_dependencia/'.$datos['documento'];
                    $datos['imagen_vista_previa_doc'] = "<a target='_blank' href='{$nomb_ra}' >documento</a>";
                }
            }
            $form->set_datos($datos);
        }
    }

    function evt__formulario__alta($datos) {
        /*
         * todo: el periodo por defecto
         */
        if($this->nombre_tabla=='mocovi_credito'){
            if($datos['credito']>0){
                if($datos['id_tipo_credito']==2){//permuta
                    //recibe
                    $datos2['id_periodo']=$datos['id_periodo'];
                    $datos2['id_unidad']=$datos['id_unidad_recibe'];
                    $datos2['id_escalafon']=$datos['id_escalafon'];
                    $datos2['id_tipo_credito']=$datos['id_tipo_credito'];
                    $datos2['id_programa']=$datos['id_programa_recibe'];
                    $datos2['descripcion']=$datos['descripcion'];
                    $datos2['credito']=$datos['credito'];
                    $this->dep('datos')->tabla($this->nombre_tabla)->set($datos2);
                    $this->dep('datos')->sincronizar();
                    $this->resetear();
                    //adjunto
                    if(isset($datos['documento'])) {
                        $res1=$this->dep('datos')->tabla($this->nombre_tabla)->get();
                        $nombre=$res1['id_credito'].".pdf";
                        //$destino="C:/proyectos/toba_2.6.3/proyectos/designa/www/creditos_dependencia/".$nombre;
                        $destino="/home/cristian/toba_2.7.1/proyectos/designa/www/creditos_dependencia/".$nombre;
                        if(move_uploaded_file($datos['documento']['tmp_name'], $destino)){//mueve un archivo a una nueva direccion, retorna true cuando lo hace y falso en caso de que no
                           $this->dep('datos')->tabla($this->nombre_tabla)->setear_archivo($res1['id_credito'],strval($nombre));
                        }
                        
                    }
                    
                    //da credito
                    $datos3['id_periodo']=$datos['id_periodo'];
                    $datos3['id_unidad']=$datos['id_unidad'];
                    $datos3['id_escalafon']=$datos['id_escalafon'];
                    $datos3['id_tipo_credito']=$datos['id_tipo_credito'];
                    $datos3['id_programa']=$datos['id_programa'];
                    $datos3['descripcion']=$datos['descripcion'];
                    $datos3['credito']=$datos['credito']*(-1);
                    $this->dep('datos')->tabla($this->nombre_tabla)->set($datos3);
                    $this->dep('datos')->sincronizar();
                    if(isset($datos['documento'])) {
                        $res2=$this->dep('datos')->tabla($this->nombre_tabla)->get();
                        $nombre2=$res2['id_credito'].".pdf";
                        //$destino="C:/proyectos/toba_2.6.3/proyectos/designa/www/creditos_dependencia/".$nombre2;
                        $destino="/home/cristian/toba_2.7.1/proyectos/designa/www/creditos_dependencia/".$nombre2;
                        if(move_uploaded_file($datos['documento']['tmp_name'], $destino)){
                            $this->dep('datos')->tabla($this->nombre_tabla)->setear_archivo($res2['id_credito'],$nombre2);
                        }
                    }
                    
                    
                    $this->resetear();
                    
                }else{//inicial

                    $this->dep('datos')->tabla($this->nombre_tabla)->set($datos);
                    $this->dep('datos')->sincronizar();
                    $res=$this->dep('datos')->tabla($this->nombre_tabla)->get();
                    $nombre=$res['id_credito'].".pdf";
                    //$destino="C:/proyectos/toba_2.6.3/proyectos/designa/www/creditos_dependencia/".$nombre;
                    $destino="/home/cristian/toba_2.7.1/proyectos/designa/www/creditos_dependencia/".$nombre;
                    if(move_uploaded_file($datos['documento']['tmp_name'], $destino)){
                        $this->dep('datos')->tabla($this->nombre_tabla)->setear_archivo($res['id_credito'],$nombre);
                    }
                    $this->resetear();
                } 
                    
                
                 
            }else{
                toba::notificacion()->agregar('El monto no puede ser negativo', 'info');
            }
            
        }else{
            $this->dep('datos')->tabla($this->nombre_tabla)->set($datos);
            $this->dep('datos')->sincronizar();
            $this->resetear();
        }
        /*$this->dep('datos')->tabla($this->nombre_tabla)->set($datos);
        $this->dep('datos')->sincronizar();
        $this->resetear();*/
    }

    function evt__formulario__modificacion($datos) {
        if($this->nombre_tabla=='mocovi_credito'){
            //print_r($datos);
             if(isset($datos['documento'])) {
                        $res=$this->dep('datos')->tabla($this->nombre_tabla)->get();
                        //print_r($res);
                        $nombre=$res['id_credito'].".pdf";
                        //$destino="C:/proyectos/toba_2.6.3/proyectos/designa/www/creditos_dependencia/".$nombre;
                        $destino="/home/cristian/toba_2.7.1/proyectos/designa/www/creditos_dependencia/".$nombre;
                        if(move_uploaded_file($datos['documento']['tmp_name'], $destino)){//mueve un archivo a una nueva direccion, retorna true cuando lo hace y falso en caso de que no
                           $datos['documento']=$nombre;
                        }
                    } 
         }
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
