@extends ('layout.admin')
@section('contenido')
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>
			Listado de Folios Mensual
		</h3>	
	</div>		
</div>

@include('reports.reportes.search')

<br>
<!-- {{$FechaI}}
{{$FechaF}} -->
<div class="row">		
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table id="datatable1" class="table table-striped table-bordered table-condensed table-hover">				
				<thead>
					<th></th>
					<th>Status</th>
					<th>Nombre </th>
					<th>003 Viatico Entregado al Trabajador - Abierto</th>
					<th>Suma de Complemento de Anticipo</th>
					<th>003 Viatido Entregado al Trabajador - Cerrado</th>
					<th>50 Viatico - Importe Exento</th>
					<th>50 Viatico - Importe Gravado</th>
				</thead>
				@foreach ($folio as $fol)
				<tr>
					<td><a href="{{ route('ReporteMensuUsuario', ['FechaI' => $FechaI,'FechaF' => $FechaF,'id' => $fol->NombreCompleto,'status' => $fol->Status]) }}"><button class="btn btn-success"> <i class="fa fa-search"></i></button></a></td>
					<td>{{ $fol->Status }}</td>
					<td>{{ $fol->NombreCompleto }}</td>
					<td>{{ $fol->ViaticoEntregadoAbierto }}</td>
					<td>{{ $fol->ComplementoAnticipo }}</td>
					<td>{{ $fol->ViaticoEntregadoCerrado }}</td>
					<td>{{ $fol->TotalExcento }}</td>	
					<td>{{ $fol->Ttotalgravado }}</td>
				</tr>					
				@endforeach
			</table>
		</div>			
	</div>
</div>
@endsection
<!--SCRIPTS PARA BOTONES -->

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
	
		dom: 'Bfrtip',
		bAutoWidth: false,
		// "order": [[ 3, "asc" ]],
		lengthChange: false,
		"searching": false,
		"info": false,
		"language": {
    	"paginate": {
      		"next": ">",
			"previous": "<"
    	}
  	},
    buttons: [
		{
        extend: 'excelHtml5',
		className: 'btn btn-success',
        title: null,
		footer: true,
		autoFilter: false,
		extension: '.xlsx',
		filename: 'Reporte mensual de Gastos ',
		
        },
		{
        extend: 'csvHtml5',
        title: null,
		className: 'btn btn-success',
		footer: true,
		filename: 'Reporte mensual de Gastos ',
	
        }    
    ]
	 } );
} );
</script>
