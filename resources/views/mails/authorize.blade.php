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
            <p>El Usuario <strong>{{$data1->name}}</strong> esta solicitando un anticipo para un Viaje de Negocio con el No. Folio <strong>{{$data1->id_header_folio}}</strong> por la cantidad de <strong>$ {{$data1->anticipo}} {{$data1->moneda}}</strong>.</br >
            <p>User <strong>{{$data1->name}}</strong> requests authorization for a business trip with Folio Number <strong>{{$data1->id_header_folio}}</strong> in the amount of <strong>$ {{$data1->anticipo}} {{$data1->moneda}}</strong>.</p>
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
      <table border="0"  cellspacing="0">
        <tr>
          <td><h3>Desea Autorizar la solicitud de Viaje?</h3>
            <h3>You want to Authorize the Trip request?</h3>
          </td>
        </td>
        </tr>
      </table>
    </td></tr>
    <tr><td>
      <table width="100%" border="0">
        <tr>
          <td><a href="{{url('')}}/authorizeGet/{{$data1->_token}}/token/{{$data1->id_header_folio}}/folio/si" class="boton_1">YES</a></td>
          <td><a href="{{url('')}}/authorizeGet/{{$data1->_token}}/token/{{$data1->id_header_folio}}/folio/no" class="boton_2">NO</a></td>
        </tr>
        
      </table>
    </td></tr>
    <tr><td></td></tr>
  </table>
</body>
</html>