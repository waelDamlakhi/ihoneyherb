<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\GeneralFunctions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class PhoneRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-phone') || Str::contains($this->path(), 'edit-phone') || Str::contains($this->path(), 'set-phone-default')) 
        {
            $rules['id'] = 
            [
                'required',
                'integer',
                Rule::exists('phones', 'id')->where('user_id', $this->user_id)
            ];
        }
        else 
        {
            if (Str::contains($this->path(), 'update-phone'))
                $rules = [
                    'id' => 
                    [
                        'required',
                        'integer',
                        Rule::exists('phones', 'id')->where('user_id', $this->user_id)
                    ],
                    'tel' => 
                    [
                        'required',
                        'numeric',
                        Rule::unique('phones', 'tel')->ignore($this->id)
                    ]
                ];
            else
                $rules['tel'] = 'required|numeric|unique:phones,tel';
        }

        return $rules;
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
            'id.required' => 'The Id Field Is Required.',
            'id.integer' => 'The Id Must Be a Integer.',
            'id.exists' => 'This Id Is Invalid.',
            'tel.required' => 'The Tel Field Is Required.',
            'tel.numeric' => 'The Tel Must Be a Numeric.',
            'tel.unique' => 'The Tel Has Already Been Taken.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'tel.required' => 'رقم الهاتف مطلوب.',
            'tel.numeric' => 'يجب أن يكون رقم الهاتف من نوع رقمي.',
            'tel.unique' => 'رقم الهاتف موجود مسبقا.'
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
