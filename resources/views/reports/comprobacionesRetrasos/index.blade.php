@extends ('layout.admin')
@section('contenido')
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
		<h3>
		Usuarios con folios con retraso de comprobacion
		</h3>	
	</div>	
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
	@include('reports.comprobacionesRetrasos.search')
    </div>	
</div>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
		<button id="descargarReporte" name="descargarReporte" type="button" class="btn btn-success">Descargar Excel</button>
	</div>
</div>



<br>
<div class="row">		
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table id="datatable1" class="table table-striped table-bordered table-condensed table-hover">				
				<thead>
					<th></th>
					<th>Compañia</th>
					<th>Nombre </th>
					<th>Departamento</th>
					<th>Division</th>
					<th>Folios Retraso</th>
					<th>Año</th>
				</thead>
				@foreach ($folio as $fol)
				<tr>
					<td><a href="{{ route('ReportePorUsuario', ['id' => $fol->name]) }}"><button class="btn btn-success"> <i class="fa fa-search"></i></button></a></td>
					<td>{{ $fol->company }}</td>
					<td>{{ $fol->name }}</td>
					<td>{{ $fol->depto }}</td>
					<td>{{ $fol->division }}</td>
					<td>{{ $fol->foliosRetraso }}</td>
					<td>{{ $fol->Año_fecha }}</td>
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
	$('#descargarReporte').click(function() {
		Swal.fire({
			position: 'center',
			title: 'Descargando Reporte...',
			customClass: {
				popup: 'swal-wide',
				title: 'swal-title',
				loader: 'swal-loader',
			},
			allowEscapeKey: false,
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
				$.ajax({
				type: "GET",
				url: "http://170.1.1.253:8012/api/v5_1/Download_Reporte_Excel",
				contentType: 'application/json',

				dataType: "json",
					success: function(data){
						// console.log(data);
						if ((data.errors))
						{
							Swal.fire({
							icon: 'error',
							customClass: {
								popup: 'swal-wide',
								title: 'swal-title'
							},
							title: 'Ha ocurrido un error mientras se descargaba el reporte',
							})

						}
						else{			
							window.open(data.XLSXurl, "_blank") 				
							console.log(data.XLSXurl);
							Swal.fire({
								position: 'center',
								icon: 'success',
								title: 'Reporte Descargado',
								showConfirmButton: false,
								customClass: {
									popup: 'swal-wide',
									title: 'swal-title'
								},
								timer: 3500
							});						
						}						
					}
				});
			}
		});	
    });
    $('#datatable1').DataTable( {
		// Configure Export Buttons
	
		// dom: 'Bfrtip',
		bAutoWidth: false,
		// "order": [[ 3, "asc" ]],
		
		"fnInfoCallback": function( oSettings, iStart, iEnd, iMax, iTotal, sPre ) {
			return "Mostrando registros del " + iStart +" al "+ iEnd + " (filtrado de un total de "+ iTotal +")";
		},
		"pageLength": 15,
		"language": {
			"paginate": {
				"next": ">",
				"previous": "<"
			}
  		}
    // buttons: [
	// 	{
    //     extend: 'excelHtml5',
	// 	className: 'btn btn-success',
    //     title: null,
	// 	footer: true,
	// 	autoFilter: false,
	// 	extension: '.xlsx',
	// 	filename: 'Reporte general de retrasos ',
		
    //     },
	// 	{
    //     extend: 'csvHtml5',
    //     title: null,
	// 	className: 'btn btn-success',
	// 	footer: true,
	// 	filename: 'Reporte general de retrasos ',
	
    //     }    
    // ]
	});
});
</script>
<style lang="scss">
   .swal-wide{
    	width:450px !important;
		height:200px !important;
	}

	.swal-title {
		margin: 0px;
		font-size: 30px;
		margin-bottom: 28px;
	}

	.swal-loader {
      grid-column: 10;
      grid-row: 7/99;
      align-self: center;
      width: 8em;
      height: 8em;
      margin: 0.25em;
    }
</style>