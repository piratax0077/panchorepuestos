<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/intranet', function () {
    //return view('login');
});


Route::get('/layout',function(){
    return view('layoutNuevo');
});

Route::get('/pruebaNueva', function(){
    return view('pruebaNueva');
});

//Prueba de columnas
Route::get('3columnas', function()
{
	return view('errors.3columnas');
}
);

Route::get('combito', function () {
    return view('combito');
});

//Prueba de cargar DIV con ajax jquery
Route::get('cargardiv','cargadiv_controlador@ver');
Route::get('cargardiv/ver','cargadiv_controlador@verr');
Route::post('cargardiv/cargar','cargadiv_controlador@cargar');



Route::post('3columnas/guardardatos', 'tres_col@guardardatos');
Route::post('3columnas/guardarsimilares', 'tres_col@guardarsimilares');
Route::post('3columnas/guardarfotos', 'tres_col@guardarfotos');

//Route::resource('login','logincontrolador'); //RESTFULL
Route::get('loginvs','logincontrolador@login');
Route::post('loginvs','logincontrolador@loginvs');
Route::get('/avatar-user/{filename}','logincontrolador@getAvatar');

Route::middleware(['auth'])->prefix('marcavehiculo')->group(function (){
    Route::resource('/','marcavehiculocontrolador'); //RESTFULL
    Route::get('/{id}/eliminar','marcavehiculocontrolador@destroy');
});

//MODELO VEHICULO
Route::middleware(['auth'])->prefix('modelovehiculo')->group(function ()
{
    Route::resource('/','modelovehiculocontrolador'); //RESTFULL
    Route::get('/{id}/eliminar','modelovehiculocontrolador@destroy');
    Route::get('/{id}/ver','modelovehiculocontrolador@vermodelos');
    Route::get('/damepormarca/{id}','modelovehiculocontrolador@dame_modelos');
    Route::post('/guardar','modelovehiculocontrolador@store');
    Route::get('/dameuno/{id}','modelovehiculocontrolador@dame_un_modelo');
});

//ROL
Route::middleware(['auth'])->prefix('rol')->group(function ()
{
    Route::resource('/','rolcontrolador'); //RESTFULL
    Route::get('/{id}/eliminar','rolcontrolador@destroy');
    Route::post('/guardar', 'rolcontrolador@store');
});

//FORMA DE PAGO
Route::middleware(['auth'])->prefix('formapago')->group(function ()
{
    Route::resource('/','formapagocontrolador'); //RESTFULL
    Route::get('/{id}/eliminar','formapagocontrolador@destroy');
});

Route::middleware(['auth'])->prefix('familia')->group(function ()
{
    Route::resource('/','familiacontrolador'); //RESTFULL
    Route::get('/{id}/eliminar','familiacontrolador@destroy');
});
Route::get('familiasJSON','familiacontrolador@dame_familias');

//PROVEEDOR
Route::middleware(['auth'])->prefix('proveedor')->group(function ()
{
    Route::resource('/','proveedorcontrolador'); //RESTFULL
    Route::get('/{id}/eliminar','proveedorcontrolador@destroy');
    Route::get('/dametransportistas','proveedorcontrolador@dame_transportistas');
    Route::get('/dameproveedores','proveedorcontrolador@dame_proveedores');
});

//MARCA REPUESTOS
Route::middleware(['auth'])->prefix('marcarepuesto')->group(function ()
{
    Route::resource('/','marcarepuestocontrolador'); //RESTFULL
    Route::get('/{id}/eliminar','marcarepuestocontrolador@destroy');
    Route::get('/{id}/destruir','marcarepuestocontrolador@destruir');
});
Route::get('marcarepuestoJSON','marcarepuestocontrolador@dame_marca_repuestos');

//PAIS
Route::middleware(['auth'])->prefix('pais')->group(function ()
{
    Route::resource('/','pais_controlador'); //RESTFULL
    Route::get('/{id}/eliminar','pais_controlador@destroy');
});

