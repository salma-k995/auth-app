<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class ResetPasswordValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            // TODO Add your validation rules
            'password'=> ['required' ,'min:8','confirmed']
        ];
    }

    public function messages():array {
        return [
            'password.required' => 'Le champs mot de passe est obligatoire.',
            'password.min' => 'Le champs mot de passe doit compoter au minimum 8 caractéres.',
            'password.confirmed' => 'Vérifier votre password.',
        ];

    }
}
