<?php

namespace sisViaticos\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetalleFolioRequest extends FormRequest
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

            'id_header_folio',
            'fecha_gasto',
            'tipo',
            'gasto_tarjeta',
            'gasto_efectivo',
            'viatico'
        ];
    }
}
