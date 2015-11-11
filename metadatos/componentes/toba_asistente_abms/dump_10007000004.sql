------------------------------------------------------------
--[10007000004]--  Costo por Categoría 
------------------------------------------------------------

------------------------------------------------------------
-- apex_molde_operacion
------------------------------------------------------------

--- INICIO Grupo de desarrollo 10007
INSERT INTO apex_molde_operacion (proyecto, molde, operacion_tipo, nombre, item, carpeta_archivos, prefijo_clases, fuente, punto_montaje) VALUES (
	'presupuesto', --proyecto
	'10007000004', --molde
	'10', --operacion_tipo
	'Costo por Categoría', --nombre
	'10007000009', --item
	'costo_por_categoria', --carpeta_archivos
	'_costo_por_categoria', --prefijo_clases
	'presupuesto', --fuente
	'10007000002'  --punto_montaje
);
--- FIN Grupo de desarrollo 10007

------------------------------------------------------------
-- apex_molde_operacion_abms
------------------------------------------------------------
INSERT INTO apex_molde_operacion_abms (proyecto, molde, tabla, gen_usa_filtro, gen_separar_pantallas, filtro_comprobar_parametros, cuadro_eof, cuadro_eliminar_filas, cuadro_id, cuadro_forzar_filtro, cuadro_carga_origen, cuadro_carga_sql, cuadro_carga_php_include, cuadro_carga_php_clase, cuadro_carga_php_metodo, datos_tabla_validacion, apdb_pre, punto_montaje) VALUES (
	'presupuesto', --proyecto
	'10007000004', --molde
	'mocovi_costo_categoria', --tabla
	'0', --gen_usa_filtro
	NULL, --gen_separar_pantallas
	NULL, --filtro_comprobar_parametros
	NULL, --cuadro_eof
	NULL, --cuadro_eliminar_filas
	'id_costo_categoria', --cuadro_id
	NULL, --cuadro_forzar_filtro
	'datos_tabla', --cuadro_carga_origen
	'SELECT
	t_mcc.id_costo_categoria,
	t_mpp.id_periodo as id_periodo_nombre,
	t_cs.descripcion as codigo_siu_nombre,
	t_mcc.costo_basico,
	t_mcc.costo_diario
FROM
	mocovi_costo_categoria as t_mcc	LEFT OUTER JOIN mocovi_periodo_presupuestario as t_mpp ON (t_mcc.id_periodo = t_mpp.id_periodo)
	LEFT OUTER JOIN categ_siu as t_cs ON (t_mcc.codigo_siu = t_cs.codigo_siu)', --cuadro_carga_sql
	NULL, --cuadro_carga_php_include
	NULL, --cuadro_carga_php_clase
	NULL, --cuadro_carga_php_metodo
	NULL, --datos_tabla_validacion
	NULL, --apdb_pre
	NULL  --punto_montaje
);

------------------------------------------------------------
-- apex_molde_operacion_abms_fila
------------------------------------------------------------

