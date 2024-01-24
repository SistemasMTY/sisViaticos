<!DOCTYPE html>
<html>
<head>
	<title>Aplication and report for business trip - {{$folio->id_header_folio}}</title>
</head>
<body>

	<table border="1" width="700px">
		<td>
			<table border="">
				@if($profile->id_user_profile==536){
					<td width="205px"><img src="img/servilamina.jpg" height="40px" width="204px"></td>
				}@endif

				@if($profile->company=='SLM'){
					<td width="205px"><img src="img/SummitSlm.jpg" height="40px" width="204px"></td>
				}@endif

				@if($profile->company=='QRO' && $profile->id_user_profile!=536 || $profile->company=='MTY' && $profile->id_user_profile!=536){
					<td width="205px"><img src="img/Servicios.png" height="40px" width="204px"></td>
				}@endif
				<td width="350px" align="center"><strong>Aplication and report for business trip<br>Solicitud y reporte de viaje</strong></td>
				<td width="55px"> </td>
				<td width="75px" style="border: solid 1px; font-size: 12px" align="center">No. Folio <br><strong>{{$folio->id_header_folio}}</strong></td>
			</table>
			<table  width="100%">
				<td height="5px"></td>
			</table>
			<table width="100%">
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse">
							<tr>
								<th colspan="2" style="font-size:11px" align="center" valign="center" height="20px">Solicitante - Applicant</th>
							</tr>

								<tr>
									@if(count($UserAnticipo)==0)
										<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top">  </td>	
									@else
										@foreach ($UserAnticipo as $anticipo)
										<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$anticipo->name}}<br>{{$anticipo->anticipo}}</strong></td>
										
										@endforeach
									@endif

									@if(count($UserGasto)==0)
										<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"> </td>
									@else
										@foreach ($UserGasto as $gasto)
											<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$gasto->name}}<br>{{$gasto->gasto}}</strong></td>
										@endforeach	
									@endif
								</tr>
							<tr>
								<th colspan="2" style="font-size:9px" align="center" valign="middle" height="20px">{{$folio->name}} </th>
							</tr>
						</table>
					</td>
					<td>
						<table border="" style="border-collapse: collapse">
							<tr>
								<th></th>
							</tr>
							<tr>
								<td width="20px" height="60px"><img src="https://png.pngtree.com/element_origin_min_pic/16/10/28/115d7ed11cf4e4f5b8ccdec2d42cbb8b.jpg" width="19pz" height="45px"></td>
							</tr>
							<tr>
								<th></th>
							</tr>
						</table>
					</td>
					<td>
						<?php 
						$auto1='';
						$auto2='';
						$auto3='';	 ?>
						<table border="1" style="border-collapse: collapse">
							<tr>
								<th colspan="2" style="font-size:10px" align="center" valign="center" height="20px">Jefe o Sub Gerente o Gerente</th>
							</tr>
							<tr>
								@if(count($Auto1Anticipo)==0)
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top">  </td>	
								@else
									@foreach ($Auto1Anticipo as $anticipo)
									{{$auto1 = $anticipo->autorizador}}
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$anticipo->autorizador}}<br>{{$anticipo->anticipo}}</strong></td>
									
									@endforeach
								@endif

								@if(count($Auto1Gasto)==0)
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"> </td>
								@else
									@foreach ($Auto1Gasto as $gasto)
										<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$gasto->autorizador}}<br>{{$gasto->gasto}}</strong></td>
									@endforeach	
								@endif
							</tr>
							
								@if($auto1=='')
									<tr>
										<th colspan="2" style="font-size:10px" align="center" valign="top" height="20px">  </th>
									</tr>
								@else
									<tr>
										<th colspan="2" style="font-size:10px" align="center" valign="top" height="20px">{{$auto1}}</th>
									</tr>	
								@endif
						</table>
					</td>
					<td>
						<table border="" style="border-collapse: collapse">
							<tr>
								<th></th>
							</tr>
							<tr>
								<td width="20px" height="60px"><img src="https://png.pngtree.com/element_origin_min_pic/16/10/28/115d7ed11cf4e4f5b8ccdec2d42cbb8b.jpg" width="19pz" height="45px"></td>
							</tr>
							<tr>
								<th></th>
							</tr>
						</table>
					</td>
					<td>
						<table border="1" style="border-collapse: collapse">
							<tr>
								<th colspan="2" style="font-size:8px" align="center" valign="center" height="20px">Gerente General - Director - Vicepresidente</th>
							</tr>
							<tr>
								@if(count($Auto2Anticipo)==0)
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top">  </td>	
								@else
									@foreach ($Auto2Anticipo as $anticipo)
									{{$auto2 = $anticipo->autorizador}}
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$anticipo->autorizador}}<br>{{$anticipo->anticipo}}</strong></td>
									
									@endforeach
								@endif

								@if(count($Auto2Gasto)==0)
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"> </td>
								@else
									@foreach ($Auto2Gasto as $gasto)
										<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$gasto->autorizador}}<br>{{$gasto->gasto}}</strong></td>
									@endforeach	
								@endif
							</tr>
							
								@if($auto2=='')
									<tr>
										<th colspan="2" style="font-size:10px" align="center" valign="top" height="20px"">  </th>
									</tr>
								@else
									<tr>
										<th colspan="2" style="font-size:10px" align="center" valign="top" height="20px">{{$auto2}}</th>
									</tr>	
								@endif
						</table>
					</td>
					<td>
						<table border="" style="border-collapse: collapse">
							<tr>
								<th></th>
							</tr>
							<tr>
								<td width="20px" height="60px"><img src="https://png.pngtree.com/element_origin_min_pic/16/10/28/115d7ed11cf4e4f5b8ccdec2d42cbb8b.jpg" width="19pz" height="45px"></td>
							</tr>
							<tr>
								<th></th>
							</tr>
						</table>
					</td>
					<td>
						<table border="1" style="border-collapse: collapse">
							<tr>
								<th colspan="2" style="font-size:9px" align="center" valign="center" height="20px">Director General - General Director</th>
							</tr>
							<tr>
								@if(count($Auto3Anticipo)==0)
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"> </td>	
								@else
									@foreach ($Auto3Anticipo as $anticipo)
									{{$auto3 = $anticipo->autorizador}}
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$anticipo->autorizador}}<br>{{$anticipo->anticipo}}</strong></td>
									
									@endforeach
								@endif

								@if(count($Auto3Gasto)==0)
									<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"> </td>
								@else
									@foreach ($Auto3Gasto as $gasto)
										<td width="70px" height="75px" style="font-size:10px; text-align: center;" valign="top"><strong>{{$gasto->autorizador}}<br>{{$gasto->gasto}}</strong></td>
									@endforeach	
								@endif
							</tr>
							
								@if($auto3=='')
									<tr>
										<th colspan="2" style="font-size:10px" align="center" valign="top" height="20px">  </th>
									</tr>
								@else
									<tr>
										<th colspan="2" style="font-size:10px" align="center" valign="top" height="20px">{{$auto3}}</th>
									</tr>	
								@endif
						</table>
					</td>
				</tr>				
			</table>
			<table border="" width="100%">
				<tr><td height="15px"></td></tr>
			</table>
			<table border="" width="100%" style="border-collapse: collapse">
				<tr>
					<td >
						<table border="1"  style="border-collapse: collapse" cellpadding="0" cellspacing="0">
							<tr>
								<td height="20px" width="200px" bgcolor="Silver" style="font-size:11px">FECHA / DATE</td>
								<td height="20px" width="500px" style="font-size:11px" align="center">{{date('d-m-Y', strtotime($folio->fecha))}}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse" cellpadding="0" cellspacing="0">
							<tr>
								<td width="200px" style="font-size:11px" rowspan="2"  bgcolor="Silver">NOMBRE COMPLETO / FULL NAME<br>PUESTO / TITLE<br><br>NOMBRE DEL DEPTO / DEPT NAME</td>
								<td width="300px" style="font-size:11px" align="center" rowspan="2">{{$folio->name}}<br>{{$profile->puesto}}<br><br>{{$profile->departamento}}</td>
								<td width="200px" bgcolor="Silver"  style="font-size:10px;" align="center"> CUENTA CLABE / BANK ACCOUNT</td>
							</tr>
							<tr>
								<td style="font-size:11px" align="center">{{$profile->clabe}} /<br>{{$profile->cuenta}}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse" cellpadding="0" cellspacing="0">
							<tr>
								<td width="200px" style="font-size: 11px;" bgcolor="Silver">DESTINO / DESTINATION</td>
								<td width="300px" style="font-size: 11px;" align="center">{{$folio->destino}}</td>
								<td width="100px" style="font-size: 10px;" align="center" bgcolor="Silver">TIPO<br>TYPE</td>
								<td width="98px" style="font-size: 11px;" align="center">{{$folio->tipo}}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" >
							<tr>
								<td width="200px" style="font-size: 11px;" bgcolor="Silver">PROPOSITOS / PURPOSES</td>
								<td width="500px" align="center" style="font-size: 11px;">{{$folio->proposito}}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
							<tr>
								<td width="200px" style="font-size: 11px;" bgcolor="Silver">PERIODO / PERIOD</td>
								<td width="150px" style="font-size: 11px;" align="center">DE / FROM: {{ date('d-m-Y', strtotime($folio->fecha_salida)) }}</td>
								<td width="150px" style="font-size: 11px;" align="center">A / TO: {{date('d-m-Y', strtotime($folio->fecha_llegada))}}</td>
								<td width="100px" style="font-size: 11px;" bgcolor="Silver">DIAS / DAYS</td>
								<td width="97px" style="font-size: 11px;" align="center">{{$folio->dias}}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
							<tr>
								<td width="200px" style="font-size: 10px;" bgcolor="Silver">ANTICIPO DE VIAJE / TRIP ADVANCE</td>
								<td width="200px" style="font-size: 11px;" align="center">{{$folio->moneda}}</td>
								<td width="200px" style="font-size: 10px;" align="CENTER" bgcolor="Silver">ANTICIPO / ADVANCE PAYMENT</td>
								<td width="98px" style="font-size: 11px;" align="center">$ {{$folio->anticipo}}</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
							<tr>
								<td width="700px" height="5px"> </td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
							<tr>
								<td width="700px" height="10px" style="font-size: 8px;" align="center">REFERENCIAS / REFERENCES</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
							<tr>
								<td width="160px" style="font-size: 9px;" align="center" bgcolor="Silver">CONTACTO (PERSONA / COMPAÑIA)<br>CONTACT(PERSON / COMPANY)</td>
								<td width="40px" style="font-size: 8px;" align="center" bgcolor="Silver">NOMBRE<br>NAME</td>
								<td width="350px" style="font-size: 9px;" align="center"><br>
								</td>
								<td width="50px" style="font-size: 8px;" align="center" bgcolor="Silver">TEL. <br></td>
								<td width="98px" style="font-size: 8px;" align="center" ><br></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="1" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
							<tr>
								
							@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
								<th width="230px" style="font-size: 10px;" bgcolor="Silver" align="center">GASTO DE:</th>
								<th width="230px" style="font-size: 10px;" bgcolor="Silver" align="center">PAGADO CON AMEX:</th>
								<th width="230px" style="font-size: 10px;" bgcolor="Silver" align="center">PAGADO EN EFECTIVO:</th>
								@else
								<th width="180px" style="font-size: 10px;" bgcolor="Silver" align="center">GASTO DE:</th>
								<th width="130px" style="font-size: 10px;" bgcolor="Silver" align="center">PAGADO CON AMEX:</th>
								<th width="130px" style="font-size: 10px;" bgcolor="Silver" align="center">MONEDA:</th>
								<th width="130px" style="font-size: 10px;" bgcolor="Silver" align="center">PAGADO EN EFECTIVO:</th>
								<th width="130px" style="font-size: 10px;" bgcolor="Silver" align="center">PAGADO EN EFECTIVO (PESOS):</th>

								@endif
							</tr>
							<?php 
							$amex = 0;
							$efectivo = 0;
							$moneda = 0;
							$efectivopesos = 0;
							?>
							<tr>
								<td style="font-size: 12px;" align="center"><strong>HOTEL:</strong></td>
								@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
								@foreach($detalles as $hotel)

								@if($hotel->id_gasto==2 && $hotel->metodoPago=="AMEX")

								<?php $amex = $amex + $hotel->Subtotal; ?>
								@endif
								@if($hotel->id_gasto==2 && $hotel->metodoPago=="Efectivo")

								<?php $efectivo = $efectivo + $hotel->Subtotal; ?>
								@endif

								@endforeach

							
								
								<?php 
								if (empty($amex)) {
									echo "<td style='font-size: 12px;' align='center'>-</td>";
								}
								else{

									echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
								}

								if (empty($efectivo)) {
									echo "<td style='font-size: 12px;' align='center'>-</td>";
								}else{

									echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
								}

								$amex = 0;
								$efectivo = 0;
								$moneda = 0;
								$efectivopesos = 0;
								?>
								@else
									@foreach($detalle as $hotel)

									@if($hotel->id_gasto==2 && $hotel->metodoPago=="AMEX")

									<?php $amex = $amex + $hotel->Subtotal; ?>
									@endif
									@if($hotel->id_gasto==2 && $hotel->metodoPago=="Efectivo")

									@if($hotel->moneda <>'Pesos')
									<?php $efectivo = $efectivo + $hotel->Subtotal;?>
									<?php $efectivopesos = $efectivopesos + $hotel->Subtotalint;?>
									@else
									<?php $efectivopesos = $efectivopesos + $hotel->Subtotalint;?>
									
									@endif
									@endif
									
									
									@endforeach
									@foreach($tipomoneda as $monedas)
									@if($monedas->id_gasto==2)
									<?php  if($moneda<>''){
										$moneda = $monedas->moneda .'/'. $moneda;}
										else{
											$moneda = $monedas->moneda;
										}
										 ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
									}

									if (empty($moneda)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".$moneda."</td>";
									}
									
									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									if (empty($efectivopesos)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivopesos,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@endif
							</tr>
							<tr>
								<td style="font-size: 12px;" align="center"><strong>ESTACIONAMIENTO:</strong></td>
								@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
									@foreach($detalles as $parking)
									@if($parking->id_gasto==5 && $parking->metodoPago=="AMEX")

									<?php $amex = $amex + $parking->Subtotal; ?>
									@endif
									@if($parking->id_gasto==5 && $parking->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $parking->Subtotal; ?>
									
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($amex,2)."</td>";
									}

									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@else
									@foreach($detalle as $parking)
									@if($parking->id_gasto==5 && $parking->metodoPago=="AMEX")

									<?php $amex = $amex + $parking->Subtotal; ?>
									@endif
									@if($parking->id_gasto==5 && $parking->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $parking->Subtotal; ?>
									<?php $efectivopesos = $efectivopesos + $parking->Subtotalint; ?>
									@endif
									@endforeach
									@foreach($tipomoneda as $monedas)
									@if($monedas->id_gasto==5)
									<?php  if($moneda<>''){
										$moneda = $monedas->moneda .'/'. $moneda;}
										else{
											$moneda = $monedas->moneda;
										}
										 ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
									}

									if (empty($moneda)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".$moneda."</td>";
									}
									
									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									if (empty($efectivopesos)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivopesos,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@endif
							</tr>

							<tr>
								<td style="font-size: 12px;" align="center"><strong>TAXI:</strong></td>
								@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
									@foreach($detalles as $taxi)
									@if($taxi->id_gasto==3 && $taxi->metodoPago=="AMEX")

									<?php $amex = $amex + $taxi->Subtotal; ?>
									@endif
									@if($taxi->id_gasto==3 && $taxi->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $taxi->Subtotal; ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($amex,2)."</td>";
									}

									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@else
									@foreach($detalle as $taxi)
									@if($taxi->id_gasto==3 && $taxi->metodoPago=="AMEX")

									<?php $amex = $amex + $taxi->Subtotal; ?>
									@endif
									@if($taxi->id_gasto==3 && $taxi->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $taxi->Subtotal; ?>
									<?php $efectivopesos = $efectivopesos + $taxi->Subtotalint; ?>
									@endif
									@endforeach
									@foreach($tipomoneda as $monedas)
									@if($monedas->id_gasto==3)
									<?php  if($moneda<>''){
										$moneda = $monedas->moneda .'/'. $moneda;}
										else{
											$moneda = $monedas->moneda;
										}
										 ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
									}

									if (empty($moneda)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".$moneda."</td>";
									}
									
									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									if (empty($efectivopesos)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivopesos,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@endif
							</tr>

							<tr>
								<td style="font-size: 12px;" align="center"><strong>RENTA DE AUTO:</strong></td>
								@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
									@foreach($detalles as $auto)
									@if($auto->id_gasto==6 && $auto->metodoPago=="AMEX")

									<?php $amex = $amex + $auto->Subtotal; ?>
									@endif
									@if($auto->id_gasto==6 && $auto->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $auto->Subtotal; ?>
									@endif            
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($amex,2)."</td>";
									}

									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@else
									@foreach($detalle as $auto)
									@if($auto->id_gasto==6 && $auto->metodoPago=="AMEX")

									<?php $amex = $amex + $auto->Subtotal; ?>
									@endif
									@if($auto->id_gasto==6 && $auto->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $auto->Subtotal; ?>
									<?php $efectivopesos = $efectivopesos + $auto->Subtotalint; ?>
									@endif            
									@endforeach
									@foreach($tipomoneda as $monedas)
									@if($monedas->id_gasto==6)
									<?php  if($moneda<>''){
										$moneda = $monedas->moneda .'/'. $moneda;}
										else{
											$moneda = $monedas->moneda;
										}
										 ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
									}

									if (empty($moneda)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".$moneda."</td>";
									}
									
									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									if (empty($efectivopesos)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivopesos,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@endif
							</tr>

							<tr>
								<td style="font-size: 12px;" align="center"><strong>OTROS:</strong></td>
								@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
									@foreach($detalles as $otros)
									@if($otros->id_gasto==7 && $otros->metodoPago=="AMEX")

									<?php $amex = $amex + $otros->Subtotal; ?>
									@endif
									@if($otros->id_gasto==7 && $otros->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $otros->Subtotal; ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($amex,2)."</td>";
									}

									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@else
									@foreach($detalle as $otros)
									@if($otros->id_gasto==7 && $otros->metodoPago=="AMEX")

									<?php $amex = $amex + $otros->Subtotal; ?>
									@endif
									@if($otros->id_gasto==7 && $otros->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $otros->Subtotal; ?>
									<?php $efectivopesos = $efectivopesos + $otros->Subtotalint; ?>
									@endif
									@endforeach
									@foreach($tipomoneda as $monedas)
									@if($monedas->id_gasto==7)
									<?php  if($moneda<>''){
										$moneda = $monedas->moneda .'/'. $moneda;}
										else{
											$moneda = $monedas->moneda;
										}
										 ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
									}

									if (empty($moneda)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".$moneda."</td>";
									}
									
									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									if (empty($efectivopesos)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivopesos,2)."</td>";
									}

									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@endif 
							</tr>

							<tr>
								<td style="font-size: 12px;" align="center"><strong>VIATICO:</strong></td>
								@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
									@foreach($detalles as $viatico)
									@if($viatico->id_gasto==1 && $viatico->metodoPago=="AMEX")

									<?php $amex = $amex + $viatico->Subtotal; ?>
									@endif
									@if($viatico->id_gasto==1 && $viatico->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $viatico->Subtotal; ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($amex,2)."</td>";
									}

									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}
									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@else
									@foreach($detalle as $viatico)
									@if($viatico->id_gasto==1 && $viatico->metodoPago=="AMEX")

									<?php $amex = $amex + $viatico->Subtotal; ?>
									@endif
									@if($viatico->id_gasto==1 && $viatico->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $viatico->Subtotal; ?>
									<?php $efectivopesos = $efectivopesos + $viatico->Subtotalint; ?>
									@endif
									@endforeach
									@foreach($tipomoneda as $monedas)
									@if($monedas->id_gasto==1)
									<?php  if($moneda<>''){
										$moneda = $monedas->moneda .'/'. $moneda;}
										else{
											$moneda = $monedas->moneda;
										}
										 ?>
									@endif
									@endforeach
                                    
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
									}

									if (empty($moneda)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".$moneda."</td>";
									}
									
									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									if (empty($efectivopesos)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivopesos,2)."</td>";
									}
									$amex = 0;
									$efectivo = 0;
									$moneda = 0;
									$efectivopesos = 0;
									?>
								@endif
							</tr>

							<tr>
								<td style="font-size: 12px;" align="center"><strong>DEVOLUCION:</strong></td>
								@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
									@foreach($detalles as $devolucion)
									@if($devolucion->id_gasto==8 && $devolucion->metodoPago=="AMEX")

									<?php $amex = $amex + $devolucion->Subtotal; ?>
									@endif
									@if($devolucion->id_gasto==8 && $devolucion->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $devolucion->Subtotal; ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($amex,2)."</td>";
									}

									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}
									?>
								@else
									@foreach($detalle as $devolucion)
									@if($devolucion->id_gasto==8 && $devolucion->metodoPago=="AMEX")

									<?php $amex = $amex + $devolucion->Subtotal; ?>
									@endif
									@if($devolucion->id_gasto==8 && $devolucion->metodoPago=="Efectivo")

									<?php $efectivo = $efectivo + $devolucion->Subtotal; ?>
									<?php $efectivopesos = $efectivopesos + $devolucion->Subtotalint; ?>
									@endif
									@endforeach
									@foreach($tipomoneda as $monedas)
									@if($monedas->id_gasto==8)
									<?php  if($moneda<>''){
										$moneda = $monedas->moneda .'/'. $moneda;}
										else{
											$moneda = $monedas->moneda;
										}
										 ?>
									@endif
									@endforeach
									<?php 
									if (empty($amex)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}
									else{

										echo "<td style='font-size: 12px;' align='center>".number_format($amex,2)."</td>";
									}

									if (empty($moneda)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".$moneda."</td>";
									}
									
									if (empty($efectivo)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivo,2)."</td>";
									}

									if (empty($efectivopesos)) {
										echo "<td style='font-size: 12px;' align='center'>-</td>";
									}else{

										echo "<td style='font-size: 12px;' align='center'>".number_format($efectivopesos,2)."</td>";
									}
									?>
								@endif
							</tr>
							
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
						@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <"2019-10-08 00:00:00")
							<tr>
								<td width="464px" style="font-size: 10px;" align="right">TOTAL GASTO &nbsp;</th></td>
								<td width="230px" style="font-size: 12px; border: solid 1px; " align="center">{{$folio->all_total}}</th></td>
							</tr>
							<tr>
								<td width="464px" style="font-size: 10px;" align="right">ANTICIPO &nbsp;</th></td>
								<td width="230px" style="font-size: 12px; border: solid 1px; " align="center">{{$folio->anticipo}}</th></td>
							</tr>
							<tr>
								<td width="464px" style="font-size: 10px;" align="right">DIF A CARGO (FAVOR) &nbsp;</th></td>
								<td width="230px" style="font-size: 12px; border: solid 1px; " align="center">{{number_format(abs($folio->anticipo-$folio->all_total), 2)}}</th></td>
							</tr>
						@else
							<tr>
								<td width="582px" style="font-size: 10px;" align="right">TOTAL GASTO &nbsp;</th></td>
								<td width="140px" style="font-size: 12px; border: solid 1px; " align="center">{{$folio->all_total}}</th></td>
							</tr>
							<tr>
								<td width="582px" style="font-size: 10px;" align="right">ANTICIPO &nbsp;</th></td>
								<td width="140px" style="font-size: 12px; border: solid 1px; " align="center">{{$folio->anticipo}}</th></td>
							</tr>
							<tr>
								<td width="582px" style="font-size: 10px;" align="right">DIF A CARGO (FAVOR) &nbsp;</th></td>
								<td width="140px" style="font-size: 12px; border: solid 1px; " align="center">{{number_format(abs($folio->anticipo-$folio->all_total), 2)}}</th></td>
							</tr>
						@endif
							
						</table>
						<table>
							<tr>
								<td width="700px" style="font-size: 10px;">
									@if($folio->anticipo > $folio->all_total && $folio->id_status>8)
										<p>*FAVOR DE REALIZAR LA DEVOLUCION DE <strong>{{number_format(abs($folio->anticipo-$folio->all_total),2)}} {{$folio->moneda}}</strong> A LA SIGUIENTE CUENTA. <br>
										SSM SERVICIOS SA DE CV <br>
										CUENTA: PESOS <br>
										NOMBRE DEL BANCO: BBVA BANCOMER <br>
										DIRECCION DEL BANCO: CONSTITUYENTES 120 PTE COL. EL CARRIZAL <br>
										No. DE SUCURSAL: 828 <br>
										NO.  DE CUENTA: 0149395768 <br>
										NO. DE CUENTA CLABE:  012 680 001 493 957 685 <br>
										QUERETARO QRO.</p>
									@elseif($folio->anticipo < $folio->all_total)
										<p>*EN LOS PROXIMOS DIAS SE ESTARA REMBOLSANDO LA CANTIDAD DE <strong>{{number_format(abs($folio->anticipo-$folio->all_total),2)}} {{$folio->moneda}}</strong></p>
									@endif
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</table>
</div>
</body>
</html>