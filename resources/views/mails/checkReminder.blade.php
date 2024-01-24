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
            <p>
              Te recordamos que tienes pendiente la comprobación de gastos con el No de Folio <strong>{{$data1->id_header_folio}}</strong> y cuentas con un saldo de <strong>{{$diff}}</strong> día(s) para realizarla, de lo contrario el folio se deshabilitara.
            </p>
            <p>
              We remind you that you have pending the verification of expenses with the No of Folio <strong>{{$data1->id_header_folio}}</strong> and accounts with a balance of <strong>{{$diff}}</strong> day(s) to make it, otherwise the folio will be disabled.
            </p>
          </td>
          </tr>
        </table>
      </td></tr>
      <tr>
        <td>
        <table width="100%" border="0">
          <tr>
            <td>
              <table  width="100%" border="0" bgcolor="#dbdbdb">
                <tr>
                  <td width="50%" valign="top">
                    <p><strong>NOMBRE / FULL NAME</strong><br /> {{$data1->name}}</p>
                    <p><strong>DESTINO / DESTINATION</strong><br /> {{$data1->destino}}</p>
                    <p><strong>PROPOSITO / PURPOSES</strong><br /> {{$data1->proposito}}</p>
                  </td>
                  <td width="50%" valign="top">
                    <p><strong>TIPO / TYPE:</strong><br /> {{$data1->tipo}}<br /></p> 
                    <p><strong>PERIODO / PERIOD</strong><br /> <strong>DE / FROM:</strong> {{date('d-m-Y', strtotime($data1->fecha_salida))}}<br /> <strong>A / TO:</strong> {{date('d-m-Y', strtotime($data1->fecha_llegada))}}<br /></p>
                    <p><strong>ANTICIPO / ADVANCE</strong><br /> $ {{$data1->anticipo}} {{$data1->moneda}}<br /></p>
                  </td>
                </tr>
              </table>
            </td>         
          </tr>
        </table>
        </td>
      </tr>
  </table>
</body>
</html>