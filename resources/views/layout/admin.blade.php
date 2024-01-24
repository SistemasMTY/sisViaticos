<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />

    <title>ADViaticos | </title>
    <link rel="stylesheet" href="@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-select.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('css/font-awesome.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('css/AdminLTE.min.css')}}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{asset('css/_all-skins.min.css')}}">
    <link rel="apple-touch-icon" href="{{asset('img/apple-touch-icon.png')}}">
    <link rel="shortcut icon" href="{{asset('img/favicon.ico')}}">
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>




  </head>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <header class="main-header">

        <!-- Logo -->
        <a href="{{url('home')}}" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>ADV</b></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>ADViaticos</b></span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Navegación</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->
              
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="hidden-xs">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    
                    <p>
                      {{ Auth::user()->name }}
                      <small>{{ $userClaims[0]->depto }}</small>
                    </p>
                  </li>
                  
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    
                    <div class="pull-right">
                      <a href="{{ url('/logout') }}" class="btn btn-default btn-flat">Cerrar</a>
                    </div>
                  </li>
                </ul>
              </li>
              
            </ul>
          </div>

        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
                    
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header"></li>  
            <li class="treeview">
              <a href="#">
                <i class="fa fa-folder"></i> <span>Acceso</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <!-- @if(Auth::user()->id == 2 OR Auth::user()->id == 1 OR Auth::user()->id == 3 OR Auth::user()->id == 1195 OR Auth::user()->id == 93)
                    <li><a href="{{url('seguridad/usuario')}}"><i class="fa fa-circle-o"></i> Usuarios</a></li>
                @endif -->
                <li><a href="{{URL::action('UserProfileController@show', Auth::user()->numeroNom)}}"><i class="fa fa-circle-o"></i> Perfil de Usuario</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-suitcase"></i> <span>Solicitud de Viaje</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="{{url('travel/solicitud')}}"><i class="fa fa-circle-o"></i> Solicitud</a>
                <li><a href="{{url('travel/gasto')}}"><i class="fa fa-circle-o"></i> Gastos</a>
                <li><a href="{{url('travel/print/index')}}"><i class="fa fa-circle-o"></i><small class="label pull-right bg-red">PDF</small> Imprimir</a>
                </li>
              </ul>
            </li>
            <!-- @if(Auth::user()->id == 1 OR Auth::user()->id == 16 OR Auth::user()->id == 1195) -->
            <li>
              <a href="{{url('travel/reportes/reporte')}}">
                <i class="fa fa-file-text"></i> <span>Reporte Viajes</span>
              </a>
            </li>
            <!-- @endif -->
            
              <li class="treeview">
                <a href="#">
                  <i class="fa fa-archive"></i> <span>Administracion</span>
                  <i class="fa fa-angle-left pull-right"></i>                 
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{url('administracion/vuelos')}}"><i class="fa fa-circle-o"></i> Comprobación Vuelos</a></li>                  
                </ul>
              </li>
            
            @if(Auth::user()->id == 1 OR Auth::user()->id == 4 OR Auth::user()->id == 5 OR Auth::user()->id == 7 OR Auth::user()->id == 27 OR Auth::user()->id == 2260 OR Auth::user()->id == 2249 OR Auth::user()->id == 1190 OR Auth::user()->id == 1195 OR Auth::user()->id == 2196 OR Auth::user()->id == 2217 OR Auth::user()->id == 2230 OR Auth::user()->id == 2291 OR Auth::user()->id == 1038 OR Auth::user()->id == 2319)
              <li class="treeview">
                <a href="#">
                  <i class="fa fa-calculator"></i> <span>Contabilidad</span>
                  <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{url('accounting/comprobacion')}}"><i class="fa fa-circle-o"></i> Comprobación Gastos</a></li>
                  <li><a href="{{url('accounting/validados')}}"><i class="fa fa-circle-o"></i> Folios Validados</a></li>                 
                  <li><a href="{{url('accounting/preAnticipo')}}"><i class="fa fa-circle-o"></i> Pre-Anticipos</a></li>
                  <li><a href="{{url('accounting/statusFolios')}}"><i class="fa fa-circle-o"></i> Status Folios Pendientes</a></li>
                </ul>
              </li>
            @endif
              <li class="treeview">
                <a href="#">
                  <i class="fa fa-clipboard"></i> <span>Reportes</span>
                  <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{url('reports/reportes')}}"><i class="fa fa-circle-o"></i> Folios Mensuales</a></li>
                </ul>
              </li>
            @if(Auth::user()->id == 1 OR Auth::user()->id == 7 OR Auth::user()->id == 9 OR Auth::user()->id == 1195)
              <li class="treeview">
                <a href="#">
                  <i class="fa fa-tasks"></i> <span>Tesorería</span>
                  <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{url('treasury/anticipo')}}"><i class="fa fa-circle-o"></i> Anticipo</a></li>
                  <li><a href="{{url('treasury/rembolso')}}"><i class="fa fa-circle-o"></i> Rembolso</a></li>
                </ul>
              </li>
            @endif

            @if($userClaims[0]->Categoria <=4 OR Auth::user()->id == 1195)
              <li class="treeview">
                <a href="#">
                  <i class="fa fa-link"></i> <span>Aprobaciones</span>
                  <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                  <li><a href="{{url('authorizers/approbation')}}"><i class="fa fa-circle-o"></i> Pendientes</a></li>
                  @if(Auth::user()->id == 1 OR Auth::user()->id == 37 OR Auth::user()->id == 1195)
                  <li><a href="{{url('authorizers/evidencias')}}"><i class="fa fa-circle-o"></i> Reporte Evidencias de Viaje</a></li>
                @endif
                </ul>
              
              </li>
            @endif

            <li>
              <a href="#">
                <i class="fa fa-plus-square"></i> <span>Ayuda</span>
                <small class="label pull-right bg-red">PDF</small>
              </a>
              <ul class="treeview-menu">
                  <li><a target="_blank" href="{{asset('imagenes/manual/ADViaticos; Manual de usuario.pdf')}}" onclick=""><i class="fa fa-circle-o"></i> ADViaticos; Manual de usuario</a></li>
                  <li><a target="_blank" href="{{asset('imagenes/manual/ADViaticos; Users Manual.pdf')}}" onclick=""><i class="fa fa-circle-o"></i> ADViaticos; Users Manual</a></li>
                  <li><a target="_blank" href="{{asset('imagenes/manual/Politica de viaje de negocios  BUSINESS TRIP POLICY Ver 05.pdf')}}" onclick=""><i class="fa fa-circle-o"></i> Política de viaje</a></li>
                </ul>
            </li>
            <li>
              <a href="#">
                <i class="fa fa-info-circle"></i> <span>Acerca De...</span>
                <small class="label pull-right bg-yellow">IT</small>
              </a>
            </li>
                        
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>





       <!--Contenido-->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        
        <!-- Main content -->
        <section class="content">
          
          <div class="row">
            <div class="col-md-12">
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Sistema de Administración</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                  	<div class="row">
	                  	<div class="col-md-12">
		                          <!--Contenido-->
                              @yield('contenido')
		                          <!--Fin Contenido-->
                           </div>
                        </div>
		                    
                  		</div>
                  	</div><!-- /.row -->
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <!--Fin-Contenido-->
      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> 2.3.0
        </div>
        <strong>Copyright &copy; 2015-2020 <a href="www.incanatoit.com">IncanatoIT</a>.</strong> All rights reserved.
      </footer>

      
    <!-- jQuery 2.1.4 -->
    <script src="{{asset('js/jQuery-2.1.4.min.js')}}"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src ="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>  

    @stack('scripts')
    <!-- Bootstrap 3.3.5 -->
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-select.min.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{asset('js/app.min.js')}}"></script>
   
    <!-- SCRIPTS PARA BOTONES DATATABLES -->

    <script src="{{asset('DataTables\DataTables-1.10.18\js\jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('DataTables\Buttons-1.5.6\js\dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('DataTables\Buttons-1.5.6\js\buttons.bootstrap4.min.js')}}"></script>
    <script src="{{asset('DataTables\DataTables-1.10.18\js\dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('DataTables\JSZip-2.5.0\jszip.min.js')}}"></script>
    <script src="{{asset('DataTables\pdfmake-0.1.36\pdfmake.min.js')}}"></script>
    <script src="{{asset('DataTables\pdfmake-0.1.36\vfs_fonts.js')}}"></script>
    <script src="{{asset('DataTables\Buttons-1.5.6\js\buttons.html5.min.js')}}"></script>
    <script src="sweetalert2/dist/sweetalert2.min.js"></script>
  </body>
</html>
