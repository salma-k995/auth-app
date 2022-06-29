<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateProductValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name'=> ['min:3'],
            'description'=> ['min:10'],
            'price'=> ['numeric']
        ];
    }

    public function messages():array {
        return [
        'name.min' => 'Le champs nom doit compoter au minimum 3 caractéres.',
        'description.min' => 'Le champs nom doit compoter au minimum 10 caractéres.',
        'price.numeric' => 'Vérifier le prix.',
        ];

    }
}
