<?php
    namespace App\Backend\Http\Requests;
    use App\Core\Common\UploadConst;
    use App\Core\Common\UserConst;
    use Illuminate\Support\Facades\Auth;

    class EditProfileRequest extends Request
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
            $rule_pass        = "";
            $rule_passConfirm = "";
            $user          = Auth::user();
            if((int ) $this->changePass === UserConst::changePass ){
                $rule_pass       = "required|min:4|max:32";
                $rule_passConfirm= "required|min:4|max:32|same:password";
            }
            $rule = [
                "image"                 => "bail|nullable|mimes:" .                  UploadConst::FILE_IMAGE_UPLOAD_ACCESSED . "|image|max:" . UploadConst::BACKEND_UPLOAD_IMAGE_MAX,
                "name"                  => "required|min:3|max:32",
                "date"                  => "required|date",
                "email"                 => "required|email|unique:users,email," .$user->id,
                "password"              => $rule_pass,
                "password_confirmation" => $rule_passConfirm,
            ];
            return $rule;
        }
        
        public  function messages()
        {
            return [
                '*.mimes' => trans('validation.mimes')
            ];
            // TODO: Change the autogenerated stub
        }
        public function attributes()
        {
            return [
                'image'                 => trans('label.image'),
                'name'                  => trans('label.name'),
                'date'                  => trans('label.birthdate'),
                "email"                 => trans('label.email'),
                "password"              => trans('label.password'),
                "password_confirmation" => trans('label.re_password'),
            ];
        }
    }