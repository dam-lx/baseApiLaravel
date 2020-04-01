<?php

namespace App\Api\V1\Http\Requests;

use App\Api\V1\Http\Requests\Request as ApiRequest;
use App\Core\Common\UserConst;

class GetPriceClassProductRequest extends ApiRequest
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
            'product_id'                  => 'required|regex:' . UserConst::VALIDATE_INTERGER,
            'class_category_id1'           => 'required|regex:' . UserConst::VALIDATE_INTERGER,
            'class_category_id2'           => 'required|regex:' . UserConst::VALIDATE_INTERGER,
        ];
    }
}
