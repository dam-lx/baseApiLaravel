<?php
namespace App\Core\ExtendValidationRules;

use Illuminate\Contracts\Validation\Rule;

class ValidateSpaceJapanRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $size = strlen($value) - 1;
        if($value[0] == "　" || $value[$size] == "　"){
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.has_spacejp');
    }
}