//MEDIDAS
Route::middleware(['auth'])->prefix('medidas')->group(function ()
{
    Route::resource('/','familiaMedidas_controlador'); //RESTFULL
    Route::get('/{id}/eliminar','familiaMedidas_controlador@destroy');
});

Route::get('paisJSON','pais_controlador@dame_paises');
Route::get('medidasJSON/{id}','familiaMedidas_controlador@dame_medidas');

//Para repuestos debemos crear propias rutas y métodos ya que utiliza 3 blades
//para guardar el  repuesto (datos del repuesto, similares, fotos)

//En el Form1 recibe datos y los manda al Form2 que recibe los similares,
//entonces datos y similares se envian a Form3 que recibe las fotos y guarda todo.
//Route::resource('repuesto','repuestocontrolador'); //RESTFULL

Route::middleware(['auth'])->prefix('repuesto')->group(function ()
{
    Route::get('/','repuestocontrolador@index');
    Route::get('{id}/eliminar','repuestocontrolador@destroy'); //Eliminar
    Route::get('/modificar','repuestocontrolador@edit');
    Route::get('/modificar/{id_repuesto}','repuestocontrolador@editar');
    Route::get('create','repuestocontrolador@create'); //ingresar datos
    Route::get('buscarcodigo/{codigo}','repuestocontrolador@buscar_por_codigo');
    Route::post('datos','repuestocontrolador@datos'); //guardar datos
    Route::post('modificado','repuestocontrolador@guardar_repuesto_modificado');
    Route::post('buscarepuestos','repuestocontrolador@buscarepuestos'); //buscar datos
    Route::get('proveedor/{id_prov}','repuestocontrolador@dame_repuestos_x_proveedor');
    Route::post('actualizasaldos','repuestocontrolador@actualiza_saldos');
    Route::get('/buscarcodint/{codint}','repuestocontrolador@dame_repuesto_x_cod_int');

    Route::get('/buscar','repuestocontrolador@buscar');

    Route::get('crea_fotos','repuestocontrolador@crea_fotos'); //ingresar fotos
    Route::post('fotos','repuestocontrolador@fotos'); //guardar fotos

    Route::get('crea_similares','repuestocontrolador@crea_similares'); //ingresar similares
    Route::post('similares','repuestocontrolador@similares'); //guardar similares
    Route::post('guardaOEM','repuestocontrolador@oems'); // guardar oems
    Route::get('guardaOEM','repuestocontrolador@oems'); // guardar oems
    Route::get('actualizar_anio_similar/{dato}','repuestocontrolador@actualizar_anio_similares');


    //Para la ventana modal en repuestos.blade.php
    Route::get('{id}/damerepuesto','repuestocontrolador@dame_datos_repuesto');
    Route::get('{id}/damesimilares','repuestocontrolador@dame_similares');
    Route::get('{id}/damesimilares_modificar','repuestocontrolador@dame_similares_modificar');
    Route::get('{id}/damefotos','repuestocontrolador@fotos_repuesto');
    Route::get('{id}/damefotos_modificar','repuestocontrolador@dame_fotos_modificar');
    Route::get('{id}/dameoems','repuestocontrolador@dame_oems');
    Route::get('{id}/dameoems_modificar','repuestocontrolador@dame_oems_modificar');
    Route::get('{id}/damefabricantes','repuestocontrolador@dame_fabricantes');
    Route::get('{id}/damefabricantes_modificar','repuestocontrolador@dame_fabricantes_modificar');
    Route::get('{id}/cambiaprecio/{preciocom}/{precioven}','repuestocontrolador@cambiaprecio');
    Route::get('guardar_precio_venta/{dato}','repuestocontrolador@guardar_precio_venta');

    //Desde venta_principal.blade
    Route::post('/guardar_xpress','repuestocontrolador@guardar_xpress');
});

