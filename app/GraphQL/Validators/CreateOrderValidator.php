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
            'reference' => ['required', 'min:6'],
            'products' => ['exists:products,id']
        ];
    }

    public function messages(): array
    {
        return [
            'reference.required' => 'Le champs reference est obligatoire.',
            'reference.min' => 'Le champs reference doit comporter au minimum 6 caractéres.',

            'products' =>'Choisir des produits existants'
        ];
    }
}
