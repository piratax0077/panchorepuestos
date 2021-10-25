@if($carrito->count()>0)
<script>
	$('#monto-1').val({{$total}});
</script>
@foreach($cts as $item)
@if($item->estado === 1)
<div class="alert alert-danger" role="alert">
	Tiene un carrito pendiente de {{$item->name}} de nombre {{$item->nombre_carrito}}. <a href="#" onclick="dame_carrito_transferido()">Abrir carrito</a> 
  </div>
@endif
@endforeach
<div class="row">
	<div id="_actual" class="col-sm-12 tabla-scroll-y-300">
	  <table id="tbl_carrito" class="table table-hover table-bordered table-sm letra-chica">
	    <thead>
		 <th width="5%" scope="col" class="alin-cen">Cod Int</th>
	      <th width="52%" scope="col" class="alin-cen">Descripción</th>
	      <th width="3%" scope="col" class="alin-cen">Cant</th>
          <th width="7%" scope="col" class="alin-cen">Precio</th>
          <th width="10%" scope="col" class="alin-cen">SubTotal</th>
          <th width="7%" scope="col" class="alin-cen">Descuento</th>
		  <th width="10%" scope="col" class="alin-cen">Total</th>
		  <th width="3%" scope="col"></th> <!-- Ver Relacionados -->
		  <th width="3%" scope="col"></th> <!-- Borrar Item -->
	    </thead>
	    <tbody>
	    @foreach ($carrito as $item)
	    <tr>

			<td class="alin-cen">{{$item->codigo_interno}}</td>
	      <td>{{$item->descripcion}}</td>
	      <td class="alin-cen">{{$item->cantidad}}</td>
	      <td class="alin-der">{{number_format($item->pu,0,',','.')}}</td>
	      <td class="alin-der">{{number_format($item->subtotal_item,0,',','.')}}</td>
          <td class="alin-der">{{number_format($item->descuento_item,0,',','.')}}</td>
          <td class="alin-der">{{number_format($item->total_item,0,',','.')}}</td>
      <!--
		  <td class="alin-cen"><a href="javascript:void(0);" onclick="ver_relacionados({{$item->id_repuestos}})"><abbr title="Repuestos Relacionados">R</abbr></a> </td>
        -->
        <td>R</td>
          <td class="alin-cen"><a href="javascript:void(0);" onclick="borrar_item_carrito({{$item->id}})"><abbr title="Borrar Item"><b style="color:red">X</b></abbr></span></a></td>
	    </tr>
	    @endforeach
		</tbody>
	  </table>
    </div>
</div>

<div class="row mt-3" id="datos_carrito_actual">
    <div class="col-sm-8">
        <input type="hidden" id="items_carrito" value="{{$carrito->count()}}">
        <input type="hidden" id="total_carrito" value="{{$total}}">
        Total Items: {{$carrito->count()}}
    </div>
    <div class="col-sm-4" style="color:blue"><b>Total Venta: {{number_format($total,0,',','.')}}</b></div>
</div>

@else
<script></script>
@foreach($cts as $item)
@if($item->estado === 1)
<div class="alert alert-danger" role="alert">
	Tiene un carrito pendiente de {{$item->name}} de nombre {{$item->nombre_carrito}}. <a href="#" onclick="dame_carrito_transferido()">Abrir carrito</a> 
  </div>
@endif
@endforeach
<div class="col-sm-12">
    <div class="row">
        <h4 class='alert alert-info text-center' id="msg_carrito_vacio">Carrito Vacio</h4><input type='hidden' id='items_carrito' value='0'>
        <input type="hidden" id="items_carrito" value="0">
    </div>
</div>

@endif



