<?php

namespace App\Http\Requests\Ventas;

use Illuminate\Foundation\Http\FormRequest;
// Laravel 13: atributo en Form Requests también disponible
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class GuardarVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'id_tido'                     => ['required','integer'],
            'id_tipo_pago'                => ['required','integer'],
            'fecha'                       => ['required','date'],
            'fecha_vencimiento'           => ['nullable','date','gte:fecha'],
            'id_cliente'                  => ['required','integer'],
            'total'                       => ['required','numeric','min:0.01'],
            'subtotal'                    => ['nullable','numeric'],
            'igv'                         => ['nullable','numeric'],
            'apli_igv'                    => ['nullable','in:0,1'],
            'direccion'                   => ['nullable','string','max:220'],
            'observacion'                 => ['nullable','string','max:220'],
            'metodo_pago'                 => ['nullable','integer'],
            'dias_pagos'                  => ['nullable','string'],

            'productos'                   => ['required','array','min:1'],
            'productos.*.id_producto'     => ['required','integer'],
            'productos.*.descripcion'     => ['required','string','max:245'],
            'productos.*.cantidad'        => ['required','numeric','min:0.001'],
            'productos.*.precio'          => ['required','numeric','min:0'],
            'productos.*.total'           => ['required','numeric','min:0'],
            'productos.*.igv_prod'        => ['nullable','integer'],
            'productos.*.descuento'       => ['nullable','numeric'],

            'lista_pagos'                 => ['nullable','array'],
            'lista_pagos.*.fecha'         => ['required_with:lista_pagos','date'],
            'lista_pagos.*.monto'         => ['required_with:lista_pagos','numeric','min:0'],
            'lista_pagos.*.tipo_pago'     => ['nullable','string','max:50'],
            'lista_pagos.*.pagado'        => ['nullable','boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_tido.required'        => 'Selecciona el tipo de comprobante.',
            'id_tipo_pago.required'   => 'Selecciona la forma de pago.',
            'fecha.required'          => 'La fecha de emisión es obligatoria.',
            'id_cliente.required'     => 'Selecciona un cliente.',
            'total.required'          => 'El total es obligatorio.',
            'total.min'               => 'El total debe ser mayor a 0.',
            'productos.required'      => 'Agrega al menos un producto.',
            'productos.min'           => 'Agrega al menos un producto.',
            'fecha_vencimiento.gte'   => 'La fecha de vencimiento debe ser igual o posterior a la emisión.',
        ];
    }

    /** Laravel 13: respuesta JSON automática para API requests */
    protected function failedValidation(Validator $validator): never
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'res'     => false,
                    'message' => 'Error de validación.',
                    'errors'  => $validator->errors(),
                ], 422)
            );
        }
        parent::failedValidation($validator);
    }
}
