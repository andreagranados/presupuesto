<?php
class form_extension extends toba_ei_formulario
{
    function extender_objeto_js()
    {
        echo "
			{$this->objeto_js}.evt__efecto__procesar = function(es_inicial) 
			{
				if (! es_inicial) {
					this.evt__id_tipo_credito__procesar(es_inicial);
				}
			}
                        {$this->objeto_js}.evt__id_tipo_credito__procesar = function(es_inicial) 
			{
                     
				switch (this.ef('id_tipo_credito').get_estado()) {
					case '2':
						this.ef('id_unidad_recibe').mostrar(true);
                                                this.ef('id_programa_recibe').mostrar(true);
						break;
										
					default:
						this.ef('id_unidad_recibe').mostrar(false);
                                                this.ef('id_programa_recibe').mostrar(false);
						break;					
				}
                                
			}
                       			
			
                        ";
    }
}

?>