@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Solicitudes 
			
			</h3>
			@include('travel.gasto.search')
		</div>		

	</div>
	@if(session()->has('msg'))
    	<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="alert alert-warning">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  					<strong>{{ session()->get('msg') }}</strong>
				</div>
			</div>
		</div>	
	@endif

	<div class="row">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">
					
					<thead>
						<th>No. Folio</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Fecha Salida</th>
						<th>Fecha Llegada</th>
						<th>Anticipo</th>
						<th>Comprobado</th>
						<th>Status</th>
					</thead>
					<?php 
						$date='';
						$mod_date=''; 
					?>
					@foreach ($folios as $fol)
					<tr>
						<?php  
						$date = date("Y-m-d");
						$mod_date = date('Y-m-d', strtotime("$fol->fecha_llegada + 7 day"));
						?>
						@if($fol->status == "7SR"||$fol->status == "4SR"||$fol->status == "5SR"||$fol->status == "6SR"||$fol->status == "CE" || $date <= $mod_date)
							<td>{{ $fol->folio }}</td>
							<td>{{ $fol->tipo }}</td>	
							<td>{{ $fol->destino }}</td>
							<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
							<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
							<td>{{ $fol->anticipo}} </td>
							<td>{{ $fol->all_total }} </td>
							<td>{{ $fol->descripcion }} {{ $fol->autorizador }}</td>
							<td>
								<!--<a href="#"><button disabled class="btn btn-info">Editar</button></a>-->
								@if ($fol->status=='3SA' || $fol->status=='4SR' || $fol->status=='5SR' || $fol->status=='6SR' || $fol->status=='CE'|| $fol->status=='7SR')
									<a href="{{URL::action('DetalleFolioController@show', $fol->folio)}}"><button class="btn btn-info">Captura</button></a>
								@else
									<a href="{{URL::action('DetalleFolioController@show', $fol->folio)}}"><button disabled class="btn btn-info">Captura</button></a>
								@endif

								@if ($fol->status=='3SA' || $fol->status=='4SR' || $fol->status=='5SR' || $fol->status=='6SR' || $fol->status=='CE'|| $fol->status=='7SR')
									<a href="{{URL::action('DetalleFolioController@confirm', $fol->folio)}}"><button class="btn btn-success">Confirmar</button></a>
								@else
									<a href="{{URL::action('DetalleFolioController@confirm', $fol->folio)}}"><button disabled class="btn btn-success">Confirmar</button></a>
								@endif

								<!--@if($fol->status=='C' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR')
									<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
								@else
									<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button disabled class="btn btn-danger">Eliminar</button></a>
								@endif
								-->
							</td>
						@else

							<td>{{ $fol->folio }}</td>
							<td>{{ $fol->tipo }}</td>	
							<td>{{ $fol->destino }}</td>
							<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
							<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
							<td>{{ $fol->anticipo}} </td>
							<td>{{ $fol->all_total }} </td>
							<td>{{ $fol->descripcion }} {{ $fol->autorizador }}</td>
							<td>
								<!--<a href="#"><button disabled class="btn btn-info">Editar</button></a>-->
							
									<a href="{{URL::action('DetalleFolioController@show', $fol->folio)}}"><button disabled class="btn btn-info">Captura</button></a>
							

							
									<a href="{{URL::action('DetalleFolioController@confirm', $fol->folio)}}"><button disabled class="btn btn-success">Confirmar</button></a>
						@endif
					</tr>
					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div>
@endsection