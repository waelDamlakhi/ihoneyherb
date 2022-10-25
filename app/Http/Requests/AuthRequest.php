<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\GeneralFunctions;

class AuthRequest extends FormRequest
{
    use GeneralFunctions;
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
        return Str::contains($this->path(), 'login') ? 
        [
            'userName' => 'required|string',
            'password' => 'required|string',
        ] : 
        [
            'name' => 'required|string',
            'userName' => 'required|string|unique:users,userName',
            'password' => 'required|string|min:8',
            'email' => 'required|email|unique:users,email',
            'tel' => 'required|numeric',
            'address' => 'required|string',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'userName.required' => __("AuthLang.TheUserNameFieldIsRequired"),
            'userName.string' => __("AuthLang.TheUserNameMustBeAString"),
            'userName.unique' => __("AuthLang.ThisUserNameHasAlreadyBeenTaken"),
            'password.required' => __("AuthLang.ThePasswordFieldIsRequired"),
            'password.string' => __("AuthLang.ThePasswordMustBeAString"),
            'name.required' => __("AuthLang.TheNameFieldIsRequired"),
            'name.string' => __("AuthLang.TheNameMustBeAString"),
            'email.required' => __("AuthLang.TheEmailAddressFieldIsRequired"),
            'email.email' => __("AuthLang.TheEmailMustBeAEmailAddress"),
            'email.unique' => __("AuthLang.ThisEmailAddressHasAlreadyBeenTaken"),
            'tel.required' => __("AuthLang.ThePhoneFieldIsRequired"),
            'tel.numeric' => __("AuthLang.ThePhoneMustBeANumeric"),
            'address.required' => __("AuthLang.TheAddressFieldIsRequired"),
            'address.string' => __("AuthLang.TheAddressMustBeAString")
        ];
    }

    /**
     * Return Validation Error Messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function failedValidation(Validator $validator)
    {
        $response = $this->makeResponse("Failed", 422, "InVailed Inputs", $validator->errors());
        throw new ValidationException($validator, $response);
    }
}
