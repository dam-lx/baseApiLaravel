<?php

namespace App\Api\V1\Http\Requests;

use App\Api\V1\Http\Requests\Request as ApiRequest;
use App\Core\Common\SysConst;
use App\Core\Common\UserConst;
use App\Core\ExtendValidationRules\TelRequiredWith;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'pref'                  => 'required|regex:' . UserConst::VALIDATE_INTERGER,
            'name01' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
            'name02' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
            'kana01' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
            'kana02' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
            'company_name'          => 'nullable|max:100',
            'zip01'                 => 'required_with:zip02|required|max:3|min:3',
            'zip02'                 => 'required_with:zip01|required|max:4|min:4',
            'addr01'                => 'required|max:32',
            'addr02'                => 'required|max:32',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            "tel01"  => [new TelRequiredWith($this->request->all()),'max:5'],
            "tel02"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
            "tel03"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
            "fax01" => 'nullable|max:5|required_with:fax02',
            "fax02" => 'nullable|max:5|required_with:fax03',
            "fax03" => 'nullable|max:5|required_with:fax01',
        ];
        $customerMail = $this->all()['email'];
        if($this->all()['email'] != Auth::user()->email){
            $rules['email'] = ['required','max:100','email','regex:'.UserConst::VALIDATE_EMAIL,
                Rule::unique('dtb_customer')->where(function ($query) use ($customerMail) {
                    return $query->where('email', $customerMail)
                        ->where('del_flg',SysConst::NOT_DEL_FLG);
                }),];
            $rules['email_confirmation']    = 'required|same:email';
        }else{
            $rules['email'] = 'required|string|max:100|email|regex:'.UserConst::VALIDATE_EMAIL;
            $rules['email_confirmation']    = 'required|same:email';
        }
        return $rules;
    }
    public function messages()
    {
        return [
            "name01.regex"                  => trans('api.validate.space'),
            "name02.regex"                  => trans('api.validate.space'),
            "email.regex"                   => trans('api.validate.email'),
            "email.email"                   => trans('api.validate.email'),
            "kana01.regex"                  => trans('api.validate.kana'),
            "kana02.regex"                  => trans('api.validate.kana'),
            "kana02.max"                    => trans('api.validate.field.max'),
            "kana01.max"                    => trans('api.validate.field.max'),
            "name02.max"                    => trans('api.validate.field.max'),
            "name01.max"                    => trans('api.validate.field.max'),
            "company_name.max"              => trans('api.validate.field.max'),
            'pref.required'                 => trans('api.pref.required'),
            'name01.required'               => trans('api.validate.required'),
            'name02.required'               => trans('api.validate.required'),
            'kana01.required'               => trans('api.validate.required'),
            'kana02.required'               => trans('api.validate.required'),
            'zip01.required'                => trans('api.validate.required'),
            'zip02.required'                => trans('api.validate.required'),
            'addr01.required'               => trans('api.validate.required'),
            'addr02.required'               => trans('api.validate.required'),
            'company_name.required'         => trans('api.validate.required'),
            'email.required'                => trans('api.validate.required'),
            'tel01.max'                     => trans('api.validate.tel.max'),
            'tel02.max'                     => trans('api.validate.tel.max'),
            'tel03.max'                     => trans('api.validate.tel.max'),
            'zip01.max'                     => trans('api.validate.zip01'),
            'zip02.max'                     => trans('api.validate.zip02'),
            'zip01.min'                     => trans('api.validate.zip01'),
            'zip02.min'                     => trans('api.validate.zip02'),
            "email_confirmation.same"       => trans('api.confirm.email'),
            'fax01.required_with'           => trans('api.validate.input.full'),
            'fax02.required_with'           => trans('api.validate.input.full'),
            'fax03.required_with'           => trans('api.validate.input.full'),
            'tel01.required_with'           => trans('api.validate.input.full'),
            'tel02.required_with'           => trans('api.validate.input.full'),
            'tel03.required_with'           => trans('api.validate.input.full'),
            'fax01.max'                     => trans('api.validate.tel.max'),
            'fax02.max'                     => trans('api.validate.tel.max'),
            'fax03.max'                     => trans('api.validate.tel.max'),
            'zip01.required_with'           => trans('api.validate.input.full'),
            'zip02.required_with'           => trans('api.validate.input.full'),
            "email.unique"                  => trans('api.user.exists'),
        ];
    }
}
