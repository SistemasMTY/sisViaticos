<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    //
    protected $table='ssm_viat_header_token';

    protected $primaryKey='id_header_token';

    public $timestamps=false;


    protected $fillable=[
    	'id_header_folio',
    	'_token',

    ];

    protected $guarded = [

    ];
}
