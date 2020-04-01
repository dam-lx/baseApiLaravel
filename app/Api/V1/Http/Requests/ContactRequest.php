<?php

namespace App\Api\V1\Http\Requests;

use App\Api\V1\Http\Requests\Request as ApiRequest;
use App\Core\Common\UserConst;
use App\Core\ExtendValidationRules\TelRequiredWith;

class ContactRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {000-
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contents'  => 'required',
            'name01' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
            'name02' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
            'kana01' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
            'kana02' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
            'email'     => 'required|email|regex:'.UserConst::VALIDATE_EMAIL,
            "tel01"  => [new TelRequiredWith($this->request->all()),'max:5'],
            "tel02"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
            "tel03"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
            'zip01'     => 'nullable|max:3|min:3|required_with:zip02|regex:' . UserConst::VALIDATE_INTERGER,
            'zip02'     => 'nullable|max:4|min:4|required_with:zip01|regex:' . UserConst::VALIDATE_INTERGER,
            "company_name" =>'nullable|max:100'
        ];
    }

    public function messages()
    {
        return [
            "name01.regex"                  => trans('api.validate.space'),
            "name02.regex"                  => trans('api.validate.space'),
            "kana01.regex"        => trans('api.validate.kana'),
            "kana02.regex"        => trans('api.validate.kana'),
            "kana02.max"                    => trans('api.validate.field.max'),
            "kana01.max"                    => trans('api.validate.field.max'),
            "name02.max"                    => trans('api.validate.field.max'),
            "name01.max"                    => trans('api.validate.field.max'),
            "company_name.max"              => trans('api.validate.field.max'),
            "email.regex"         => trans('api.validate.email'),
            "email.email"         => trans('api.validate.email'),
            'tel01.max'           => trans('api.validate.tel.max'),
            'tel02.max'           => trans('api.validate.tel.max'),
            'tel03.max'           => trans('api.validate.tel.max'),
            'zip01.max'           => trans('api.validate.zip01'),
            'zip02.max'           => trans('api.validate.zip02'),
            'zip01.min'           => trans('api.validate.zip01'),
            'zip02.min'           => trans('api.validate.zip02'),
            'tel01.required_with' => trans('api.validate.input.full'),
            'tel02.required_with' => trans('api.validate.input.full'),
            'tel03.required_with' => trans('api.validate.input.full'),
            'zip01.required_with' => trans('api.validate.input.full'),
            'zip02.required_with' => trans('api.validate.input.full'),
            'zip01.regex:' . UserConst::VALIDATE_INTERGER         => trans('api.validate.interger'),
            'zip02.regex:' . UserConst::VALIDATE_INTERGER         => trans('api.validate.interger'),
            'tel01.regex:' . UserConst::VALIDATE_INTERGER         => trans('api.validate.interger'),
            'tel02.regex:' . UserConst::VALIDATE_INTERGER         => trans('api.validate.interger'),
            'tel03.regex:' . UserConst::VALIDATE_INTERGER         => trans('api.validate.interger'),
        ];
    }
}
