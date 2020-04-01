<?php
namespace App\Core\ExtendValidationRules;

use App\Core\Dao\SDB;
use Illuminate\Contracts\Validation\Rule;

class HasUsernameRule implements Rule
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
        $countUser = SDB::table('users')->where('user_name', $value)->count();
        if($countUser <= 0){
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
        return trans('validation.check_has_username');
    }
}
