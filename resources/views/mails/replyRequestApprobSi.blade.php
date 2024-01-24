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
  </style>
  <title></title>
</head>
<body>
  <table style="width: 90%; height: 134px; text-align: left;" border="0" >
    <tr><td>
      <table align="center" border="0">
        <tr>
          @if($folioMail->id_status == 8 OR $folioMail->id_status > 15 )
            
            @if(count($foliosPend)>0 && $folioMail->anticipo > 0)

              <td >
                <h1>Gracias por usar el Sistema ADViaticos.</h1>
                <p>
                  En hora buena, tu solicitud de viaje con el Folio <strong>{{$folioMail->id_header_folio}}</strong> ha sido aprobada, pero debido a que cuentas con alguna(s) de las siguientes situaciones: Folio con fechas anteriores a este viaje, folio pendiente de comprobar o anticipo en espera. <br>
                  La solicitud de tu anticipo tendrá que ser aprobada por el departamento de cuentas por pagar como un anticipo extraordinario.
                </p>
                <p>
                  In good time, your travel request with Folio <strong>{{$folioMail->id_header_folio}}</strong> has been approved, but because you have any of the following situations: Folio with dates before this trip, folio pending check or advance pending. <br>
                  The request for your advance will have to be approved by the accounts payable department as an extraordinary advance.
                </p>
                <p>
                  Folio with dates prior to this trip, pending verification or with assigned advance./Folio con fechas previas a este viaje, pendientes de comprobación o con anticipo asignado.

                  @foreach($foliosPend as $Pend)

                    <h4>Folio N°. {{$Pend->id_header_folio}}</h4>
                  @endforeach
                </p>
              </td>
            @elseif(count($foliosPend)>0 && $folioMail->anticipo == 0)

              <td >
                <h1>Gracias por usar el Sistema ADViaticos.</h1>
                <p>
                  En hora buena, tu solicitud de viaje con el Folio <strong>{{$folioMail->id_header_folio}}</strong> ha sido aprobada, te recordamos que cuentas con Folios pendientes por comprobar.
                </p>
                <p>
                  In good time, your travel request with Folio <strong>{{$folioMail->id_header_folio}}</strong> has been approved, we remind you that you have pending Folios to check.
                </p>
                <p>
                  Folios pendientes de comprobación o con anticipo asignado/Folios pending verification or with assigned advance.
                </p>
                <p>
                  @foreach($foliosPend as $Pend)

                    <h4>Folio N°. {{$Pend->id_header_folio}}</h4>
                  @endforeach
                </p>
              </td>
            @else

              <td >
                <h1>Gracias por usar el Sistema ADViaticos.</h1>
                <p>
                  En hora buena, tu solicitud de anticipo con el Folio Numero <strong>{{$folioMail->id_header_folio}}</strong>, ha sido aprobada por <strong>{{$folioMail->NombreAuto}}</strong>, se le ha notificado a Tesorería para el deposito del anticipo.
                </p>
              </td>

            @endif

          @else
            <td>
              <h1>Gracias por usar el Sistema ADViaticos.</h1>
              <p>En hora buena, tu solicitud de anticipo con el Folio Numero <strong>{{$folioMail->id_header_folio}}</strong>, ha sido aprobada por <strong>{{$folioMail->NombreAuto}}</strong>, esta solicitud de anticipo queda en proceso de ser autorizada por el siguiente autorizador.</p>
              <p>In good time, your advance request with Folio Number <strong>{{$folioMail->id_header_folio}}</strong>, has been approved  by <strong>{{$folioMail->NombreAuto}}</strong>, this advance request is in the process of being approved by the following authorizer.</p>
          </td>
          @endif
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
                  <p><strong>NOMBRE / FULL NAME</strong><br /> {{$folioMail->name}}</p>
                        <p><strong>DESTINO / DESTINATION</strong><br /> {{$folioMail->destino}}</p>
                        <p><strong>PROPOSITO / PURPOSES</strong><br /> {{$folioMail->proposito}}</p>
                        <p><strong>EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</strong><br /> {{$folioMail->eq_computo}}</p>
                </td>
                <td width="50%">
                  <p><strong>TIPO / TYPE:</strong><br /> {{$folioMail->tipo}}<br /></p>
                  <p><strong>PERIODO / PERIOD</strong><br /> <strong>DE / FROM:</strong> {{date('d-m-Y', strtotime($folioMail->fecha_salida))}}<br /> <strong>A / TO:</strong> {{date('d-m-Y', strtotime($folioMail->fecha_llegada))}}<br /></p>
                        <p><strong>ANTICIPO / ADVANCE</strong><br /> $ {{$folioMail->anticipo}} {{$folioMail->moneda}}<br /></p>
                </td>
              </tr>
            </table>
          </td>         
        </tr>
      </table>
    </td></tr>
    <tr><td></td></tr>
  </table>
</body>
</html>