Route::middleware(['auth'])->prefix('similar')->group(function ()
{
    Route::get('/{id}/eliminar','similarcontrolador@eliminar'); // id es del repuesto
    Route::get('/{id}/modificar','similarcontrolador@modificar'); // id es del repuesto
});

//COMPRAS
Route::middleware(['auth'])->prefix('compras')->group(function ()
{
    Route::get('/crear','compras_controlador@crear');
    Route::post('/guardarcabecera','compras_controlador@guardarcabecera');
    Route::post('/buscarepuestos','compras_controlador@buscarepuestos');
    Route::get('/buscarepuestosprov/{cod}','compras_controlador@buscarepuestosprov');
    Route::post('/guardaritem','compras_controlador@guardaritem');
    Route::get('/{id}/dameultimoitem','compras_controlador@dameultimoitem');
    Route::get('/{id}/dameitems','compras_controlador@dameitemsfactura');
    Route::get('/{id}/eliminar','compras_controlador@eliminaritem');
    Route::get('/listar','compras_controlador@listar');
    Route::get('/{id}/proveedor','compras_controlador@dame_facturas_por_proveedor');
    Route::get('/{id}/damefactura','compras_controlador@dame_factura');
});

//FACTU PRODU
Route::middleware(['auth'])->prefix('factuprodu')->group(function ()
{
    Route::get('/crear','factuprodu_controlador@crear');
    Route::get('/{id}/utilidad','factuprodu_controlador@dameporcentaje');
    Route::get('/{id}/medidas','factuprodu_controlador@damemedidas');
    Route::post('/guardaritem','factuprodu_controlador@guardaritem');
    Route::post('/guardarfoto','factuprodu_controlador@guardarfoto');
    Route::post('/guardarsimilar','factuprodu_controlador@guardarsimilar');
    Route::post('/guardaroem','factuprodu_controlador@guardaroem');
    Route::get('/{cod}/buscarepuesto/{idprov}','factuprodu_controlador@buscarepuesto');
    Route::get('/verificafactura/{num}','factuprodu_controlador@verifica_factura');
    Route::get('/{idfoto}/borrarfoto/{idrep}','factuprodu_controlador@borrar_foto');
    Route::get('/{idsimilar}/borrarsimilar/{idrep}','factuprodu_controlador@borrar_similar');
    Route::get('/{idoem}/borraroem/{idrep}','factuprodu_controlador@borrar_oem');
    Route::get('/{crp}/proveedor/{idprov}/factura/{idfac}','factuprodu_controlador@verificacodprov');
    Route::post('/guardarfab','factuprodu_controlador@guardarfab');
    Route::get('/{idfab}/borrarfab/{idrep}','factuprodu_controlador@borrar_fab');
    Route::get('/damecompras/{idrep}','factuprodu_controlador@dame_compras');
});

//LIMITE DE CRÉDITO
Route::middleware(['auth'])->prefix('limitecredito')->group(function ()
{
    Route::get('/','limite_controlador@index');
    Route::post('/guardar','limite_controlador@store');
    Route::get('/dame','limite_controlador@damelimites');
    Route::get('/{id}/borrar','limite_controlador@destroy');
});

//DÍAS DE CRÉDITO
Route::middleware(['auth'])->prefix('diascredito')->group(function ()
{
    Route::get('/','dias_controlador@index');
    Route::post('/guardar','dias_controlador@store');
    Route::get('/dame','dias_controlador@damedias');
    Route::get('/{id}/borrar','dias_controlador@destroy');
});

