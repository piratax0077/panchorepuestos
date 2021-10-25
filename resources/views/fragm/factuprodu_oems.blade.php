<div class="col-12 tabla-scroll-y-300">
	<p><strong>Lista: {{$oems->count()}}</strong></p>
	@if($oems->count()>0)
	<table class="table table-sm">
	  	<tbody>
	  	@foreach($oems as $oem)
	      <tr>
			<td>{{$oem->codigo_oem}}</td>
			<td>
				<abbr title="oems {{$oem->id}}">
					<button class="btn btn-danger btn-sm" style="line-height:10px" onclick="borraroem({{$oem->id}})">X</button>
				</abbr>
			</td>
	      </tr>
	  	@endforeach
		</tbody>
	</table>
	@else
		<p>No hay OEMs agregados...</p>
	@endif
</div>
