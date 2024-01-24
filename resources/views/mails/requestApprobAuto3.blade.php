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
            <p>El Usuario <strong>{{$folioMail->name}}</strong> esta solicitando un anticipo para un Viaje de Negocio con el No. Folio <strong>{{$folioMail->id_header_folio}}</strong> por la cantidad de <strong>$ {{$folioMail->anticipo}} {{$folioMail->moneda}}</strong>.</br >
            <p>User <strong>{{$folioMail->name}}</strong> requests authorization for a business trip with Folio Number <strong>{{$folioMail->id_header_folio}}</strong> in the amount of <strong>$ {{$folioMail->anticipo}} {{$folioMail->moneda}}</strong>.</p>
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
    <tr><td>
        <table border="0"  cellspacing="0">
          <tr>
            <td>
            <h3>Para aprobar o rechazar la solicitud por favor presiona <a href="http://summitmx.com:8080/authorizers/approbation/{{$folioMail->id_header_folio}}">aquí</a></h3>
            <h3>To approve or reject the request please click <a href="http://summitmx.com:8080/authorizers/approbation/{{$folioMail->id_header_folio}}">here</a></h3>
              <!-- <h3>Desea Autorizar la solicitud de Viaje?</h3>
              <h3>Do you want to Authorize the Trip request?</h3> -->
            </td>
          </td>
        </tr>
      </table>
    </td></tr>
    <tr>
      <td>
       
      </td>
    </tr>
    <tr><td></td></tr>
  </table>
</body>
</html>