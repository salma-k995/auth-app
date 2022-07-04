<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class RegisterValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed']
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Le champs nom est obligatoire.',
            'name.min' => 'Le champs nom doit compoter au minimum 3 caractéres.',
            'email.required' => 'Le champs email est obligatoire.',
            'email.email' => 'Le champs email doit etre un email.',
            'email.unique' => 'Email doit etre unique.',
            'password.required' => 'Le champs mot de passe est obligatoire.',
            'password.min' => 'Le champs mot de passe doit compoter au minimum 8 caractéres.',
            'password.confirmed' => 'Vérifier votre password.',
        ];
    }
}
