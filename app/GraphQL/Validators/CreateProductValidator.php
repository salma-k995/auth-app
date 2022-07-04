<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class CreateProductValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3'],
            'description' => ['required', 'min:3'],
            'price' => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le champs nom est obligatoire.',
            'name.min' => 'Le champs nom doit compoter au minimum 3 caractéres.',
            'description.required' => 'Le champs description est obligatoire.',
            'description.min' => 'Le champs nom doit compoter au minimum 10 caractéres.',
            'price.required' => 'Le champs prix est obligatoire.',

        ];
    }
}
