<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return ['required', 'string', (new Password)->length(8)->requireUppercase()->requireNumeric()->requireSpecialCharacter(), 'confirmed'];
    }

    /**
     * Get the validation rules used to validate passwords in edit form.
     *
     * @return array
     */
    protected function passwordRulesEditForm()
    {
        return ['nullable', 'string', (new Password)->length(8)->requireUppercase()->requireNumeric()->requireSpecialCharacter(), 'confirmed'];
    }
}
