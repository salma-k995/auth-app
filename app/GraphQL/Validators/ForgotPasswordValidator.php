<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class ForgotPasswordValidator extends Validator
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
            'email'=> ['required' ,'email'],
        ];
    }

    public function messages():array {
        return [
        'email.required' => 'Le champs email est obligatoire.',
        'email.email' => 'Le champs email doit etre un email.',
        ];
    }
}
