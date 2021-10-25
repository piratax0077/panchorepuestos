<div id="ccuenta" class="col-sm-12 tabla-scroll-y-400 mt-3" style="background-color: rgb(179, 248, 173)">
    <nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist" style="background-color:beige">
            <a class="nav-item nav-link active" id="nav-resumen-tab" data-toggle="tab" href="#tab-resumen" role="tab" aria-controls="nav-home" aria-selected="true">Resumen</a>
            <a class="nav-item nav-link" id="nav-cuenta-tab" data-toggle="tab" href="#tab-cuenta" role="tab" aria-controls="nav-profile" aria-selected="false">Cuenta</a>
            <a class="nav-item nav-link" id="nav-facturas-tab" data-toggle="tab" href="#tab-facturas" role="tab" aria-controls="nav-profile" aria-selected="false">Facturas Emitidas</a>
            <a class="nav-item nav-link" id="nav-boletas-tab" data-toggle="tab" href="#tab-boletas" role="tab" aria-controls="nav-contact" aria-selected="false">Boletas Emitidas</a>
        </div>
    </nav>
    <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
        <div class="tab-pane fade show active" id="tab-resumen" role="tabpanel" aria-labelledby="nav-resumen-tab">
            <strong>Total Pagos:</strong>&nbsp;{{number_format($total_pagos,0,',','.')}} <br> <strong>Total Deuda:</strong>&nbsp;{{number_format($total_deuda,0,',','.')}} <br> <strong>Diferencia:</strong>
            @if($diferencia>=0)
                        {{number_format($diferencia,0,',','.')}}
                    @else
                        {{number_format(abs($diferencia),0,',','.')}} A favor
                    @endif
            <br><br>
            <strong>Total Facturas Emitidas:</strong>&nbsp;{{number_format($facturas_suma,0,',','.')}}<br>
            <strong>Total Boletas Emitidas:</strong>&nbsp;{{number_format($boletas_suma,0,',','.')}}<br>
        </div>
        <div class="tab-pane fade" id="tab-cuenta" role="tabpanel" aria-labelledby="nav-cuenta-tab">
            <div id="ccuenta_cabecera" class="row">
                <div class="form-group">
                    <label for="pago">Pago:</label>
                    <input type="text" id="pago" value="0" maxlength="15" style="width:100px"><button class="btn btn-sm btn-success" onclick="agregar_cuenta(1)">Agregar</button>&nbsp;&nbsp;
                    <label for="deuda">Deuda:</label>
                    <input type="text" id="deuda" value="0" maxlength="15" style="width:100px"><button class="btn btn-sm btn-success" onclick="agregar_cuenta(2)">Agregar</button>&nbsp;&nbsp;
                    <label for="referencia">Referencia:</label>
                    <input type="text" id="referencia" maxlength="200" style="width:300px"><br>
                    <strong>Total Pagos:</strong>&nbsp;{{number_format($total_pagos,0,',','.')}} &nbsp;&nbsp; <strong>Total Deuda:</strong>&nbsp;{{number_format($total_deuda,0,',','.')}} &nbsp;&nbsp; <strong>Diferencia:</strong>&nbsp;
                    @if($diferencia>=0)
                        {{number_format($diferencia,0,',','.')}}
                    @else
                        {{number_format(abs($diferencia),0,',','.')}} A favor
                    @endif
                </div>
                @if($diferencia==0 && $cuenta->count()>0)
                    <div style="text-align:right;width:100%">
                        <button class="btn btn-danger btn-sm" onclick="limpiar_deuda_cero_clave()">Limpiar Deuda Cero</button>
                    </div>
                @endif
            </div> <!-- fin ccuenta_cabecera -->
            @if($cuenta->count()>0)
                <div id="ccuenta_detalle" class="row mt-2">
                    <table class="table table-sm table-hover">
                        <thead>
                            <th scope="col" width="10%">Fecha</th>
                            <th scope="col" width="20%">Pago</th>
                            <th scope="col" width="20%">Deuda</th>
                            <th scope="col" width="40%">Referencia</th>
                            <th scope="col" width="5%">Usuario</th>
                            <th scope="col" width="5%"></th> <!-- para borrar -->
                        </thead>
                        <tbody>
                            @foreach($cuenta as $i)
                                <tr>
                                    <td>{{$i->fecha_operacion}}</td>
                                    <td>{{number_format($i->pago,0,',','.')}}</td>
                                    <td>{{number_format($i->deuda,0,',','.')}}</td>
                                    <td>{{$i->referencia}}</td>
                                    <td>{{$i->usuario}}</td>
                                <td><button class="btn btn-danger btn-sm" onclick="borrar_cuenta_clave({{$i->id}})">X</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- fin ccuenta_detalle -->
            @else
                <h4>Sin datos en la cuenta</h4>
            @endif
        </div>
        <div class="tab-pane fade" id="tab-facturas" role="tabpanel" aria-labelledby="nav-facturas-tab">
            @if($facturas->count()>0)
                <table class="table table-bordered table-hover" style="width:50%">
                    <thead>
                        <th class="text-center" scope="col" style="width:100px">Fecha</th>
                        <th class="text-center" scope="col" style="width:50px">N°</th>
                        <th class="text-center" scope="col" style="width:80px">Total</th>
                    </thead>
                    <tbody>
                        @foreach($facturas as $factura)
                        <tr>
                            <td class="text-center">{{\Carbon\Carbon::parse($factura->fecha_emision)->format("d-m-Y")}}</td>
                            <td class="text-center">{{$factura->num_factura}}</td>
                            <td class="text-right">{{number_format($factura->total,0,',','.')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3 style="color:red">NO HAY FACTURAS EMITIDAS</h3>
            @endif
        </div>
        <div class="tab-pane fade" id="tab-boletas" role="tabpanel" aria-labelledby="nav-boletas-tab">
            @if($boletas->count()>0)
                <table class="table table-bordered table-hover" style="width:50%">
                    <thead>
                        <th class="text-center" scope="col" style="width:100px">Fecha</th>
                        <th class="text-center" scope="col" style="width:50px">N°</th>
                        <th class="text-center" scope="col" style="width:80px">Total</th>
                    </thead>
                    <tbody>
                        @foreach($boletas as $boleta)
                        <tr>
                            <td class="text-center">{{\Carbon\Carbon::parse($boleta->fecha_emision)->format("d-m-Y")}}</td>
                            <td class="text-center">{{$boleta->num_boleta}}</td>
                            <td class="text-right">{{number_format($boleta->total,0,',','.')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3 style="color:red">NO HAY BOLETAS EMITIDAS</h3>
            @endif
        </div>

    </div>

</div> <!-- fin ccuenta -->
