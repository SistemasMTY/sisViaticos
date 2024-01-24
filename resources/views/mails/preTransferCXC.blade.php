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
  <table style="width: 90%; height: 134px;" border="0">
    <tr><td>
      <table align="center" border="0">
        <tr>
          <td>
            <h1>Gracias por usar el Sistema ADViaticos.</h1>
            <p>El Departamento de Cuentas por Pagar ha autorizado el Folio No. <strong>{{$folioMail->id_header_folio}}</strong> del usuario <strong>{{$folioMail->name}}</strong>. Por tal motivo el anticipo del Folio <strong>{{$folioUpdate->id_header_folio}}</strong> ha sido liberado para su depósito, favor de realizar el anticipo a la cuenta: <strong>{{$folioMail->BancoCuenta}} / {{$folioMail->CLABE}}</strong> del Banco: <strong>{{$folioMail->Banco}}</strong> antes del día: <strong>{{date('d-m-Y', strtotime($folioUpdate->fecha_salida))}}</strong></br ></strong>
            </p>
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
                  <p><strong>NOMBRE / FULL NAME</strong><br /> {{$folioMail->name}}</p>
                        <p><strong>DESTINO / DESTINATION</strong><br /> {{$folioUpdate->destino}}</p>
                        <p><strong>PROPOSITO / PURPOSES</strong><br /> {{$folioUpdate->proposito}}</p>
                        <p><strong>EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</strong><br /> {{$folioMail->eq_computo}}</p>
                </td>
                <td width="50%">
                  <p><strong>TIPO / TYPE:</strong><br /> {{$folioUpdate->tipo}}<br /></p>
                  <p><strong>PERIODO / PERIOD</strong><br /> <strong>DE / FROM:</strong> {{date('d-m-Y', strtotime($folioUpdate->fecha_salida))}}<br /> <strong>A / TO:</strong> {{date('d-m-Y', strtotime($folioUpdate->fecha_llegada))}}<br /></p>
                        <p><strong>ANTICIPO / ADVANCE</strong><br /> $ {{$folioUpdate->anticipo}} {{$folioUpdate->moneda}}<br /></p>
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
    <tr><td></td></tr>
  </table>
</body>
</html>