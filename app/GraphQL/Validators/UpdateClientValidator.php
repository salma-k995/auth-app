<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateClientValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['min:3'],
            'last_name' => ['min:3'],
            'email' => ['email'],
            'phone' => ['min:8'],

        ];
    }

    public function messages(): array
    {

        return [
            'first_name.min' => 'Le champs nom doit compoter au minimum 3 caractéres.',

            'last_name.min' => 'Le champs prénom doit compoter au minimum 3 caractéres.',

            'email.email' => 'Le champs email doit etre un email.',

            'phone.min' => 'Le champs phone doit compoter au minimum 8 caractéres.',
        ];
    }
}
