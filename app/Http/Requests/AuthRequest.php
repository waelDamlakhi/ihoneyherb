<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use App\Traits\GeneralFunctions;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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
        $rules = array();
        if (Str::contains($this->path(), 'login')) 
            $rules = [
                'userName' => 'required|string',
                'password' => 'required|string',
            ];
        elseif (Str::contains($this->path(), 'register'))
            $rules = [
                'name' => 'required|string',
                'userName' => 'required|string|unique:users,userName',
                'password' => 'required|string|min:8',
                'email' => 'required|email|unique:users,email',
                'tel' => 'required|numeric',
                'address' => 'required|string',
            ];
        elseif (Str::contains($this->path(), 'change-password')) 
        {
            $rules['password'] = 'required|string|min:8';
        }
        elseif (Str::contains($this->path(), 'forget-password')) 
            $rules['email'] = [
                'required',
                'email',
                function ($attribute, $value, $fail) 
                {
                    $guards = array_slice(config('auth.guards'), 0, count(config('auth.guards')) - 1);
                    foreach ($guards as $guard => $attributes)
                        if ($user = DB::table($attributes['provider'])->where('email', $value)->first())
                        {
                            $this->merge(
                                [
                                    'guard' => $guard,
                                    'user' => $user
                                ]
                            );
                            break;
                        }
                    if (!$user)
                        $fail(__('AuthLang.ThisEmailIsNotExist'));
                }
            ];
        return $rules;
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
            'password.min' => __("AuthLang.ThePasswordMustBeAtLeast8Characters"),
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
