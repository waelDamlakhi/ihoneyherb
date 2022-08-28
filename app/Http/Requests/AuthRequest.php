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
        return app()->getLocale() == 'en' ? 
        [
            'userName.required' => 'The User Name Field Is Required.',
            'userName.string' => 'The User Name Must Be a String.',
            'userName.unique' => 'The User Name Has Already Been Taken.',
            'password.required' => 'The Password Field Is Required.',
            'password.string' => 'The Password Must Be a String.',
            'name.required' => 'The Name Field Is Required.',
            'name.string' => 'The Name Must Be a String.',
            'email.required' => 'The Email Address Field Is Required.',
            'email.email' => 'The Email Must Be a Email Address.',
            'email.unique' => 'The Email Address Has Already Been Taken.',
            'tel.required' => 'The Phone Field Is Required.',
            'tel.numeric' => 'The Phone Must Be a Numeric.',
            'address.required' => 'The Address Field Is Required.',
            'address.string' => 'The Address Must Be a String.'
        ] : 
        [
            'userName.required' => 'اسم المستخدم مطلوب.',
            'userName.string' => 'يجب أن يكون اسم المستخدم نصي.',
            'userName.unique' => 'اسم المستخدم محجوز مسبقا.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string' => 'يجب أن تكون كلمة المرور نصية.',
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'يجب أن يكون الاسم نصي.',
            'email.required' => 'البريد الالكتروني مطلوب',
            'email.email' => 'يجب أن يكون البريد الالكتروني صحيح.',
            'email.unique' => 'البريد الالكتروني محجوز مسبقا.',
            'tel.required' => 'الهاتف مطلوب.',
            'tel.numeric' => 'يجب أن يكون الهاتف رقمي.',
            'address.required' => 'العنوان مطلوب.',
            'address.string' => 'يجب أن يكون العنوان نصي.'
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
