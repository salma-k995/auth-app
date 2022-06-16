<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class ConfirmCodeValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'token'=> ['required']
        ];
    }

    public function messages():array {
        return [
            'token.required' => 'Le champs confirme code est obligatoire.',

        ];

    }
}
