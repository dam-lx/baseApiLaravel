<?php

namespace App\Api\V1\Http\Requests;

use App\Api\V1\Http\Requests\Request as ApiRequest;
use App\Core\Common\UserConst;
use App\Core\ExtendValidationRules\TelRequiredWith;

class CreateShippingAddressRequest extends ApiRequest
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
        return [
            'pref'             => 'required',
            'name01' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
            'name02' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
            'kana01' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
            'kana02' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
            'zip01'            => 'required_with:zip02|required|max:3|min:3|regex:' . UserConst::VALIDATE_INTERGER,
            'zip02'            => 'required_with:zip01|required|max:4|min:4|regex:' . UserConst::VALIDATE_INTERGER,
            'addr01'           => 'required|max:32',
            'addr02'           => 'required|max:32',
            "tel01"  => [new TelRequiredWith($this->request->all()),'max:5'],
            "tel02"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
            "tel03"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
            "fax01"            => 'nullable|max:5|required_with:fax02|regex:' . UserConst::VALIDATE_INTERGER,
            "fax02"            => 'nullable|max:5|required_with:fax03|regex:' . UserConst::VALIDATE_INTERGER,
            "fax03"            => 'nullable|max:5|required_with:fax01|regex:' . UserConst::VALIDATE_INTERGER,
            "company_name" =>'nullable|max:100'

        ];
    }
    public function messages()
    {
        return [
            "kana01.regex"                  => trans('api.validate.kana'),
            "kana02.regex"                  => trans('api.validate.kana'),
            'pref.required'                 => trans('api.pref.required'),
            'name01.required'               => trans('api.validate.required'),
            'name02.required'               => trans('api.validate.required'),
            "name01.regex"                  => trans('api.validate.space'),
            "name02.regex"                  => trans('api.validate.space'),
            'kana01.required'               => trans('api.validate.required'),
            'kana02.required'               => trans('api.validate.required'),
            "kana02.max"                    => trans('api.validate.field.max'),
            "kana01.max"                    => trans('api.validate.field.max'),
            "name02.max"                    => trans('api.validate.field.max'),
            "name01.max"                    => trans('api.validate.field.max'),
            "company_name.max"              => trans('api.validate.field.max'),
            'zip01.required'                => trans('api.validate.required'),
            'zip02.required'                => trans('api.validate.required'),
            'addr01.required'               => trans('api.validate.required'),
            'addr02.required'               => trans('api.validate.required'),
            'tel01.max'                     => trans('api.validate.tel.max'),
            'tel02.max'                     => trans('api.validate.tel.max'),
            'tel03.max'                     => trans('api.validate.tel.max'),
            'zip01.max'                     => trans('api.validate.zip01'),
            'zip02.max'                     => trans('api.validate.zip02'),
            'zip01.min'                     => trans('api.validate.zip01'),
            'zip02.min'                     => trans('api.validate.zip02'),
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
            'tel01.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
            'tel02.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
            'tel03.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
            'fax01.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
            'fax02.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
            'fax03.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
            'zip01.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
            'zip02.regex:' . UserConst::VALIDATE_INTERGER                   => trans('api.validate.interger'),
        ];
    }

}
