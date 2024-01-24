<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    //
    protected $table='ssm_viat_repayment';

    protected $primaryKey='id_repayment';

    public $timestamps=false;


    protected $fillable=[
        'company',
        'id_header_folio',
    	'fecha',

    ];

    protected $guarded = [

    ];
}
