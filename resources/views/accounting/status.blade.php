@extends ('layout.admin')
@section ('contenido')
<title>Reporte de gastos de viaje</title>
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Status de Folios Pendientes </h3>
	
	</div>
</div>
<div class="row">	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table id="datatable1" class="table table-striped table-bordered table-condensed table-hover">
				<thead>
					<tr>
						<th>Folio</th>
						<th>Nombre </th>
						<th>FECHA</th>
						<th>Status</th>
					</tr>
            	</thead>
				<tbody>
				@foreach($estatus as $estatu)
            	<tr>	
                	<td>{{$estatu->id_header_folio}}</td>
				  	<td>{{$estatu->NombreCompleto}}</td>
				  	<td>{{$estatu->fecha_salida}}</td>
					@if ($estatu->id_gasto>0)
				  		<td>{{$estatu->descripcion}} {{$estatu->NOMBREAUTO}} </td>
					@else
				  	<td>{{$estatu->descripcion}}</td>
				  	@endif

                </tr>
				@endforeach	
			</table>
		</div>
		<a href="{{ url('accounting/validados')}}" class="btn btn-danger">Atras</a>
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
		"order": [[ 3, "asc" ]],
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
		filename: 'Reporte de gastos de viaje ',
		
        },
		{
        extend: 'csvHtml5',
        title: null,
		className: 'btn btn-success',
		footer: true,
		filename: 'Reporte de gastos de viaje Estatus',
	
        }    
    ]
	 } );
} );
</script>
