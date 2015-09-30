------------------------------------------------------------
--[10007000025]--  mocovi_credito 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 10007
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'presupuesto', --proyecto
	'10007000025', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'10007000002', --punto_montaje
	'dt_mocovi_credito', --subclase
	'datos/dt_mocovi_credito.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'mocovi_credito', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'presupuesto', --fuente_datos_proyecto
	'presupuesto', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2015-09-16 14:50:53', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 10007

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	NULL, --max_registros
	NULL, --min_registros
	NULL, --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'mocovi_credito', --tabla
	NULL, --tabla_ext
	NULL, --alias
	NULL, --modificar_claves
	'presupuesto', --fuente_datos_proyecto
	'presupuesto', --fuente_datos
	'1', --permite_actualizacion_automatica
	NULL, --esquema
	NULL  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 10007
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000026', --col_id
	'id_credito', --columna
	'E', --tipo
	'1', --pk
	'mocovi_credito_id_credito_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000027', --col_id
	'id_periodo', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000028', --col_id
	'id_unidad', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000029', --col_id
	'id_escalafon', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000030', --col_id
	'id_tipo_credito', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000031', --col_id
	'descripcion', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000032', --col_id
	'credito', --columna
	'N', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'presupuesto', --objeto_proyecto
	'10007000025', --objeto
	'10007000033', --col_id
	'id_programa', --columna
	'C', --tipo
	NULL, --pk
	NULL, --secuencia
	NULL, --largo
	NULL, --no_nulo
	NULL, --no_nulo_db
	NULL, --externa
	NULL  --tabla
);
--- FIN Grupo de desarrollo 10007
