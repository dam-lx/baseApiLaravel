<?php

namespace App\Api\V1\Http\Requests;

use App\Core\Common\UserConst;
use Illuminate\Foundation\Http\FormRequest;

class GetAddressRequest extends FormRequest
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
            'zip01' => 'required_with:zip02|required|max:3|min:3|regex:' . UserConst::VALIDATE_INTERGER,
            'zip02' => 'required_with:zip01|required|max:4|min:4|regex:' . UserConst::VALIDATE_INTERGER,
        ];
    }
    public function messages()
    {
        return [
            'zip01.max'                     => trans('api.validate.zip01'),
            'zip02.max'                     => trans('api.validate.zip02'),
            'zip01.min'                     => trans('api.validate.zip01'),
            'zip02.min'                     => trans('api.validate.zip02'),
            'zip01.required_with'           => trans('api.validate.input.full'),
            'zip02.required_with'           => trans('api.validate.input.full'),
            'zip01.regex:' . UserConst::VALIDATE_INTERGER          => trans('api.validate.interger'),
            'zip02.regex:' . UserConst::VALIDATE_INTERGER          => trans('api.validate.interger'),
        ];
    }
}
