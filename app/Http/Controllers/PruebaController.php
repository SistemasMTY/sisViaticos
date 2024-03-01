<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Folio;
use sisViaticos\FirmaFolio;
use sisViaticos\FirmaAnticipo;
use sisViaticos\FirmaGasto;
use sisViaticos\Transfer;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Mail;

use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class PruebaController extends Controller
{
    //
    public function __construct()
    {

    }

    public function enviarautorizador(Request $request)
    {
        // $response = Http::get('http://example.com');
        if ($request-> ajax())
        {
        //     $response = Http::post('http://170.1.2.33/enviarautorizador', [
        //         '_token' => '_token',
        //         'id' => 'id',
        //         'id_user' => 'id_user',
        //         'token' => 'token',
        //     ]);

        return response()->json([
            'validated' => true
        ]);
        }
    }
    


}
