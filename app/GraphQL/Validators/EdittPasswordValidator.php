<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class EdittPasswordValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'current-password' => 'required',
            'new-password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'current-password.required' => 'Le champs mot de passe courant est obligatoire.',
            'new-password.required' => 'Le champs mot de passe courant est obligatoire.',
            'new-password.min' => 'Le champs mot de passe doit compoter au minimum 8 caractéres.',
            'new-password.confirmed' => 'Vérifier votre password.',
            
        ];
    }
}
