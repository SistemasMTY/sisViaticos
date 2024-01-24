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
            <p>La comprobación de Gastos con el Folio Numero <strong>{{$data1->id_header_folio}}</strong>, ha sido denegada por <strong>{{$data1->autorizador}}</strong>, favor de revisarlo con Gerencia General.</p>
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
                <td width="50%">
                  <p><strong>NOMBRE / FULL NAME</strong><br /> {{$data1->name}}</p>
                        <p><strong>DESTINO / DESTINATION</strong><br /> {{$data1->destino}}</p>
                        <p><strong>PROPOSITO / PURPOSES</strong><br /> {{$data1->proposito}}</p>
                </td>
                <td width="50%">
                  <p><strong>TIPO / TYPE:</strong><br /> {{$data1->tipo}}<br /></p>
                  <p><strong>PERIODO / PERIOD</strong><br /> <strong>DE / FROM:</strong> {{date('d-m-Y', strtotime($data1->fecha_salida))}}<br /> <strong>A / TO:</strong> {{date('d-m-Y', strtotime($data1->fecha_llegada))}}<br /></p>
                        <p><strong>ANTICIPO / ADVANCE</strong><br /> $ {{$data1->anticipo}} {{$data1->moneda}}<br /></p>
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
                      <td width="20%"><strong>GASTO DE:</strong></td>
                      <td width="20%"><strong>PAGADO CON AMEX</strong></td>
                      <td width="20%"><strong>PAGADO EN EFECTIVO</strong></td>
                    </tr>
                    <?php 
                    $total = 0; 
                    $amex = 0;
                    $efectivo = 0;
                    ?>
                  
                    <tr>
                      <td><strong>HOTEL:</strong></td>
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
                      ?>
                    </tr>

                    <tr>
                      <td><strong>ESTACIONAMINETO:</strong></td>
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
                      ?>
                    </tr>

                    <tr>
                      <td><strong>TAXI:</strong></td>
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
                      ?>
                    </tr>

                    <tr>
                      <td><strong>RENTA DE AUTO:</strong></td>
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
                      ?>                           
                    </tr>

                    <tr>
                      <td><strong>OTROS:</strong></td>
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
                      ?>
                    </tr>

                    <tr>
                      <td><strong>VIATICO:</strong></td>
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
                      ?>
                    </tr>

                    <tr>
                      <td></td>
                      <td><strong>TOTAL</strong></td>      
                      <td><strong>$ {{ $data1->all_total }}</strong></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td><strong>ANTICIPO DE VIAJE</strong></td>      
                      <td><strong>$ {{ $data1->anticipo }}</strong></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td><strong>DIF A CARGO (FAVOR)</strong></td>      
                      <td><strong>$ {{($data1->anticipo-$data1->all_total)}}</strong></td>
                    </tr>
                  </table>
              @endif
            </td>
          </tr>
        </table>
      </td></tr>
  </table>
</body>
</html>