@if($repuestos->count()>0)
<div class="row">
	<div class="col-12 tabla-scroll-y-500">
	  <table id="tbl_repuestos" class="table table-sm table-hover">
	    <thead>
	      <th width="10%" scope="col">Cod Int</th>
		  <th width="10%" scope="col">Cod Proveedor</th>
		  <th width="20%" scope="col">Proveedor</th>
	      <th width="25%" scope="col">Descripción</th>
          <th width="10%" scope="col">Origen</th>
		  <th width="10%" scope="col">Precio Compra</th>
		  <th width="10%" scope="col">Precio Venta</th>
		  <th width="5%"></th> <!-- ELEGIR-->

	    </thead>
	    <tbody>
	    @foreach ($repuestos as $repuesto)
	    <tr style="font-stretch: condensed">
	      <td>{{$repuesto->codigo_interno}}</td>
		  <td>{{$repuesto->cod_repuesto_proveedor}}</td>
		  <td>{{$repuesto->empresa_nombre}}</td>
		  <td>{{$repuesto->descripcion}}</td>
	      <td>{{$repuesto->nombre_pais}}</td>
		  <td>{{$repuesto->precio_compra}}</td> <!-- Debería ser precio unitario (PU) en compras_det -->
		  <td>{{$repuesto->precio_venta}}</td>

			<td>
			<button class="btn btn-warning btn-sm" onclick="elegir_repuesto({{$repuesto}})">Elegir</button>
	      </td>

	    </tr>
	    @endforeach
		</tbody>
	  </table>
	</div>
</div>
@else

<div class="alert alert-info">
	<h4><center>Sin resultados.</center></h4>
</div>

@endif
