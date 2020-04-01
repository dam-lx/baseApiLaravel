<?php

namespace App\Api\V1\Http\Requests;

use App\Api\V1\Http\Requests\Request as ApiRequest;
use App\Core\Common\UserConst;
use App\Core\ExtendValidationRules\TelRequiredWith;
use App\Core\Helpers\ProductHelper;
use Illuminate\Support\Facades\Auth;

class AddOrderRequest extends ApiRequest
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

    protected function validationData()
    {
        return ProductHelper::convertJsonRequestOrder($this);
    }

    public function rules()
    {
        $rules1 = $rules2 = [];
        if (empty($this->request->all()['order_id'])) {
            $rules2 = [
                'product_class_id'   => 'required',
                'product_class_id.*' => 'required',
                'delivery_date'      => 'required',
                'delivery_date.*'    => 'required|date',
                'stock'              => 'required',
                'stock.*'            => 'required|regex:' . UserConst::VALIDATE_INTERGER,
            ];
            if (!Auth::guard('api')->check()) {
                $rules1 = [
                    'pref'   => 'required',
                    'name01' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
                    'name02' => 'required|max:16|regex:'.UserConst::VALIDATE_SPACE,
                    "tel01"  => [new TelRequiredWith($this->request->all()['customer']),'max:5'],
                    "tel02"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
                    "tel03"  => 'nullable|max:5|regex:' . UserConst::VALIDATE_INTERGER,
                    'kana01' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
                    'kana02' => 'required|max:25|regex:' . UserConst::VALIDATE_KANA,
                    'zip01'  => 'required_with:zip02|required|min:3|max:3|regex:' . UserConst::VALIDATE_INTERGER,
                    'zip02'  => 'required_with:zip01|required|min:4|max:4|regex:' . UserConst::VALIDATE_INTERGER,
                    'addr01' => 'required|max:32',
                    'addr02' => 'required|max:32',
                    'email'  => 'required|email|regex:' . UserConst::VALIDATE_EMAIL,
                    'email_confirmation'        => 'required|same:email',
                    "company_name" =>'nullable|max:100',
                ];
            }
        }
        $rules = array_merge($rules1, $rules2);
        return $rules;
    }

    public function messages()
    {
        return [
            "name01.regex"                  => trans('api.validate.space'),
            "name02.regex"                  => trans('api.validate.space'),
            "kana01.regex"                  => trans('api.validate.kana'),
            "kana02.regex"                  => trans('api.validate.kana'),
            "kana02.max"                    => trans('api.validate.field.max'),
            "kana01.max"                    => trans('api.validate.field.max'),
            "name02.max"                    => trans('api.validate.field.max'),
            "name01.max"                    => trans('api.validate.field.max'),
            "company_name.max"              => trans('api.validate.field.max'),
            "email.regex"                   => trans('api.validate.email'),
            "email.email"                   => trans('api.validate.email'),
            'pref.required'                 => trans('api.pref.required'),
            'tel01.max'                     => trans('api.validate.tel.max'),
            'tel02.max'                     => trans('api.validate.tel.max'),
            'tel03.max'                     => trans('api.validate.tel.max'),
            'zip01.max'                     => trans('api.validate.zip01'),
            'zip02.max'                     => trans('api.validate.zip02'),
            'zip01.min'                     => trans('api.validate.zip01'),
            'zip02.min'                     => trans('api.validate.zip02'),
            "product_class_id.*.required"   => "入力されていません。",
            "stock.required"                => "入力されていません。",
            "stock.*.required"              => "入力されていません。",
            "delivery_date.required"        => "選択されていません。",
            "delivery_date.*.required"      => "選択されていません。",
            'tel01.required_with'           => trans('api.validate.input.full'),
            'tel02.required_with'           => trans('api.validate.input.full'),
            'tel03.required_with'           => trans('api.validate.input.full'),
            'fax01.max'                     => trans('api.validate.tel.max'),
            'fax02.max'                     => trans('api.validate.tel.max'),
            'fax03.max'                     => trans('api.validate.tel.max'),
            'tel01.regex'                   => trans('api.validate.interger'),
            'tel02.regex'                   => trans('api.validate.interger'),
            'tel03.regex'                   => trans('api.validate.interger'),
            'fax01.regex'                   => trans('api.validate.interger'),
            'fax02.regex'                   => trans('api.validate.interger'),
            'fax03.regex'                   => trans('api.validate.interger'),
            'zip01.regex'                   => trans('api.validate.interger'),
            'zip02.regex'                   => trans('api.validate.interger'),
            'zip01.required_with'           => trans('api.validate.input.full'),
            'zip02.required_with'           => trans('api.validate.input.full'),
            "class_category_id1.*.required" => "入力されていません。",
            "class_category_id2.*.required" => "入力されていません。",
            "email_confirmation.same"       => trans('api.confirm.email'),

        ];
    }
}
