<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\GeneralFunctions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class DepartmentRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-department') || Str::contains($this->path(), 'edit-department')) 
        {
            $rules['id'] = 'required|integer|exists:departments,id';
        }
        else 
        {
            if (Str::contains($this->path(), 'update-department'))
                $rules = [
                    'id' => 'required|integer|exists:departments,id',
                    'photo' => 'nullable|mimetypes:image/jpg,image/jpeg,image/png'
                ];
            else
                $rules['photo'] = 'required|mimetypes:image/jpg,image/jpeg,image/png';
    
            $rules['department_id'] = 'nullable|integer|exists:departments,id';
    
            foreach (config('translatable.locales') as $lang) 
                $rules[$lang . ".name"] = [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('department_translations', 'name')->ignore($this->id, 'department_id')
                ];
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->makeResponse("Failed", 422, "InVailed Inputs", $validator->errors());
        throw new ValidationException($validator, $response);
    }
}
