<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class RegisterClientValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'min:3'],
            'last_name' => ['required', 'min:3'],
            'email' => ['required', 'email', 'unique:clients'],
            'phone' => ['required', 'min:8'],
            
        ];
    }


    public function messages(): array
    {
        return [

            'first_name.required' => 'Le champs nom est obligatoire.',
            'first_name.min' => 'Le champs nom doit compoter au minimum 3 caractéres.',

            'last_name.required' => 'Le champs prénom est obligatoire.',
            'last_name.min' => 'Le champs prénom doit compoter au minimum 3 caractéres.',

            'email.required' => 'Le champs email est obligatoire.',
            'email.email' => 'Le champs email doit etre un email.',
            'email.unique' => 'Email doit etre unique.',

            'phone.required' => 'Le champs phone est obligatoire.',
            'phone.min' => 'Le champs phone doit compoter au minimum 8 caractéres.',


        ];
    }
}