//CLIENTES
Route::middleware(['auth'])->prefix('clientes')->group(function ()
{
    Route::get('/','clientes_controlador@index');
    Route::post('/guardar','clientes_controlador@store');
    Route::get('/xpress','clientes_controlador@cliente_xpress_abrir');
    Route::get('/xpress_listar_todos','clientes_controlador@cliente_xpress_listar_todos');
    Route::get('/xpress_actualizar_estado_envio/{dato}','clientes_controlador@cliente_xpress_actualizar_estado_envio');
    Route::post('/guardar_cliente_xpress','clientes_controlador@cliente_xpress_guardar');
    Route::post('/agregarfamilia','clientes_controlador@agregar_familia');
    Route::post('/buscar','clientes_controlador@buscar');
    Route::get('/{id}/cargar','clientes_controlador@cargar');
    Route::get('/{id}/borrar','clientes_controlador@destroy');
    Route::get('/{id}/borrardeuda','clientes_controlador@borrar_deuda_cero');
    //Agrega descuento por familia en clientes
    Route::post('/descfam','clientes_controlador@descfam');
    Route::get('/{id}/borrarfam','clientes_controlador@borrarfam');
    Route::get('/borrarfamtodo','clientes_controlador@borrarfamtodo');
    Route::get('/{id}/cargardescuentos','clientes_controlador@damedescuentos');
    Route::get('/dame_cuenta/{id}','clientes_controlador@damecuenta');
    Route::post('/agrega_cuenta','clientes_controlador@agregacuenta');
    Route::get('/borrar_cuenta/{data}','clientes_controlador@borrarcuenta');
    Route::get('/agregar_documento/{data}','clientes_controlador@agregar_documento');
    Route::get('/dame_tipo_documentos/{idc}','clientes_controlador@dame_documentos_cliente');
    Route::get('/borrar_documento_cliente/{data}','clientes_controlador@borrar_documento_cliente');
});
Route::get('clientes_buscar/{id}','clientes_controlador@cuenta_busqueda_clientes');




//VENTAS
Route::middleware(['auth'])->prefix('ventas')->group(function ()
{
    Route::get('/','ventas_controlador@index');
    Route::get('/damemarcas','ventas_controlador@damemarcas');
    Route::get('/damemodelos/{idmarca}','ventas_controlador@damemodelos');
    Route::get('/damefamilias/{idmodelo}','ventas_controlador@dame_familias_repuestos');
    Route::get('/{idfamilia}/{dato}/damerepuestos','ventas_controlador@dame_repuestos');
    Route::post('/agregar_carrito','ventas_controlador@agregar_carrito');
    Route::get('/dame_carrito','ventas_controlador@dame_carrito_vista');
    Route::get('/borrar_carrito/{cual}','ventas_controlador@borrar_carrito');
    Route::get('/{item}/borrar_item_carrito','ventas_controlador@borrar_item_carrito');
    Route::get('/descuento_carrito/{id_cliente}','ventas_controlador@descuentos_carrito');
    Route::get('/dame_forma_pago','ventas_controlador@dame_formas_pago');
    Route::get('/dame_forma_pago_delivery','ventas_controlador@dame_formas_pago_delivery');
    Route::get('/dame_forma_pago_modificar_pagos','ventas_controlador@dame_formas_pago_modificar_pagos');
    Route::post('/generarxml','ventas_controlador@generar_xml');
    Route::post('/enviarsii','ventas_controlador@enviar_sii');
    Route::get('/verestado/{trackid}','ventas_controlador@revisar_mail_estado');
    Route::post('/agregar_pago','ventas_controlador@agregar_pago');
    Route::post('/actualizar_pago','ventas_controlador@actualizar_pago');
    Route::get('/cargar_pago/{id_pago}','ventas_controlador@cargar_pago');

    Route::post('/guardarventa','ventas_controlador@guardar_venta');
    Route::post('/cotizar','ventas_controlador@cotizar');
    Route::get('/imprimir_cotizacion/{num}','imprimir_controlador@imprimir_cotizacion');
    //Route::get('/{id}','ventas_controlador@dame_relacionados');
    Route::get('/buscardescripcion/{descripcion}','ventas_controlador@buscar_por_descripcion');
    Route::get('/buscarcodproveedor/{codigo}','ventas_controlador@buscar_por_codigo_proveedor');
    Route::get('/buscaroem/{oem}','ventas_controlador@buscar_por_oem');
    Route::get('/buscarcodfabricante/{codfab}','ventas_controlador@buscar_por_codigo_fabricante');
    Route::get('/buscarmedidas/{medida}','ventas_controlador@buscar_por_medidas');
    Route::get('/buscarcodint/{codint}','ventas_controlador@buscar_por_codigo_interno');
    Route::get('/buscarmodelo/{modelo}','ventas_controlador@buscar_por_modelo');
    Route::get('/verificarnombrecarrito/{nombre}','ventas_controlador@verificar_nombre_carrito');
    Route::get('/guardarcarritocompleto/{nombre}/{existe}','ventas_controlador@guardar_carrito_completo');
    Route::get('/cargarcarritocompleto/{quien}','ventas_controlador@cargar_carrito_completo');
    Route::get('/cargarcarritocompleto_transferido/{nombrecarrito}/{vendedor_id}','ventas_controlador@cargar_carrito_completo_transferido');
    Route::get('/damecarritosguardados','ventas_controlador@dame_carritos_guardados');
    Route::get('/damecotizaciones/{id_cliente}','ventas_controlador@dame_cotizaciones');
    Route::get('/damecotizacionesmes/{mes}','ventas_controlador@dame_cotizaciones_mes');
    Route::get('/cargarcotizacion/{num_cotizacion}','ventas_controlador@cargar_cotizacion');
    Route::get('/damedteporfechas/{tipodte}/{fechainicial}/{fechafinal}','ventas_controlador@damedteporfechas');
    Route::get('/limpiarsesion','ventas_controlador@limpiar_sesion');
    Route::get('/setxmlimprimir','ventas_controlador@set_xml_imprimir');

    Route::get('/arqueocaja','ventas_controlador@arqueo');
    Route::get('/damecarritotransferir','ventas_controlador@dame_carrito_transferir');
});

