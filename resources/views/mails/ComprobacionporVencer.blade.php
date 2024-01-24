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
            <h1>Estimado usuario <strong>{{$data2->name}}</strong></h1>
            <p>Le recordamos que el Folio No. <strong>{{$data2->id_header_folio}}</strong> aun esta pendiente por comprobar, le pedimos su apoyo para realizar la comprobacion correspondiente y mandar a autorizacion.
            </p>
            <p>We remind you that Folio No.<strong>{{$data2->id_header_folio}}</strong> it is still pending verification, we ask for your support to carry out the corresponding verification and send it to authorization.
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
                  <p><strong>NOMBRE / FULL NAME</strong><br /> {{$data2->name}}</p>
                        <p><strong>DESTINO / DESTINATION</strong><br /> {{$data2->destino}}</p>
                        <p><strong>PROPOSITO / PURPOSES</strong><br /> {{$data2->proposito}}</p>
                        <p><strong>EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</strong><br /> {{$data2->eq_computo}}</p>
                </td>
                <td width="50%">
                  <p><strong>TIPO / TYPE:</strong><br /> {{$data2->tipo}}<br /></p>
                  <p><strong>PERIODO / PERIOD</strong><br /> <strong>DE / FROM:</strong> {{date('d-m-Y', strtotime($data2->fecha_salida))}}<br /> <strong>A / TO:</strong> {{date('d-m-Y', strtotime($data2->fecha_llegada))}}<br /></p>
                        <p><strong>ANTICIPO / ADVANCE</strong><br /> $ {{$data2->anticipo}} {{$data2->moneda}}<br /></p>
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