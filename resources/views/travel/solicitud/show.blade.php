@extends ('layout.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
  			<div class="form-group">
  				<label for="fecha">Fecha:</label>
  				<p>{{ date('d-m-Y', strtotime($folio->fecha)) }}</p>
  			</div>
  		</div>
  		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
        	<div class="form-group">
          		<label for="tipo">TIPO / TYPE </label>
          			<p>{{$folio->tipo}}</p>
        	</div>
      	</div>
  		<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        	<div class="form-group">
          		<label for="name">NOMBRE COMPLETO / FULL NAME</label>
          			<p>{{$folio->name}}</p>
        	</div>
      	</div>
	    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
	        <div class="form-group">
	         	<label for="destino">DESTINO / DESTINATION</label>
	         	<p>{{$folio->destino}}</p>
	        </div>
	    </div>
	     <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
	        <div class="form-group">
	         	<label for="proposito">PROPOSITO / PURPOSES</label>
	         	<p>{{$folio->proposito}}</p>
	        </div>
	    </div>
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
	        <div class="form-group">
	         	<label for="eq_computo">EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</label>
	         	<p>{{$folio->eq_computo}}</p>
	        </div>
	    </div>
	    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
	        <div class="form-group">
	         	<label for="periodo">PERIODO / PERIOD</label>
	        </div>
	    </div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
	        <div class="form-group">
	         	<label for="fecha_salida">DE / FROM</label>
	         	<p>{{ date('d-m-Y', strtotime($folio->fecha_salida)) }}</p>
	        </div>
	    </div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
	        <div class="form-group">
	         	<label for="fecha_salida">A / TO</label>
	         	<p>{{date('d-m-Y', strtotime($folio->fecha_llegada))}}</p>
	        </div>
	    </div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
	        <div class="form-group">
	         	<label for="fecha_salida">DIAS / DAYS</label>
	         	<p>{{$folio->dias}}</p>
	        </div>
	    </div>
	    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
	        <div class="form-group">
	         	<label for="fecha_salida">DETALLES DEL VUELO/ FLIGHT DETAILS</label>
	         	<p>{{$folio->criterio}}</p>
	        </div>
	    </div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
        	<div class="form-group">
          		<label for="id_moneda">ANTICIPO DE VIAJE / TRIP ADVANCE</label>
          		<p>{{$folio->moneda}}</p>
        	</div>
      	</div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	        <div class="form-group">
	        	<label for="anticipo">ANTICIPO / ADVANCE PAYMENT</label>
	          	<p>{{$folio->anticipo}}</p>
	        </div>
	    </div>
	    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" >
	    	<div class="form-group">            	
				<button id="enviar" name="enviar" type="button" class="btn btn-primary">Enviar</button>
            	<a href="{{url('travel/solicitud')}}"><button class="btn btn-danger">Cancelar</button></a>
        	</div>
        	
        </div>
	</div>

@push('scripts')
<script>
	 
	$(document).ready(function () {
		$('#enviar').click(function() {
			Swal.fire({
				position: 'center',
				title: 'Enviando Solicitud...',
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
					type: "POST",
					url: "http://170.1.1.253:8012/api/v5_1/NewReviewFolio",
					contentType: 'application/json',
					data: JSON.stringify({                
						'id' : "{{$folio->id_header_folio}}",
						'id_user': "{{$folio->id_solicitante}}",
						'token': "{{$folio->_token}}",				
					}),
					dataType: "json",
						success: function(data){
							console.log(data);
							if ((data.errors))
							{
								Swal.fire({
								icon: 'error',
								customClass: {
									popup: 'swal-wide',
									title: 'swal-title'
								},
								title: 'Ha ocurrido un error mientras se enviaba el folio a autorizacion',
								})

							}
							else{
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: 'Se ha enviado el correo al autorizador',
									showConfirmButton: false,
									customClass: {
										popup: 'swal-wide',
										title: 'swal-title'
									},
									timer: 3500
								});						
							}
							//Se redirecciona el portal al inicio de Travel 
							window.location.replace("{{url('travel/solicitud')}}");
						}
					});
				}
			});	
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
@endpush
@endsection