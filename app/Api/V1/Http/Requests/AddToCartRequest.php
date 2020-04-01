<?php

namespace App\Api\V1\Http\Requests;

use App\Api\V1\Http\Requests\Request as ApiRequest;
use App\Core\Helpers\ProductHelper;

class AddToCartRequest extends ApiRequest
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
        return ProductHelper::convertJsonRequest($this);
    }
    public function rules()
    {
        return [
            'class_category_id1'   => 'required',
            'class_category_id1.*' => 'required',
            'class_category_id2'   => 'required',
            'class_category_id2.*' => 'required',
            'delivery_date'        => 'required',
            'delivery_date.*'      => 'required|date',
            'stock'                => 'required',
            'stock.*'              => 'required|regex:' . UserConst::VALIDATE_INTERGER,
        ];
    }
    public function messages ()
    {
        return [
            "class_category_id1.*.required" => "入力されていません。",
            "class_category_id2.*.required" => "入力されていません。",
            "stock.required"                => "入力されていません。",
            "stock.*.required"              => "入力されていません。",
            "delivery_date.required"        => "選択されていません。",
            "delivery_date.*.required"      => "選択されていません。",
        ];
    }
    public function attributes ()
    {
        return [
            "stock.*" => "a"
        ];
    }
    
}
