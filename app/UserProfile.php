<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    //

    protected $table='user_profile';

    protected $primaryKey='id_user_profile';

    public $timestamps=false;


    protected $fillable=[
    	'id_user',
    	'numeroNom', 	
    	'nombre',
    	'imss',
    	'rfc',
    	'curp',
        'Ingreso',
    	'puesto',
        'CentroGastoID',
        'departamento',
    	'banco',
    	'clabe	',
    	'cuenta',
        'auto_1',
        'auto_2',
        'auto_3',
        'lvl',
    	'BRH',
        'usuarioUpdate',
    	'TimeStampMod',
    	'TimeStampAlta',
    ];

    protected $guarded = [


    ];
}