//NOTA DE CRÉDITO
Route::middleware(['auth'])->prefix('notacredito')->group(function ()
{
    Route::get('/','nota_credito_controlador@index');
    Route::get('/cargar_documento/{doc}','nota_credito_controlador@cargar_documento');
    Route::post('/generarxml','nota_credito_controlador@generar_xml');
    Route::post('/enviarsii','nota_credito_controlador@enviar_sii');
    Route::get('/verestado/{trackid}','nota_credito_controlador@revisar_mail_estado');
    Route::get('/verestado/{trackid}','nota_credito_controlador@revisar_mail_estado');
    Route::post('/guardar_nota','nota_credito_controlador@guardar_nota');
    Route::get('/dame_nota_credito/{num_nc}','nota_credito_controlador@dame_nota_credito');
    Route::get('/dame_nota_credito_detalle/{id_nc}','nota_credito_controlador@dame_nota_credito_detalle');

});

//NOTA DE DÉBITO
Route::middleware(['auth'])->prefix('notadebito')->group(function ()
{
    Route::get('/','nota_debito_controlador@index');
    Route::get('/cargar_documento/{doc}','nota_debito_controlador@cargar_documento');
    Route::get('/existe_nc/{nc}','nota_debito_controlador@existe_nc');
    Route::post('/guardar_nota','nota_debito_controlador@guardar_nota');
    Route::post('/generarxml','nota_debito_controlador@generar_xml');
    Route::post('/enviarsii','nota_debito_controlador@enviar_sii');
    Route::post('/actualizar_estado','nota_debito_controlador@actualizar_estado');
});

//GUIA DE DESPACHO
Route::middleware(['auth'])->prefix('guiadespacho')->group(function ()
{
    Route::get('/','guia_despacho_controlador@index');
    Route::get('/cargar_documento/{doc}','guia_despacho_controlador@cargar_documento');
    Route::get('/damecliente/{rut}','guia_despacho_controlador@dame_cliente');
    Route::get('/existe_gd/{gd}','guia_despacho_controlador@existe_gd');
    Route::get('/dame_cotizacion_num/{num}','guia_despacho_controlador@dame_cotizacion_num');
    Route::post('/procesar_guia','guia_despacho_controlador@procesar_guia');
    Route::post('/guardar_guia','guia_despacho_controlador@guardar_guia');
    Route::post('/generarxml','guia_despacho_controlador@generar_xml');
    Route::post('/enviarsii','guia_despacho_controlador@enviar_sii');
    Route::post('/actualizar_estado','guia_despacho_controlador@actualizar_estado');
    Route::get('/traspaso_mercaderia','guia_despacho_controlador@traspaso_mercaderia')->name('guiadespacho.traspaso_mercaderia');
});

