@extends('plantillas.app')
@section('titulo','Inventario principal')
@section('javascript')
<script>
    function cargar_repuestos(data){
        let id = data.value;
        
        let url = '/inventario/'+id;
        $.ajax({
            type:'GET',
            url: url,
            beforeSend: function(){
                $('#inventario_local').empty();
                $('#inventario_local').append('<p>Cargando ... </p>');
            },
            success: function(repuestos){
                $('#inventario_local').empty();
                let html = `
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Repuesto</th>
                            <th scope="col">Stock actual</th>
                            <th scope="col">País</th>
                            <th scope="col">Opciones</th>
                            <th scope="col">Observacion </th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        `;
                repuestos.forEach(repuesto => {
                    if(repuesto.stock_actual <= 2){
                        html += `<tr>
                                    <td>`+repuesto.descripcion+` </td>
                                    <td style='color: red;'>`+repuesto.stock_actual+` </td> 
                                    <td style='color: red;'>`+repuesto.nombre_pais+` </td>
                                    <td><button class='btn btn-link' onclick='reponer_mercaderia(`+repuesto.id+`)'>Reponer</button> </td>
                                    <td style='color: red;'>Bajo stock </td>     
                                </tr>
                        `;
                    }else{
                        html += `<tr>
                                <td>`+repuesto.descripcion+` </td>
                                <td>`+repuesto.stock_actual+` </td>
                                <td>`+repuesto.nombre_pais+` </td>
                                <td><button class='btn btn-link' onclick='traspasar_mercaderia(`+repuesto.id+`)'>Traspasar </button> </td>
                                <td> </td>    
                            </tr>`;
                    }
                    
                });

                html += `</tbody>
                    </table>`;
               

                    $('#inventario_local').append(html);
                    $('#filtro').removeAttr('disabled');
            },
            error: function(e){
                console.log(e);
            }
        })
    }

    function traspasar_mercaderia(id){
        
        let url = '/inventario/damerepuesto/'+id;
        $.ajax({
            type:'get',
            url: url,
            success: function(data){
                
                let repuesto = data[0];
               console.log(repuesto);
                $('#info_repuesto').empty();
                $('#info_repuesto').append(`
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col">Cod Int</th>
                        <th scope="col">Descripcion</th>
                        <th scope="col">Stock Mínimo</th>
                        <th scope="col">Stock Actual</th>
                        <th scope="col">Stock Máximo</th>
                        <th scope="col">Empresa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col">`+repuesto.codigo_interno+`</th>
                            <td>`+repuesto.descripcion+`</td>
                            <td>`+repuesto.stock_minimo+` </td>
                            <td>`+repuesto.stock_actual+`</td>
                            <td>`+repuesto.stock_maximo+` </td>
                            <td>`+repuesto.empresa_nombre+` </td>
                        </tr>
                        
                    </tbody>
                    </table>`);
            },
            error: function(){

            }
        })

        $('#buscar-repuesto-modal').on('shown.bs.modal', function () {

            $("#repuesto_id").focus();

        });

        $("#buscar-repuesto-modal").modal("show");
            }

    function reponer_mercaderia(id){
        Vue.swal({

            title: 'Reponer mercaderia',

            text: "Pronto",

            icon: 'info',

            });
    }

    function confirmar(){
        let local_id = $('#local').val();
        let url = '/inventario/traslado';
        let data = {id: local_id}

        $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });

        $.ajax({
            type:'POST',
            url: url,
            data: data,
            success: function(resp){
                alert(resp);
            },
            error: function(e){
                console.log(e);
            }
        })
    }

    function ordenar(data){
        let local_id = $('#locales').val();
        let tipo_de_orden = data.value;
        let url = '/inventario/ordenar/'+local_id+'/'+tipo_de_orden;
        console.log(url);
        $.ajax({
            type:'get',
            url: url,
            success: function(repuestos){
                
                $('#inventario_local').empty();
                let html = `
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Repuesto</th>
                            <th scope="col">Stock actual</th>
                            <th scope="col">País</th>
                            <th scope="col">Opciones </th>
                            <th scope="col">Observacion </th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        `;
                repuestos.forEach(repuesto => {
                    if(repuesto.stock_actual <= 2){
                        html += `<tr>
                                    <td>`+repuesto.descripcion+` </td>
                                    <td style='color: red;'>`+repuesto.stock_actual+` </td> 
                                    <td style='color: red;'>`+repuesto.nombre_pais+` </td>
                                    <td><button class='btn btn-link' onclick='reponer_mercaderia(`+repuesto.id+`)'>Reponer</button> </td>
                                    <td style='color: red;'>Bajo stock </td>     
                                </tr>
                        `;
                    }else{
                        html += `<tr>
                                <td>`+repuesto.descripcion+` </td>
                                <td>`+repuesto.stock_actual+` </td>
                                <td>`+repuesto.nombre_pais+` </td> 
                                <td><button class='btn btn-link' onclick='traspasar_mercaderia(`+repuesto.id+`)'>Traspasar </button> </td>
                                <td> </td>    
                            </tr>`;
                    }
                    
                });

                html += `</tbody>
                    </table>`;
               

                    $('#inventario_local').append(html);
                    $('#filtro').removeAttr('disabled');
            },
            error: function(err){
                console.log(err);
            }
        })

    }
</script>
    
@endsection

@section('style')
    <style>
        
    </style>
@endsection

@section('contenido_ingresa_datos')

    <div class="g-titulo">
            <div>
                <center><h4>Inventarios por tienda</h4></center>
            </div>
            
            <div id="mensajes"></div>
    </div>    {{--  FIN de titulo --}}
<div class="container-fluid">
   <div class="row">
       <div class="col-md-6">
        <label for="locales">Elija un Local:</label>
        <select  id="locales"  onchange="cargar_repuestos(this)" class="form-control">
            <option value="0">Locales:</option>
            @foreach ($locales as $local)
                <option value="{{$local->id}}">{{$local->local_nombre}} - {{$local->local_direccion}}</option>
            @endforeach
        </select>
       </div>
       <div class="col-md-6">
           <label for="">Ordenar</label>
        <select name="" class="form-control" id="filtro" onchange="ordenar(this)" disabled>
            <option value="0">Ordenar por</option>
            <option value="1">Mayor a menor stock</option>
            <option value="2">Menor a mayor stock</option>
        </select>
       </div>
   </div>
    
    
    <div id="inventario_local">

    </div>
</div>

<div class="modal fade bd-example-modal-xl" id="buscar-repuesto-modal" tabindex="-1" role="dialog" aria-labelledby="buscar-repuesto-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <img src="{{asset('storage/imagenes/logo_pos.png')}}" alt="" srcset="" class="logoHeader">
          <h5 class="modal-title" id="exampleModalLabel">Traspaso de producto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

            <div id="info_repuesto">

            </div>
            <hr>
            <label for="locales">Elija un Local:</label>
            <select  id="local"  class="form-control" name="local">
                <option value="0">Locales:</option>
                @foreach ($locales as $local)
                    <option value="{{$local->id}}">{{$local->local_nombre}} - {{$local->local_direccion}}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" onclick="confirmar()">Generar guía de despacho</button>
        </div>
      </div>
    </div>
  </div>

@endsection