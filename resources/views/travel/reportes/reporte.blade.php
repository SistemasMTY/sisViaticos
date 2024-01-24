@extends ('layout.admin')
@section ('contenido')
<title>Reporte de viajes</title>
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Reporte de viajes</h3>
        
	
	</div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
        @include('travel.reportes.search')
    </div>
</div>
<div class="row">	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table id="datatable" class="table table-striped table-bordered table-condensed table-hover">
				<thead>
                <tr>
                	<th>FECHA</th>
					<th>NOMBRE</th>
                	<th>DESTINO</th>
                	<th>PROPOSITO</th>
					<th>ANTICIPO</th>
					<th>DEVOLUCION</th>
                </tr>
            	</thead>
				<tbody>
                @foreach($folio as $folio)
            	<tr>	
                	<td>{{$folio->fecha_salida}}</td>
				  	<td>{{$folio->nombre}}</td>
				  	<td>{{$folio->destino}}</td>
				  	<td>{{$folio->proposito}}</td>
				  	<td>{{$folio->anticipo}}</td>
				  	<td>{{$folio->Devolucion}}</td>
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
	
    $('#datatable').DataTable( {
		// Configure Export Buttons
	
		dom: 'Bfrtip',
		bAutoWidth: false,
		"order": [[ 0, "asc" ]],
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
		customize: function( xlsx ) {
			var sheet = xlsx.xl.worksheets['sheet1.xml'];
    		var col = $('col', sheet);
        },
		
	
        }    
    ]
	 } );
} );
</script>
