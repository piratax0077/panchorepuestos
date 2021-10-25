<!-- www.layoutit.com -> http://bit.ly/2Yp2BK1 -->
@extends('plantillas.app')
  @section('titulo','Listar Facturas (Compras)')
  @section('javascript')
    <script type="text/javascript">
      function dameFacturasDelProveedor()
      {
        var idProveedor=document.getElementById("proveedor").value;
        var url_proveedor='{{url("compras")}}'+'/'+idProveedor+'/proveedor'; //petición

          $.ajax({
            type:'GET',
            beforeSend: function () {
              $("#mensajes").html("Obteniendo Facturas...");
            },
            url:url_proveedor,
            success:function(facturas){
              $("#listar_facturas").html(facturas);
              $("#mensajes").html("Listo...");
            },
            error: function(xhr, status, error){
              var errorMessage = xhr.status + ': ' + xhr.statusText
              $('#mensajes').html(errorMessage);
            }

          }); //Fin ajax

      }

      function dameFactura(idFactura)
      {
        var url_factura='{{url("compras")}}'+'/'+idFactura+'/damefactura'; //petición

          $.ajax({
            type:'GET',
            beforeSend: function () {
              $("#mensajes").html("Obteniendo Factura...");
            },
            url:url_factura,
            success:function(factura){
              $("#factura").html(factura);
              $("#mensajes").html("Se muestra factura...");
            },
            error: function(xhr, status, error){
              var errorMessage = xhr.status + ': ' + xhr.statusText
              $('#mensajes').html(errorMessage);
            }

          }); //Fin ajax

      }


        function confirmacion(){
          if (confirm('Esta seguro de eliminar el registro?')==true) {
            //alert('El registro ha sido eliminado correctamente!!!');
            return true;
          }else{
            //alert('Cancelo la eliminacion');
            return false;
          }
        }
    </script>

  @endsection
  @section('contenido_titulo_pagina')
<center><h2>Listar Facturas (Compras)</h2></center><br>
@endsection
@section('contenido_ingresa_datos')
<div class="container-fluid">
  <div class="row" id="mensajes"></div>
  <div class="row">
<!-- PANEL LATERAL IZQUIERDO -->
    <div class="col-4" style="background-color: #e6a3ff">
      <div class="row">

        <div class="col-9">
        <label for="proveedor">Proveedor: </label>
          @if(!empty($proveedores) or $proveedores->count()>0)
          <select name="proveedor" class="form-control" id="proveedor">
            @foreach ($proveedores as $proveedor)
              <option value="{{$proveedor->id}}">{{$proveedor->empresa_nombre_corto}}</option>
            @endforeach
          </select>
          @else
            <p>No hay proveedores registrados</p>
          @endif
        </div>
        <div class="col-3">
          <button class="btn btn-info" type="button" id="buscar_facturas" onclick="dameFacturasDelProveedor()" style="margin-top:25px">Buscar</button>
        </div>

      </div>
      <div class="row">
        <div class="col-11" id="listar_facturas"></div>
      </div>
    </div>

<!-- PANEL LATERAL DERECHO -->
    <div class="col-8" style="background-color: #e6f2ff">
      <div class="row" id="factura" style="margin:0px"></div>
    </div>
</div>
</div>
@endsection

@section('contenido_ver_datos')

@endsection
