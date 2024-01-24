@extends ('layout.admin')
@section ('contenido')
<title>Reporte de gastos de viaje: {{$folio->id_header_folio}} </title>
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Reporte de gastos de viaje: {{$folio->id_header_folio}} </h3>
	
	</div>
</div>
<div class="row">	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table id="datatable" class="table table-striped table-bordered table-condensed table-hover">
				<thead>
                <tr>
                	<th>CUENTA</th>
					<th>   </th>
                	<th>DEBE</th>
                	<th>HABER</th>
					<th>   </th>
					<th>   </th>
					<th>RFC</th>
                	<th>PROVEEDOR</th>  
					<th>CONCEPTO</th> 
					<th>FACTURA</th>  
					<th>UUID</th> 
                </tr>
            	</thead>
				<tfoot>
					<th></th>			
					<th></th>
					<th><h4 id="total">{{number_format($suma->total,2)}}</h4><input type="hidden" name="total_venta" id="total_venta"></th>
					<th><h4 id="total"></h4><input type="hidden" name="total_venta" id="total_venta"></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tfoot>
				<tbody>
				@foreach($reporte as $reporte)
            	<tr>	
                	<td>{{$reporte->Cuenta}}</td>
				  	<td>   </td>
				  	<td>{{$reporte->debe}}</td>
				  	<td></td>
				  	<td>   </td>
				  	<td>   </td>
				  	<td>{{$reporte->RFC}}</td>
				  	<td>{{$reporte->proveedor}}</td>
					<td>{{$reporte->gasto}}</td>
					<td>{{$reporte->noFactura}}</td>
					<td>{{$reporte->UUID}}</td>
                </tr>
				@endforeach	
			</table>
		</div>
		<a href="{{ url('accounting/comprobacion')}}" class="btn btn-danger">Atras</a>
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
		"order": [[ 7, "asc" ]],
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
		filename: 'Reporte de gastos de viaje '+ '{{$folio->id_header_folio}}',
		customize: function( xlsx ) {
			var sheet = xlsx.xl.worksheets['sheet1.xml'];
    		var col = $('col', sheet);
    		col.each(function () {
				$(col[1]).attr('width', 1.8	);
				$(col[4]).attr('width', 1.8);
				$(col[5]).attr('width', 1.8);
   			});
            }
        },
		{
        extend: 'csvHtml5',
        title: null,
		className: 'btn btn-success',
		footer: true,
		filename: 'Reporte de gastos de viaje '+ '{{$folio->id_header_folio}}',
	
        }    
    ]
	 } );
} );
</script>
