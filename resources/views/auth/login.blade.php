@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Login</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="emailNom" class="col-sm-4 col-form-label text-md-right">Iniciar Sesion con:</label>
                                <div class="col-md-6">
                                    <select name="EmNom" id= "EmNom" class="form-control selectpicker" onchange="javascript:RequiredEmail()">        
                                        <option value="email">Correo Electronico</option>
                                        <option value="NumNom">Numero de Nomina</option>                
                                    </select>
                                </div>
                            </div>
                            <div id="BranchSelect" style="display: none;" >
                                <div class="form-group row" >
                                    <label for="Branch" class="col-sm-4 col-form-label text-md-right">Compañia</label>
                                    <div class="col-md-6">
                                        <select name="Branchid" id= "Branchid" class="form-control selectpicker">        
                                            <option value="MTY">MTY</option>
                                            <option value="QRO">QRO</option> 
                                            <option value="SLM">SLM</option>               
                                        </select>
                                    </div>
                                </div>
                            </div>          
                            <div id="labelemail" style="display: block;" >                  
                                <div class="form-group row">                            
                                    <label for="email" class="col-sm-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required >
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div id="labelNumNom" style="display: none;" >      
                                <div class="form-group row">                            
                                    <label for="numeroNom" class="col-sm-4 col-form-label text-md-right">Numero Nomina</label>
                                    <div class="col-md-6">
                                        <input id="numeroNom" type="number" class="form-control{{ $errors->has('numeroNom') ? ' is-invalid' : '' }}" name="numeroNom" value="{{ old('numeroNom') }}" required>
                                        @if ($errors->has('numeroNom'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('numeroNom') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- jQuery 2.1.4 -->
<script src="{{asset('js/jQuery-2.1.4.min.js')}}"></script>
<!-- SweetAlert2 -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src ="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>  
<!-- Bootstrap 3.3.5 -->
<script src="{{asset('js/bootstrap.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('js/app.min.js')}}"></script>
<script>
    $(document).ready(function(){
        document.getElementById("BranchSelect").required = false;
        document.getElementById("numeroNom").required = false;
        document.getElementById("email").required = true;
    });
    //INICIO DE FUNCIONES DE REQUERIMIENTOS FORMULARIOS//
	function RequiredEmail(){		
        //alert($('#EmNom').val());
        ValorNomEm = $('#EmNom').val();
		elementBranch = document.getElementById("BranchSelect");
        elementEmail = document.getElementById("labelemail");
        elementNomina = document.getElementById("labelNumNom");
        if(ValorNomEm == 'NumNom'){
            elementBranch.style.display = 'block';
            elementEmail.style.display = 'none';
            elementNomina.style.display = 'block';
            document.getElementById("BranchSelect").required = true;
            document.getElementById("numeroNom").required = true;
            document.getElementById("email").required = false;

        }
        if(ValorNomEm == 'email'){
            elementBranch.style.display = 'none';
            elementEmail.style.display = 'block';
            elementNomina.style.display = 'none';
            document.getElementById("BranchSelect").required = false;
            document.getElementById("numeroNom").required = false;
            document.getElementById("email").required = true;
        }
    }
</script>
@endsection

