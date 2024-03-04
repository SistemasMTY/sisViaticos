@extends ('layout.admin')
@section('contenido')
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>
				Listado de Folios Validados
			</h3>
			
		</div>		
	</div>

	@include('accounting.validados.search')
	<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12">
		<div>
			<label></label>
			<span>
				<a href="{{url('accounting/reportetotal')}}"><button class="btn btn-warning"><i class="fa  fa-file-excel-o" aria-hidden="true"></i> Reporte de gastos </button></a>										
			</span>
		</div>
		</br>
	</div>


	<div class="row">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

			<div class="table-responsive">
				<table id="datatable1" class="table table-striped table-bordered table-condensed table-hover">					
					<thead>
						<th>No. Folio</th>
						<th>BRH</th>
						<th>Fecha Salida</th>
						<th>Fecha Llegada</th>
						<th>Nombre</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Total Gastado</th>
						<th>Status</th>
						<th></th>
					</thead>
					@foreach ($folios as $fol)
					<tr>
						<td>{{ $fol->folio }}</td>
						<td>{{ $fol->company }}</td>
						<!--<td>{{ date('d-m-Y', strtotime($fol->fecha)) }}</td>-->
						<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
						<td>{{ $fol->name }}</td>
						<td>{{ $fol->tipo }}</td>	
						<td>{{ $fol->destino }}</td>
						<td>{{ $fol->all_total}} </td>
						<td>{{ $fol->descripcion }}</td>
						<td>
							<!--<a href="#"><button disabled class="btn btn-info">Editar</button></a>-->
							@if ($fol->status=='E')
								<a href="{{URL::action('CuentasXPagarValidadosController@show', $fol->folio)}}"><button class="btn btn-success"> <i class="fa fa-search" aria-hidden="true"></i> Ver</button></a>
								<a href="{{URL::action('CuentasXPagarController@edit', $fol->folio)}} " target="_blank"><button class="btn btn-info"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button></a>
								<a href="{{URL::action('CuentasXPagarValidadosController@report', $fol->folio)}}"><button class="btn btn-warning"><i class="fa  fa-file-excel-o" aria-hidden="true"></i> Reporte </button></a>
							@else
								<a href="{{URL::action('CuentasXPagarValidadosController@show', $fol->folio)}}"><button class="btn btn-success"> <i class="fa fa-search" aria-hidden="true"></i> Ver</button></a>
								<a href="{{URL::action('CuentasXPagarController@edit', $fol->folio)}}"><button class="btn btn-info"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button></a>	
								<a href="{{URL::action('CuentasXPagarValidadosController@report', $fol->folio)}}"><button class="btn btn-warning"><i class="fa  fa-file-excel-o" aria-hidden="true"></i> Reporte </button></a>					
							@endif

						</td>
					</tr>					
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