//ORDEN TRANSPORTE
Route::middleware(['auth'])->prefix('ot')->group(function ()
{
    Route::get('/','ot_controlador@index');
    Route::post('/guardarcabecera','ot_controlador@guardar_cabecera');
    Route::post('/guardardetalle','ot_controlador@guardar_detalle');
    Route::post('/guardardetallegrupo','ot_controlador@guardar_detalle_grupo');
    Route::get('/verificar_ot/{dato}','ot_controlador@verificar_ot');
    Route::get('/verificar_grupos/{idgrupo}','ot_controlador@verificar_grupos');
});

//SALIDA DINERO CAJA
Route::middleware(['auth'])->prefix('salida_dinero_caja')->group(function ()
{
    Route::get('/','salida_dinero_caja_controlador@index');
    Route::get('/damesalidas/{fecha}','salida_dinero_caja_controlador@dame_salidas');
    Route::post('/guardar','salida_dinero_caja_controlador@guardar');
    Route::post('/modificar','salida_dinero_caja_controlador@modificar');
    Route::get('/borrar/{id}','salida_dinero_caja_controlador@borrar');
});

Route::get('parametros','parametros_controlador@index');
Route::post('parametros/guardar','parametros_controlador@guardar');
Route::get('dameparametro/{id}','parametros_controlador@dameparametro');
Route::get('eliminarparametro/{id}','parametros_controlador@eliminarparametro');

Route::get('relacionados','relacionados_controlador@index');
//Route::post('relacionados/guardar','relacionados_controlador@guardar');
Route::get('damerelacionados/{id}','relacionados_controlador@damerelacionados');
Route::get('eliminarprincipal/{id}','relacionados_controlador@eliminarprincipal');
Route::get('eliminarrelacionado/{id}','relacionados_controlador@eliminarrelacionado');
Route::get('relacionados/{familia}/{marca}/{modelo}/damerepuestos','relacionados_controlador@dame_repuestos');
Route::get('relacionadoprincipal/{idrep}','relacionados_controlador@dame_un_repuesto');
Route::get('relacionadoguardar/{idrel}/{idpri}','relacionados_controlador@guardar_relacionado');

Route::get('bustrap431', function () {
    return view('errors.bustrap431');
});

//IMPRESIONES
Route::get('imprimir/{xml}','imprimir_controlador@imprimir')->middleware('auth');

//SERVICIOS SII

Route::middleware(['auth'])->prefix('sii')->group(function ()
{
    Route::get('/', function () {
        return view('prueba_sii');
    });
    Route::get('/estadodte','sii_controlador@estadodte');
    Route::get('/detalle', 'sii_controlador@detalle');
    Route::get('/emails/{trackID}','sii_controlador@emails');
    Route::get('/verestado/{id}','sii_controlador@ver_estadoUP');
    Route::get('/verestadotrack/{id}','sii_controlador@ver_estadotrack');
    Route::get('/verestado','sii_controlador@ver_estado');
    Route::post('/guardarcaf','sii_controlador@guardar_caf');
    Route::get('/damelocales','sii_controlador@damelocales');
    Route::get('/damedocumentos/{idlocal}','sii_controlador@damedocumentos');
    Route::get('/cargarfolios','sii_controlador@cargarfolios');
    Route::get('/anularfolios','sii_controlador@anularfolios');
    Route::get('/revisarfolios/{data}','sii_controlador@revisar_folios');
    Route::get('cambiarnumeracion/{dato}','sii_controlador@cambiarnumeracion');
    Route::get('/ambiente',function(){
        return view('certificacion.ambiente');
    });
    Route::get('/basico','sii_controlador@basico');
    Route::get('/libroventas','sii_controlador@libroventas');
    Route::get('/librocompras','sii_controlador@librocompras');
    Route::get('/setguias','sii_controlador@setguias');
    Route::get('/libroguias','sii_controlador@libroguias');
    Route::get('/simulacion','sii_controlador@simulacion');
    Route::get('/intercambio','sii_controlador@intercambio');
    Route::get('/generarPDF','sii_controlador@generarPDF');
    Route::get('/basico_boletas','sii_controlador@basico_boletas');
    Route::get('/rcof_boletas','sii_controlador@rcof_boletas');
    Route::get('/pdfcito','sii_controlador@pdfcito');
    Route::get('/directo','ventas_controlador@envio_directo');
});


