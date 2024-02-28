@extends ('layout.admin')
@section('contenido')
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>
			Folios con retraso de comprobacion
		</h3>	
	</div>		
</div>

<br>
<div class="row">		
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table id="datatable1" class="table table-striped table-bordered table-condensed table-hover">				
				<thead>
					<th>Folio</th>
					<th>Nombre </th>
                    <th>Status actual</th>                    
					<th>Fecha llegada viaje</th>
					<th>Fecha comprobacion realizada</th>
					<th>Dias retraso</th>
				</thead>
				@foreach ($folio as $fol)
				<tr>					
					<td>{{ $fol->id_header_folio }}</td>
					<td>{{ $fol->name }}</td>
                    <td>{{ $fol->Status }}</td>                    
					<td>{{ $fol->fecha_llegada }}</td>
					<td>{{ $fol->gasto }}</td>
					<td>{{ $fol->Retraso }}</td> 
				</tr>					
				@endforeach
			</table>
		</div>			
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="form-group">

			<a href="{{ url('reports/comprobacionesRetrasos')}}" class="btn btn-danger"><i class="fa fa-chevron-left" aria-hidden="true"></i> Atras</a>

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
		"fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
			return "Mostrando registros del " + iStart +" al "+ iEnd + " (de un total de "+ iTotal +")";
		},
		"pageLength": 15,
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
		filename: 'Reporte retraso por usuario ',
		
        },
		{
        extend: 'csvHtml5',
        title: null,
		className: 'btn btn-success',
		footer: true,
		filename: 'Reporte retraso por usuario ',
	
        }    
    ]
	 } );
} );
</script>
