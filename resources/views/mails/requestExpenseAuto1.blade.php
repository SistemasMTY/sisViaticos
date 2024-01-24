<!DOCTYPE html>
<html>
<head>
  <style type="text/css"> 
  a:link 
  { 
    text-decoration:none; 
  }
  .boton_1{
    text-decoration: none;
    padding: 3px;
    padding-left: 10px;
    padding-right: 10px;
    font-family: helvetica;
    font-weight: 300;
    font-size: 25px;
    font-style: italic;
    color: #006505;
    background-color: #82b085;
    border-radius: 15px;
    border: 3px double #006505;
  }
  .boton_1:hover{
    opacity: 0.6;
    text-decoration: none;
  }
  .boton_2{
    text-decoration: none;
    padding: 3px;
    padding-left: 10px;
    padding-right: 10px;
    font-family: helvetica;
    font-weight: 300;
    font-size: 25px;
    font-style: italic;
    color: #FF0000;
    background-color: #EC6969;
    border-radius: 15px;
    border: 3px double #FF0000;
  }
  .boton_2:hover{
    opacity: 0.6;
    text-decoration: none;
  }

  .tablaGasto{
    width: 100%;
    border: 1px solid #999;
    text-align: left;
    border-collapse: collapse;
    margin: 0 0 1em 0;
    caption-side: top;
  }

  .tablaGasto tr {
    border-bottom: 1px solid #ccc;
  }

  .tablaGasto caption, td, th{
    padding: 0.3em;
    {

      .tablaGasto td, th{
        border-bottom: 1px solid #999;
      }

      .tablaGasto caption {
       font-weight: bold;
       font-style: italic;
     }

   }
 </style>
 <title></title>
</head>
<body>
  <table style="width: 90%; height: 134px;" border="0">
    <tr><td>
      <table align="center" border="0">
        <tr>
          <td>
            <h1>Gracias por usar el Sistema ADViaticos.</h1>
            <p>El Usuario <strong>{{$folioMail->name}}</strong> esta solicitando una comprobacio de Gastos del Viaje de Negocio con el No. Folio <strong>{{$folioMail->id_header_folio}}</strong> por la cantidad de <strong>$ {{$folioMail->anticipo}} {{$folioMail->moneda}}</strong>.
            <p>The User <strong>{{$folioMail->name}}</strong> is requesting a check of Expenses of the Business Trip with No. Folio <strong>{{$folioMail->id_header_folio}}</strong> in the amount of <strong>$ {{$folioMail->anticipo}} {{$folioMail->moneda}}</strong>.</p>
            </td>
          </tr>
        </table>
      </td></tr>
      <tr><td>
        <table width="100%" border="0">
          <tr>
            <td>
              <table  width="100%" border="0" bgcolor="#dbdbdb">
                <tr>
                  <td width="50%" valign="top">
                    <p><strong>NOMBRE / FULL NAME</strong><br /> {{$folioMail->name}}</p>
                    <p><strong>DESTINO / DESTINATION</strong><br /> {{$folioMail->destino}}</p>
                    <p><strong>PROPOSITO / PURPOSES</strong><br /> {{$folioMail->proposito}}</p>
                    <p><strong>EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</strong><br /> {{$folioMail->eq_computo}}</p>
                  </td>
                  <td width="50%" valign="top">
                    <p><strong>TIPO / TYPE:</strong><br /> {{$folioMail->tipo}}<br /></p> 
                    <p><strong>PERIODO / PERIOD</strong><br /> <strong>DE / FROM:</strong> {{date('d-m-Y', strtotime($folioMail->fecha_salida))}}<br /> <strong>A / TO:</strong> {{date('d-m-Y', strtotime($folioMail->fecha_llegada))}}<br /></p>
                    <p><strong>ANTICIPO / ADVANCE</strong><br /> $ {{$folioMail->anticipo}} {{$folioMail->moneda}}<br /></p>
                    <p><strong>COMENTARIOS DE VIAJE / TRIP COMMENTS</strong><br /> {{$folioMail->evidencia_viaje}}<br /></p>
                  </td>
                </tr>
              </table>
              <table  width="100%" border="0" bgcolor="#dbdbdb">
                <tr>
                  <td width="50%" valign="top">
                    <p><strong>COMENTARIOS DE VIAJE / TRIP COMMENTS</strong><br /> {{$folioMail->evidencia_viaje}}<br /></p>
                  </td>
                </tr>
              </table>
            </td>         
          </tr>
        </table>
      </td></tr>
      <tr><td>
        <table width="100%" border="1">
          <tr>
            <td>
              <table border="0" >
                <tr>
                  <td>
                    Gastos / Expense
                  </td>
                </tr>
              </table>
              @if (count($detalles)>0)

              <table border="0" class="tablaGasto">
                <tr>
                @if($folioMail->tipo =="Nacional")
                  <td width="20%"><strong>GASTO DE:</strong></td>
                  <td width="20%"><strong>PAGADO CON AMEX</strong></td>
                  <td width="20%"><strong>PAGADO EN EFECTIVO</strong></td>
                @else
                  <td width="15%"><strong>GASTO DE:</strong></td>
                  <td width="15%"><strong>PAGADO CON AMEX</strong></td>
                  <td width="15%"><strong>MONEDA</strong></td>
                  <td width="15%"><strong>PAGADO EN EFECTIVO</strong></td>
                  <td width="15%"><strong>PAGADO EN EFECTIVO (PESOS)</strong></td>
                @endif
                </tr>
                <?php 
                $amex = 0;
                $efectivo = 0;
                $moneda = '';
                $efectivopesos = 0;
                ?>
                
                <tr>
                  <td><strong>HOTEL:</strong></td>
                  @if($folioMail->tipo =="Nacional")
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda= '';
                  $efectivopesos = 0;
                  ?>
                  @else
                  <!-- HOTEL FOLIO INTERNACIONAL -->
                  @foreach($detalle as $hotel)

                  @if($hotel->id_gasto==2 && $hotel->metodoPago=="AMEX")
                  
                  <?php $amex = $amex + $hotel->Subtotal; ?>
                  @endif
                  @if($hotel->id_gasto==2 && $hotel->metodoPago=="Efectivo")
                  
                  <?php $efectivo = $efectivo + $hotel->Subtotal; ?>
                  <?php $efectivopesos = $efectivopesos + $hotel->Subtotalint; ?>
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }
                  if (empty($moneda)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$moneda."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }
                  if (empty($efectivopesos)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivopesos."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda= '';
                  $efectivopesos = 0;
                  ?>
                  @endif
                </tr>

                <tr>
                  <td><strong>ESTACIONAMINETO:</strong></td>
                  @if($folioMail->tipo =="Nacional")
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda= '';
                  $efectivopesos = 0;
                  ?>
                  @else
                  <!-- ESTACIONAMIENTO FOLIO INTERNACIONAL -->
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
                      echo "<td>-</td>";
                    }
                    else{
  
                      echo "<td>".$amex."</td>";
                    }
                    if (empty($moneda)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$moneda."</td>";
                    }
  
                    if (empty($efectivo)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivo."</td>";
                    }
                    if (empty($efectivopesos)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivopesos."</td>";
                    }
                  $amex = 0;
                  $efectivo = 0;
                  $moneda= '';
                  $efectivopesos = 0;
                  ?>
                  @endif

                </tr>

                <tr>
                  <td><strong>TAXI:</strong></td>
                  @if($folioMail->tipo =="Nacional")
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @else
                  <!-- TAXI FOLIO INTERNACIONAL -->
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
                      echo "<td>-</td>";
                    }
                    else{
  
                      echo "<td>".$amex."</td>";
                    }
                    if (empty($moneda)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$moneda."</td>";
                    }
  
                    if (empty($efectivo)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivo."</td>";
                    }
                    if (empty($efectivopesos)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivopesos."</td>";
                    }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @endif
                </tr>

                <tr>
                  <td><strong>RENTA DE AUTO:</strong></td>
                  @if($folioMail->tipo =="Nacional")
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @else
                  <!-- RENTA DE AUTO FOLIO INTERNACIONAL -->
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
                      echo "<td>-</td>";
                    }
                    else{
  
                      echo "<td>".$amex."</td>";
                    }
                    if (empty($moneda)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$moneda."</td>";
                    }
  
                    if (empty($efectivo)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivo."</td>";
                    }
                    if (empty($efectivopesos)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivopesos."</td>";
                    }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @endif                           
                </tr>

                <tr>
                  <td><strong>OTROS:</strong></td>
                  @if($folioMail->tipo =="Nacional")
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @else
                  <!-- OTROS FOLIO INTERNACIONAL -->
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
                      echo "<td>-</td>";
                    }
                    else{
  
                      echo "<td>".$amex."</td>";
                    }
                    if (empty($moneda)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$moneda."</td>";
                    }
  
                    if (empty($efectivo)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivo."</td>";
                    }
                    if (empty($efectivopesos)) {
                      echo "<td>-</td>";
                    }else{
  
                      echo "<td>".$efectivopesos."</td>";
                    }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @endif
                </tr>

                <tr>
                  <td><strong>VIATICO:</strong></td>
                  @if($folioMail->tipo =="Nacional")
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @else
                  <!-- VIATICOS FOLIO INTERNACIONAL -->
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }
                  if (empty($moneda)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$moneda."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }
                  if (empty($efectivopesos)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivopesos."</td>";
                  }

                  $amex = 0;
                  $efectivo = 0;
                  $moneda = '';
                  $efectivopesos = 0;
                  ?>
                  @endif
                </tr>

                <tr>
                  <td><strong>DEVOLUCION:</strong></td>
                  @if($folioMail->tipo =="Nacional")
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }
                  ?>
                  @else
                  <!-- DEVOLUCION FOLIO INTERNACIONAL -->
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
                    echo "<td>-</td>";
                  }
                  else{

                    echo "<td>".$amex."</td>";
                  }
                  if (empty($moneda)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$moneda."</td>";
                  }

                  if (empty($efectivo)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivo."</td>";
                  }
                  if (empty($efectivopesos)) {
                    echo "<td>-</td>";
                  }else{

                    echo "<td>".$efectivopesos."</td>";
                  }

                  ?>
                  @endif
                </tr>
                @if($folioMail->tipo =="Nacional")
                <tr>
                  <td></td>
                  <td><strong>TOTAL</strong></td>      
                  <td><strong>$ {{ $folioMail->all_total }}</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td><strong>ANTICIPO DE VIAJE</strong></td>      
                  <td><strong>$ {{ $folioMail->anticipo }}</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td><strong>DIF A CARGO (FAVOR)</strong></td>      
                  <td><strong>$ {{($folioMail->anticipo-$folioMail->all_total)}}</strong></td>
                </tr>
                @else
                <tr>
                  <td></td>
                  <td></td>
                  <td><strong>TOTAL</strong></td> 
                  <td></td>     
                  <td><strong>$ {{ $folioMail->all_total }}</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td><strong>ANTICIPO DE VIAJE</strong></td>}
                  <td></td>      
                  <td><strong>$ {{ $folioMail->anticipo }}</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td><strong>DIF A CARGO (FAVOR)</strong></td>
                  <td></td>      
                  @if($folioMail->tipo =="Nacional" ||$folioMail->fecha_llegada <="2019-10-03 00:00:00" || $folioMail->moneda == "Pesos"||  $folioMail->anticipo == "0.00")        
                  <td><strong>$ {{($folioMail->anticipo-$folioMail->all_total)}}</strong></td>
                  @else
                  <td><strong>$ {{($folioMaill->montopesos-$folioMaill->all_total)}}</strong></td>
                  @endif
                </tr>
                @endif
              </table>
              @endif
            </td>
          </tr>
        </table>
      </td></tr>
      <tr><td>
        <table border="0"  cellspacing="0">
          <tr>
          <td>
              <h3>Si desea ver el detalle de los gastos o aprobar o rechazar la solcitud, por favor presiona <a href="http://summitmx.com:8080/authorizers/approbation/{{$folioMail->id_header_folio}}">aquí</a></h3>
              <h3>Do you want to see the details of the expenses or approve or reject the application ?, please click <a href="http://summitmx.com:8080/authorizers/approbation/{{$folioMail->id_header_folio}}">here</a></h3>
              <hr>
            
              <!-- <h3>Desea Autorizar la solicitud de Viaje?</h3>
              <h3>Do you want to Authorize the Trip request?</h3> -->
            </td>
          </td>
        </tr>
      </table>
    </td></tr>
    <tr><td>
      
    </td></tr>
    <tr><td></td></tr>
  </table>
</body>
</html>