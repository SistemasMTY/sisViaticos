<?php

namespace sisViaticos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FolioFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        
        'tipo'=>'required',
        'destino'=>'required|max:100',
        'proposito'=>'required|max:200',
        'fecha_salida'=>'required',
        'fecha_llegada'=>'required',
        'criterio'=>'max:255',
        'id_moneda'=>'required',
        'anticipo'=>'required',
        ];
    }
}
