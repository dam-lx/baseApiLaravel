<?php

namespace App\Api\V1\Http\Requests;

use App\Api\V1\Http\Requests\Request as ApiRequest;

class PaymentRequest extends ApiRequest
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
            'card_no'        => 'required|regex:/^[0-9]+$/|digits:16',
            'card_exp_month' => 'required|regex:/^[0-9]+$/|max:2',
            'card_exp_year'  => 'required|regex:/^[0-9]+$/|max:4',
            'sec_cd'         => 'required|regex:/^[0-9]+$/|max:4',
        ];
    }
    public function messages ()
    {
        return [
            'card_no.regex'           => "ハイフンは間に入れず、番号のみを入力してください",
            'card_no.digits'          => "正しいカードを入力してください",
            'card_exp_month.required' => "カード有効期限/月を入力してください",
            'card_exp_year.required'  => "カード有効期限/年を入力してください",
        ];
    }
}
