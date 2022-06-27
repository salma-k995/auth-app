<?php

namespace App\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateUserImageValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'mimes:png,jpeg,gif,jpg', 'max:8MB' ]
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'image est obligatoire.',
            'url.mimes' => 'image doit etre de type jpg png jpeg ou gif.',
            'url.max' => 'Le taille de image ne doit pas passer 8mb .',
        ];
    }
}
