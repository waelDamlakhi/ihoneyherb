<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\GeneralFunctions;

class ProductRequest extends FormRequest
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
        $rules = [
            'AED' => 'required|numeric',
            'photo' => 'required|mimetypes:image/jpg,image/jpeg,image/png',
            'banner' => 'required|mimetypes:image/jpg,image/jpeg,image/png',
            'department_id' => 'nullable|integer|exists:departments,id',
        ];
        foreach (config('translatable.locales') as $lang) 
            $rules += [
                $lang . ".name" => [
                    'required',
                    'string',
                    Rule::unique('product_translations', 'name')->ignore($this->id, 'department_id')
                ],
                $lang . ".unit" => 'required|string',
                $lang . ".description" => 'required|string'
            ];
        return $rules;
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
