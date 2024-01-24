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
            	Te informamos que cuentas con folios pendientes de aprobar, por favor da click <strong><a href="{{url('')}}/authorizers/approbation">AQUI</a></strong> para ingresar al portal si deseas autorizarlos.
            </p>
            <p>
            	We inform you that you have pending folios to approve, please click <strong><a href="{{url('')}}/authorizers/approbation">HERE</a></strong> to access the portal if you wish to authorize them.
            </p>
            <p>
              Folios pending Approve./Folios pendientes de Aprobar.

                @foreach($folios as $folio)

                  <h4>Folio N°. {{$folio->id_header_folio}}</h4>

                @endforeach
            </p>


          </td>
          </tr>
        </table>
      </td></tr>
      <tr>
        <td>
        </td>
      </tr>
  </table>
</body>
</html>