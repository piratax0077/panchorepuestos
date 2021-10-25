<div class="col-11 tabla-scroll-y-300">
<p><strong>Lista: ({{$totalfacturas}})</strong></p>
	@if($facturas->count()>0)
	<table class="table">
	  	<thead>
	    	<tr>
	      		<th scope="col">Fecha</th>
	      		<th scope="col">Número</th>
	    	</tr>
	  	</thead>
	  	<tbody>
	  	@foreach($facturas as $factura)
	      <tr>
	        <td>{{$factura->factura_fecha}}</td>
	        <td><a href="javascript:void(0);" onclick="dameFactura({{$factura->id}})">{{$factura->factura_numero}}</a></td>
	      </tr>
	  	@endforeach
		</tbody>
	</table>
	@else
		<p>No hay Facturas del Proveedor.</p>
	@endif
</div>
