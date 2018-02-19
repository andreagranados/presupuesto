<?php

class ci_comparacion_legajo extends toba_ci {

    protected $s__where;
    protected $s__datos_filtro;

    //-----------------------------------------------------------------------------------
    //---- cuadro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    /**
     * Permite cambiar la configuraci�n del cuadro previo a la generaci�n de la salida
     * El formato de carga es de tipo recordset: array( array('columna' => valor, ...), ...)
     */
    function conf__cuadro(toba_ei_cuadro $cuadro) {
        $this->datos = toba::consulta_php('consultas')->get_credito_legajo_agrupado($this->s__where);
        // ei_arbol($datos);
        $cuadro->set_datos($this->datos);
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

    //-----------------------------------------------------------------------------------
    //---- grafico ----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__grafico(toba_ei_grafico $grafico) {
        if (isset($this->datos)) {
            $datos = array();
            $leyendas = array();
            foreach ($this->datos as $value) {
                $datos[] = $value['resultado'];
                $leyendas[] = $value['codc_uacad'];
            }
        }
        
            require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph.php');
		require_once (toba_dir() . '/php/3ros/jpgraph/jpgraph_bar.php');




		// Setup a basic graph context with some generous margins to be able
		// to fit the legend
		$canvas = new Graph(900, 300);
		$canvas->SetMargin(100,140,60,40);

		$canvas->title->Set('Cr�dito Disponible');
		//$canvas->title->SetFont(FF_ARIAL,FS_BOLD,14);

		// For contour plots it is custom to use a box style ofr the axis
		$canvas->legend->SetPos(0.05,0.5,'right','center');
		$canvas->SetScale('intint');
		//$canvas->SetAxisStyle(AXSTYLE_BOXOUT);
		//$canvas->xgrid->Show();
		$canvas->ygrid->Show();
                $canvas->xaxis->SetTickLabels($leyendas);


		// A simple contour plot with default arguments (e.g. 10 isobar lines)
		$cp = new BarPlot($datos);
                $cp->SetColor("#B0C4DE");
                $cp->SetFillColor("#B0C4DE");
                $cp->SetLegend("Resultado");

		$canvas->Add($cp);

		// Con esta llamada informamos al gr�fico cu�l es el gr�fico que se tiene
		// que dibujar
		$grafico->conf()->canvas__set($canvas);

    }

}

?>