--- INICIO Grupo de desarrollo 10007
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_obligatorio, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc, punto_montaje) VALUES (
	'presupuesto', --proyecto
	'10007000004', --molde
	'10007000027', --fila
	'1', --orden
	'id_costo_categoria', --columna
	'1000003', --asistente_tipo_dato
	'Id Costo Categoria', --etiqueta
	'0', --en_cuadro
	'0', --en_form
	'0', --en_filtro
	'=', --filtro_operador
	'0', --cuadro_estilo
	'7', --cuadro_formato
	'E', --dt_tipo_dato
	NULL, --dt_largo
	'mocovi_costo_categoria_id_costo_categoria_seq', --dt_secuencia
	'1', --dt_pk
	'ef_editable_numero', --elemento_formulario
	'0', --ef_obligatorio
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_origen
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL, --ef_carga_col_desc
	NULL  --punto_montaje
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_obligatorio, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc, punto_montaje) VALUES (
	'presupuesto', --proyecto
	'10007000004', --molde
	'10007000028', --fila
	'2', --orden
	'id_periodo', --columna
	'1000008', --asistente_tipo_dato
	'Id Periodo', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	'=', --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	'C', --dt_tipo_dato
	NULL, --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_combo', --elemento_formulario
	'0', --ef_obligatorio
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	'datos_tabla', --ef_carga_origen
	'SELECT id_periodo, id_periodo FROM mocovi_periodo_presupuestario ORDER BY id_periodo', --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	'mocovi_periodo_presupuestario', --ef_carga_tabla
	'id_periodo', --ef_carga_col_clave
	'id_periodo', --ef_carga_col_desc
	NULL  --punto_montaje
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_obligatorio, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc, punto_montaje) VALUES (
	'presupuesto', --proyecto
	'10007000004', --molde
	'10007000029', --fila
	'3', --orden
	'codigo_siu', --columna
	'1000008', --asistente_tipo_dato
	'Codigo Siu', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	'=', --filtro_operador
	'4', --cuadro_estilo
	'1', --cuadro_formato
	'C', --dt_tipo_dato
	NULL, --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_combo', --elemento_formulario
	'0', --ef_obligatorio
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	'datos_tabla', --ef_carga_origen
	'SELECT codigo_siu, descripcion FROM categ_siu ORDER BY descripcion', --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	'categ_siu', --ef_carga_tabla
	'codigo_siu', --ef_carga_col_clave
	'descripcion', --ef_carga_col_desc
	NULL  --punto_montaje
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_obligatorio, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc, punto_montaje) VALUES (
	'presupuesto', --proyecto
	'10007000004', --molde
	'10007000030', --fila
	'4', --orden
	'costo_basico', --columna
	'1000006', --asistente_tipo_dato
	'Costo Basico', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	'=', --filtro_operador
	'1', --cuadro_estilo
	'7', --cuadro_formato
	'N', --dt_tipo_dato
	NULL, --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_editable_numero', --elemento_formulario
	'0', --ef_obligatorio
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_origen
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL, --ef_carga_col_desc
	NULL  --punto_montaje
);
INSERT INTO apex_molde_operacion_abms_fila (proyecto, molde, fila, orden, columna, asistente_tipo_dato, etiqueta, en_cuadro, en_form, en_filtro, filtro_operador, cuadro_estilo, cuadro_formato, dt_tipo_dato, dt_largo, dt_secuencia, dt_pk, elemento_formulario, ef_obligatorio, ef_desactivar_modificacion, ef_procesar_javascript, ef_carga_origen, ef_carga_sql, ef_carga_php_include, ef_carga_php_clase, ef_carga_php_metodo, ef_carga_tabla, ef_carga_col_clave, ef_carga_col_desc, punto_montaje) VALUES (
	'presupuesto', --proyecto
	'10007000004', --molde
	'10007000031', --fila
	'5', --orden
	'costo_diario', --columna
	'1000006', --asistente_tipo_dato
	'Costo Diario', --etiqueta
	'1', --en_cuadro
	'1', --en_form
	'0', --en_filtro
	'=', --filtro_operador
	'1', --cuadro_estilo
	'7', --cuadro_formato
	'N', --dt_tipo_dato
	NULL, --dt_largo
	'', --dt_secuencia
	'0', --dt_pk
	'ef_editable_numero', --elemento_formulario
	'0', --ef_obligatorio
	NULL, --ef_desactivar_modificacion
	NULL, --ef_procesar_javascript
	NULL, --ef_carga_origen
	NULL, --ef_carga_sql
	NULL, --ef_carga_php_include
	NULL, --ef_carga_php_clase
	NULL, --ef_carga_php_metodo
	NULL, --ef_carga_tabla
	NULL, --ef_carga_col_clave
	NULL, --ef_carga_col_desc
	NULL  --punto_montaje
);
--- FIN Grupo de desarrollo 10007
