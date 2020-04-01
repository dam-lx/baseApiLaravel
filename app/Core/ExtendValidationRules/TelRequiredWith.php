<?php

namespace App\Core\ExtendValidationRules;

use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelRequiredWith implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $param;
    protected $required = false;
    protected $validateInt = false;
    public function __construct($param)
    {
        $this->param = $param;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $count = 0;
        if($value!= null && !preg_match(UserConst::VALIDATE_INTERGER,$value)){
            $this->validateInt = true;
            $count++;
        }
        if (!empty($this->param['tel01']) || !empty($this->param['tel02']) || !empty($this->param['tel03'])) {
            if (empty($this->param['tel01']) || empty($this->param['tel02']) || empty($this->param['tel03'])) {
                $count++;
            }
        }else if (empty($this->param['tel01']) && empty($this->param['tel02']) && empty($this->param['tel03']))  {
            $this->required = true;
            $count++;
        }
        if($count > 0) return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $mess = [];
        if($this->required == true) {
            $mess[] = trans('api.validate.required');
        }else{
            $mess[] = trans('api.validate.input.full');
        }
        if($this->validateInt == true) {
            $mess[] = trans('api.validate.integer');
        }
        return $mess;
    }
}