Route::middleware(['auth'])->prefix('rcof')->group(function ()
{
    Route::get('/','rcof_controlador@rcof_boletas');
    Route::get('/listar_rcof/{mes}','rcof_controlador@listar_rcof');
    Route::get('/crear_rcof/{mes}','rcof_controlador@crear_rcof');
    Route::get('/procesar/{fecha}','rcof_controlador@procesar');
    Route::get('/enviar_sii/{fecha}','rcof_controlador@enviar_sii');
    Route::get('/ver_estado/{fecha}','rcof_controlador@ver_estado');
    Route::get('/ver_detalle/{fecha}','rcof_controlador@ver_detalle');
    Route::get('/actualizar_estado/{info}','rcof_controlador@actualizar_estado_BD');
});

Route::middleware(['auth'])->prefix('libro')->group(function ()
{
    Route::get('/ventas','libros_controlador@libro_ventas');
    Route::get('/ventas_resumen/{data}','libros_controlador@libro_ventas_resumen');
    Route::get('/ventas_detalle/{data}','libros_controlador@libro_ventas_detalle');
    Route::get('/ventas_generar_xml/{data}','libros_controlador@libro_ventas_generar_xml');
    Route::get('/ventas_enviar_sii/{data}','libros_controlador@libro_ventas_enviar_sii');
    Route::get('/compras','libros_controlador@libro_compras');
});

Route::middleware(['auth'])->prefix('reportes')->group(function ()
{
    Route::get('/ventasdiarias','reportes_controlador@ventasdiarias');
    Route::get('/documentosgenerados','reportes_controlador@documentosgenerados');
    Route::get('/documentosgenera2','reportes_controlador@documentosgenera2');
    Route::get('/buscar_documentos/{info}','reportes_controlador@buscar_documentos');
    Route::get('/transbank','reportes_controlador@transbank');
    Route::get('/transbank_mes/{data}','reportes_controlador@transbank_mes');
    Route::get('/transbank_dia/{fecha}','reportes_controlador@transbank_dia');
    Route::get('/detalle_documentosgenerados/{data}','reportes_controlador@detalle_documentosgenerados');
    Route::get('/totales/{fecha}','reportes_controlador@reporte_pagos');
    Route::get('/detalle/{info}','reportes_controlador@detalle');
    Route::get('/rechazados_mes/{info}','reportes_controlador@dame_rechazados_mes');
    Route::get('/deliverys_pendientes/{fecha}','reportes_controlador@delivery_pendientes_html');

});

//EJEMPLI-EMPEZANDO CON VUE
Route::get('/vue',function(){
    return view('bienvenida2');
});

