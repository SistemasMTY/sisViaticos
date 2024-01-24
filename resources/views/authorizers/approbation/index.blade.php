@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>
				Folio Pendientes de Autorizacion
			</h3>
			
		</div>		

	</div>
	@if(session()->has('msgOK'))
    	<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  					<strong>{{ session()->get('msgOK') }}</strong>
				</div>
			</div>
		</div>	
	@endif
	@if(session()->has('msgDenegado'))
    	<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="alert alert-warning">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  					<strong>{{ session()->get('msgDenegado') }}</strong>
				</div>
			</div>
		</div>	
	@endif

	<div class="tabbable"> <!-- Only required for left/right tabs -->
	  <ul class="nav nav-pills">
	    <li class="active"><a href="#tab1" data-toggle="tab">Solicitudes de Anticipo <span class="badge">{{count($foliosAnti)}}</span></a></li>
	    <li><a href="#tab2" data-toggle="tab">Comprobaciones de Gasto <span class="badge">{{count($foliosGasto)}}</span></a></li>
	  </ul>
	  <div class="tab-content">
	    <div class="tab-pane active" id="tab1">
	      	<div class="row">
		
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed table-hover">
							
							<thead>
								<th>No. Folio</th>
								<th>BRH</th>
								<th>Nombre</th>
								<th>Tipo</th>
								<th>Fechas del Viaje</th>
								<th>Anticipo</th>
							</thead>
							@foreach ($foliosAnti as $fol)
							<tr>
								<td>{{ $fol->id_header_folio }}</td>
								<td>{{ $fol->company }}</td>
								<td>{{ $fol->name }}</td>
								<td>{{ $fol->tipo }}</td>	
								<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }} al {{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
								<td>{{ $fol->anticipo }}</td>
								<td>
									<a href="{{ URL::action('AutorizadoresController@show', $fol->id_header_folio)}}"><button class="btn btn-primary"><i class="fa fa-check-circle"></i>  Autorizar</button></a>
									<a href="{{URL::action('AutorizadoresController@edit', $fol->id_header_folio)}}" target="_blank"><button class="btn btn-success"><i class="fa fa-file-pdf-o"></i>  Ver</button></a>
								</td>
							</tr>
							
							@endforeach
						</table>
					</div>
					
				</div>
			</div>
	    </div>
	    <div class="tab-pane" id="tab2">
	      <div class="row">
		
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed table-hover">
							
							<thead>
								<th>No. Folio</th>
								<th>BRH</th>
								<th>Nombre</th>
								<th>Tipo</th>
								<th>Fechas del Viaje</th>
								<th>Anticipo</th>
								<th>Comprobado</th>
							</thead>
							@foreach ($foliosGasto as $fol)
							<tr>
								<td>{{ $fol->id_header_folio }}</td>
								<td>{{ $fol->company }}</td>
								<td>{{ $fol->name }}</td>
								<td>{{ $fol->tipo }}</td>	
								<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }} al {{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
								<td>{{ $fol->anticipo }}</td>
								<td>{{ $fol->all_total }}</td>
								<td>
									<a href="{{ URL::action('AutorizadoresController@show', $fol->id_header_folio)}}"><button class="btn btn-primary"><i class="fa fa-check-circle"></i>  Autorizar</button></a>
									<a href="{{URL::action('AutorizadoresController@edit', $fol->id_header_folio)}}" target="_blank"><button class="btn btn-success"><i class="fa fa-file-pdf-o"></i>  Ver</button></a>
								</td>
							</tr>
							
							@endforeach
						</table>
					</div>
					
				</div>
			</div>
	    </div>
	  </div>
	</div>
	
@endsection