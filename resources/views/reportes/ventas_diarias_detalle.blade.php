@if($doc=='bo') <!-- boletas -->
    <h5>BOLETAS {{strtoupper($quien)}}</h5>
@else <!-- facturas -->
    <h5>FACTURAS {{strtoupper($quien)}}</h5>
@endif
<table class="table table-sm table-hover table-bordered">
    <thead>
        <th class="text-center">HORA</th>
        <th class="text-center">NUM</th>
        <th class="text-right">MONTO</th>
        <th class="text-center">PAGO</th>
    </thead>
    <tbody>
        @php $anterior=0; $actual=0; $c=0;
            $blanco="<tr style='background-color:white'>";
            $naranja="<tr style='background-color:rgb(255, 204, 109)'>";
            $color=$blanco;
        @endphp
        @foreach($docus as $docu)
            @php
                $actual=$docu->num_doc;
                $actual_color=$color;
                if($actual!=$anterior){
                    if($actual_color==$blanco) $color=$naranja;
                    if($actual_color==$naranja) $color=$blanco;
                }
                echo $color;
                $anterior=$actual;
                $anterior_color=$actual_color;
            @endphp
                <td class="text-center">
                    {{date('H:i',strtotime($docu->fecha_doc))}}
                </td>
                <td class="text-center">
                    <a href="javascript:imprimir_xml('{{$docu->url}}')">{{$docu->num_doc}}</a>
                </td>
                <td class="text-right">{{intval($docu->monto)}}</td>
                <td class="text-center">{{$docu->formapago}}</td>
            </tr>

        @endforeach
    </tbody>
</table>