Route::middleware(['auth'])->prefix('usuarios')->group(function ()
{
    // Route::get('/','users_controlador@index');
    Route::get('/','gestionUsuarios_controlador@index');
    // Route::get('crear','users_controlador@create');
    Route::get('/avatar/{filename}','gestionUsuarios_controlador@getAvatar')->name('user.avatar');
    Route::get('/crear','gestionUsuarios_controlador@create');
    Route::post('/save', 'gestionUsuarios_controlador@saveUser')->name('user.save');
    // Ruta para editar datos de usuario
    Route::get('/edit/{id}','gestionUsuarios_controlador@edit');
    // Ruta para guardar datos de usuario editado
    Route::post('/update','gestionUsuarios_controlador@update')->name('user.update');
    Route::get('/user/{id}','gestionUsuarios_controlador@getUser')->name('user');
    Route::get('/delete/{id}','gestionUsuarios_controlador@delete')->name('user.delete');
    Route::post('/cambiar-rol','gestionUsuarios_controlador@cambioRol')->name('user.cambioRol');
    Route::get('/up/{id}','gestionUsuarios_controlador@userUp')->name('user.up');
    Route::get('/down/{id}','gestionUsuarios_controlador@userDown')->name('user.down');
    Route::post('/permisos/add','gestionUsuarios_controlador@agregarPermisos')->name('user.agregarPermiso');
    Route::post('/permisos/delete','gestionUsuarios_controlador@quitarPermisos')->name('user.quitarPermiso');
});

Route::middleware(['auth'])->prefix('inventario')->group(function()
{
    Route::get('/','inventario_controlador@index');
    Route::get('/{id}', 'inventario_controlador@inventario_por_local');
    Route::post('/traslado', 'inventario_controlador@traslado');
    Route::get('/damerepuesto/{id}','inventario_controlador@damerepuesto');
    Route::get('/ordenar/{local_id}/{orden_id}','inventario_controlador@ordenar');
});


Route::get('/',function(){
    return view('index');
});

Route::get('/bienvenida',function(){
    return view('bienvenida');
});

Route::get('/timbre/{doc}/{num}', 'imprimir_controlador@dame_timbre')->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/damesesion','HomeController@dame_sesion')->middleware('auth');
Route::get('/expirósesión','HomeController@autenticado')->middleware('auth');
Route::get('/cambiarclave','HomeController@form_cambiar_clave')->middleware('auth');

Route::post('/cambiarclave','HomeController@cambiar_clave')->name('cambiarclave')->middleware('auth');
Route::post('/clave','HomeController@dame_clave')->middleware('auth');

//Pruebas de búsqueda
Route::get('/pruebas_buscar',function(){
    return view('pruebas.buscar');
});

Route::get('/probar_codint','ventas_controlador@probar_codint');
Route::get('/probar_codprov','ventas_controlador@probar_codprov');
Route::get('/probar_codoem','ventas_controlador@probar_codoem');
Route::get('/probar_codfam','ventas_controlador@probar_codfam');
Route::get('/probar_codfab','ventas_controlador@probar_codfab');
Route::get('/probar_nomfab','ventas_controlador@probar_nomfab');
Route::get('/probar_marveh','ventas_controlador@probar_marveh');
Route::get('/probar_modveh','ventas_controlador@probar_modveh');
Route::get('/probar_descrip','ventas_controlador@probar_descrip');
Route::get('/phpinfo',function(){
    phpinfo();
});

//Enviar correos
Route::post('/enviarcorreo','correos_controlador@enviar_correo')->middleware('auth');

//Tipo Documentos
Route::get('/dame_tipo_documentos','tipo_documentos_controlador@dame_tipos')->middleware('auth');

//rutas errores
Route::get('/noautenticado',function(){
    return view('errors.noautenticado');
});

Route::get('/sesionexpiro',function(){
    return view('errors.sesionexpiro');
});

Route::get('/error',function(){
    return view('errors.error_general');
});

Route::get('/semilla','nuevo_controlador@dameSemilla')->middleware('auth');
Route::get('/envia','nuevo_controlador@envia');
Route::get('/envia2','nuevo_controlador@envia2');
Route::post('/recibe','nuevo_controlador@recibe');
Auth::routes(); //no borrar y siempre al final



Route::get('/home', 'HomeController@index')->name('home');

