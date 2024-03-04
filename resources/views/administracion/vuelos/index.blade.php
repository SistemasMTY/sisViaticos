@extends ('layout.admin')
@section('contenido')
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Solicitudes pendientes de facturas de vuelo</h3>
			@include('administracion.vuelos.search')
		</div>				
	</div>
	<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
		@if(session()->has('msg'))    		
			<div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 alert alert-success" role="alert" >
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  			<strong>{{ session()->get('msg') }}</strong>
			</div>
		@endif
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="table-responsive">
				<table id="datatable1" class="table table-striped table-bordered table-condensed table-hover">					
					<thead>
						<th>No. Folio</th>
						<th>Fecha Salida</th>
						<th>Fecha Llegada</th>
						<th>Nombre</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Criterio</th>
						<th></th>
					</thead>
					@foreach ($folios as $fol)
					<tr>
						<td>{{ $fol->folio }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
						<td>{{ $fol->name }}</td>
						<td>{{ $fol->tipo }}</td>	
						<td>{{ $fol->destino }}</td>
						<td>{{ $fol->criterio }}</td>
						<td>
							<a href="{{URL::action('FlightsController@show', $fol->folio)}}"><button class="btn btn-success">Adjuntar factura</button></a>

							<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-danger">Descartar solicitud</button></a>								
						</td>
					</tr>					
					@include('administracion.vuelos.modal')					
					@endforeach
				</table>
			</div>
		</div>
	</div>
@endsection
<script src="{{asset('DataTables\jQuery-3.3.1\jquery-3.3.1.js')}}"></script>
<script src="{{asset('DataTables\DataTables-1.10.18\js\jquery.dataTables.min.js')}}"></script>
<script src="{{asset('DataTables\Buttons-1.5.6\js\dataTables.buttons.min.js')}}"></script>
<script src="{{asset('DataTables\Buttons-1.5.6\js\buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('DataTables\DataTables-1.10.18\js\dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('DataTables\JSZip-2.5.0\jszip.min.js')}}"></script>
<script src="{{asset('DataTables\pdfmake-0.1.36\pdfmake.min.js')}}"></script>
<script src="{{asset('DataTables\pdfmake-0.1.36\vfs_fonts.js')}}"></script>
<script src="{{asset('DataTables\Buttons-1.5.6\js\buttons.html5.min.js')}}"></script>

<script>

	$(document).ready(function() {
		var docDate = $('#folio');
		
		$('#datatable1').DataTable( {
			// Configure Export Buttons
			bAutoWidth: false,
			lengthChange: false,
			"searching": false,
			"info": false,
			"language": {
				"paginate": {
					"next": ">",
					"previous": "<"
				}
			}
		} );
	});
</script>

