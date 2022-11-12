<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\GeneralFunctions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AddressRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-address') || Str::contains($this->path(), 'edit-address') || Str::contains($this->path(), 'set-address-default')) 
        {
            $rules['id'] = 
            [
                'required',
                'integer',
                Rule::exists('addresses', 'id')->where('user_id', $this->user_id)
            ];
        }
        else 
        {
            if (Str::contains($this->path(), 'update-address'))
                $rules = [
                    'id' => 
                    [
                        'required',
                        'integer',
                        Rule::exists('addresses', 'id')->where('user_id', $this->user_id)
                    ],
                    'address' => 
                    [
                        'required',
                        'string',
                        Rule::unique('addresses', 'address')->ignore($this->id)
                    ]
                ];
            else
                $rules['address'] = 'required|string|unique:addresses,address';
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
            'address.required' => 'The Address Field Is Required.',
            'address.string' => 'The Address Must Be a String.',
            'address.unique' => 'The Address Has Already Been Taken.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'address.required' => 'رقم الهاتف مطلوب.',
            'address.string' => 'يجب أن يكون رقم الهاتف من نوع نصي.',
            'address.unique' => 'رقم الهاتف موجود مسبقا.'
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
