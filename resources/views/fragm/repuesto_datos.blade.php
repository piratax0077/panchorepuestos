<center><strong>DATOS</strong></center>

@if($repuesto->count()>0)

<div class="col-md-12">
  <label><strong>Descripción:</strong></label>
  {{$repuesto[0]->descripcion}}
</div><br>
<!--Mandamos el título del modal detalle, lo leemos con ajax y lo ponemos -->

<input type="hidden" id="titulo_detalle" value="
<center><strong>DETALLE DEL REPUESTO</strong></center>
<strong>Código:</strong>&nbsp;{{$repuesto[0]->codigo_interno}}&nbsp;&nbsp;&nbsp;&nbsp;
<strong>Familia:</strong>&nbsp;{{$repuesto[0]->nombrefamilia}}&nbsp;&nbsp;&nbsp;&nbsp;
">
<table class="table table-hover table-sm">
  <thead>
    <tr>
      <th scope="col" width="50%"></th><th width="5%" scope="col"></th><th scope="col" width="45%"></th>
    </tr>
  </thead>
  <tbody>
      <tr><th align="left">Medidas:</th><td></td><td>{{$repuesto[0]->medidas}}</td></tr>
    <tr><th align="left">Cod. Repuesto Proveedor:</td><td></td><td>{{$repuesto[0]->cod_repuesto_proveedor}}</td></tr>
      <tr><th align="left">Marca Repuesto:</td><td></td><td>{{$repuesto[0]->marcarepuesto}}</td></tr>
      <tr><th align="left">Proveedor:</td><td></td><td>{{$repuesto[0]->empresa_nombre}}</td></tr>
        <tr><th align="left">País Origen:</td><td></td><td>{{$repuesto[0]->nombre_pais}}</td></tr>
    <tr><th align="left">Precio Compra:</td><td></td><td>{{$repuesto[0]->precio_compra}}</td></tr>
    <tr><th align="left">Precio Venta:</td><td></td><td>{{$repuesto[0]->precio_venta}}</td></tr>
    <tr><th align="left">Stock Mínimo:</td><td></td><td>{{$repuesto[0]->stock_minimo}}</td></tr>
    <tr><th align="left">Stock Máximo:</td><td></td><td>{{$repuesto[0]->stock_maximo}}</td></tr>
    <tr><th align="left">Stock Actual:</td><td></td><td>{{$repuesto[0]->stock_actual}}</td></tr>


  </tbody>
</table>

@else
<div class="col-md-12">
  <label><strong>Sin datos</strong></label>
</div>
@endif






