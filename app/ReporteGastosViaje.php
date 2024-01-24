<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class ReporteGastosViaje extends Model
{
    protected $table = 'VIEW_SSM_REPORTE_GASTOS';
    protected $fillable=[
        'Cuenta',
        'debe',
        'RFC',
        'proveedor',
        'id_user',
        'departamento',
        'id_header_folio',
        'gasto'

    ];
}
