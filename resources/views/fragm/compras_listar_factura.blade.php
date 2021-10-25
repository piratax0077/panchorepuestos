<div class="col-12">

  <div class="row" id="opciones">
    <div class="btn-toolbar">
      <div class="btn-group btn-group-md" role="group">
        <button class="btn btn-success" type="button">Imprimir</button>
      </div>
      <div class="btn-group btn-group-md" role="group">
        <button class="btn btn-danger" type="button">Eliminar</button>
      </div>
      <div class="btn-group btn-group-md" role="group">
        <button class="btn btn-secondary" type="button">Opcion 3</button>
        <button class="btn btn-secondary" type="button">Opción 4</button>
      </div>
    </div> <!-- btn-toolbar -->
  </div>

  <div class="row">
    <div class="col-12" style="margin:0px">
        <table  class="table table-borderless table-responsive-sm table-sm">
            <tbody>
                <tr>
                    <td><strong>Fact. Número:</strong> {{$cabecera['factura_numero']}}</td>
                    <td><strong>Fecha: </strong>{{\Carbon\Carbon::parse($cabecera['factura_fecha'])->format("d-m-Y")}}</td>
                    <td><strong>Total:</strong> {{number_format($cabecera['factura_total'],2,',','.')}}</td>
                    <td><strong>Flete:</strong> {{number_format($suma_flete,2,',','.')}}</td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>SubTotal:</strong> {{number_format($cabecera['factura_subtotal'],2,',','.')}} </td>
                    <td><strong>IVA:</strong> {{number_format($cabecera['factura_iva'],2,',','.')}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
  </div>
  <div class="row">
    <div class="col-12 tabla-scroll-y-400">
      <table class="table table-hover table-bordered table-sm">
        <thead>
          <tr>

            <th scope="col" width="10%" class="text-center">Cod. Proveedor</th>
            <th scope="col" width="30%" class="text-center">Descripción</th>
            <th scope="col" width="4%" class="text-center">Cant</th>
            <th scope="col" width="9%" class="text-center"><abbr title="Precio Unitario">P.U.</abbr></th>
            <th scope="col" width="9%" class="text-center"><abbr title="P.U. + utilidad + iva">Costo</abbr></th>
            <th scope="col" width="8%" class="text-center"><abbr title="Flete Unidad: Precio Venta - Costo">F.U.</abbr></th>
            <th scope="col" width="8%" class="text-center"><abbr title="Flete Total: Cant x F.U.">F.T.</abbr></th>
            <th scope="col" width="9%" class="text-center"><abbr title="Cant x P.U.">Subtotal</abbr></th>
            <th scope="col" width="9%" class="text-center">Precio Venta</th>
            <th scope="col" width="4%"></th> <!-- ELIMINAR ITEM -->
          </tr>
        </thead>
        <tbody>
@foreach($items as $item)
          <tr>
            <td class="text-center letra-chica">{{$item->cod_repuesto_proveedor}}</td>
            <td class="letra-chica">{{$item->descripcion}}</td>
            <td class="text-center letra-chica">{{$item->cantidad}}</td>
            <td class="text-right letra-chica">{{number_format($item->pu,2,',','.')}}</td>
            <td class="text-right letra-chica">{{number_format($item->precio_sugerido-$item->flete,2,',','.')}}</td>
            <td class="text-right letra-chica">{{number_format($item->flete,2,',','.')}}</td>
            <td class="text-right letra-chica">{{number_format($item->cantidad * $item->flete,2,',','.')}}</td>
            <td class="text-right letra-chica">{{number_format($item->subtotal,2,',','.')}}</td>
            <td class="text-right letra-chica">{{number_format($item->precio_sugerido,2,',','.')}}</td>
            <td class="text-center">X</td>
          </tr>
@endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>





