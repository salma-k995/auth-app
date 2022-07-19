<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class CreateOrderValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'reference' => ['required', 'min:6',' unique:orders,reference'],
            'products.*.id' => ['required'],
            'products.*.quantity' => ['gt:0','required',]
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required' => 'Le champs reference est obligatoire.',
            'reference.min' => 'Le champs reference doit comporter au minimum 6 caractéres.',
            'reference.unique' => 'Le champs reference doit etre unique.',
            'products.*.quantity.required' => 'Le champs quantité est obligatoire.',
            'products.*.quantity.gt' => 'passer au moins un produit .',
        ];
    }
}
