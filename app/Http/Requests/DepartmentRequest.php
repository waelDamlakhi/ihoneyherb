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
        if (Str::contains($this->path(), 'admin/api')) 
        {
            if (Str::contains($this->path(), 'departments')) 
            {
                $rules['limit'] = 'required|integer';
            } 
            elseif (Str::contains($this->path(), 'delete-department') || Str::contains($this->path(), 'edit-department')) 
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
                        Rule::unique('department_translations', 'name')->ignore($this->id, 'department_id')
                    ];
            }
        }
        else
        {
            if (Str::contains($this->path(), 'parent-categories'))
            {
                $rules['limit'] = 'nullable|integer';
            }
            else 
            {
                $rules['department_id'] = 'required|integer|exists:departments,id';
            }
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
        return 
        [
            'id.required' => __('CategoryLang.TheIdFieldIsRequired'),
            'id.integer' => __('CategoryLang.TheIdMustBeAInteger'),
            'id.exists' => __('CategoryLang.ThisIdIsInvalid'),
            'photo.mimetypes' => __('CategoryLang.ThePhotoExtensionMustBeOneOfThese(jpg,jpeg,png)'),
            'photo.required' => __('CategoryLang.TheCategoryPhotoFieldIsRequired'),
            'department_id.required' => __('CategoryLang.TheCategoryIdFieldIsRequired'),
            'department_id.integer' => __('CategoryLang.TheCategoryIdMustBeAInteger'),
            'department_id.exists' => __('CategoryLang.ThisCategoryIdIsInvalid'),
            'en.name.required' => __('CategoryLang.TheEnglishCategoryNameFieldIsRequired'),
            'en.name.string' => __('CategoryLang.TheEnglishCategoryNameMustBeAString'),
            'en.name.unique' => __('CategoryLang.TheEnglishCategoryNameHasAlreadyBeenTaken'),
            'ar.name.required' => __('CategoryLang.TheArabicCategoryNameFieldIsRequired'),
            'ar.name.string' => __('CategoryLang.TheArabicCategoryNameMustBeAString'),
            'ar.name.unique' => __('CategoryLang.TheArabicCategoryNameHasAlreadyBeenTaken'),
            'limit.integer' => __('CategoryLang.TheLimitMustBeAInteger')
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
