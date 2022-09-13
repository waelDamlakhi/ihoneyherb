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
                    Rule::unique('department_translations', 'name')->ignore($this->id, 'department_id')
                ];
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
            'photo.mimetypes' => 'The Photo Extension Must Be One Of These (jpg, jpeg, png).',
            'photo.required' => 'The Category Photo Field Is Required.',
            'department_id.integer' => 'The Category Id Must Be a Integer.',
            'department_id.exists' => 'This Category Id Is Invalid.',
            'en.name.required' => 'The English Category Name Field Is Required.',
            'en.name.string' => 'The English Category Name Must Be a String.',
            'en.name.unique' => 'The English Category Name Has Already Been Taken.',
            'ar.name.required' => 'The Arabic Category Name Field Is Required.',
            'ar.name.string' => 'The Arabic Category Name Must Be a String.',
            'ar.name.unique' => 'The Arabic Category Name Has Already Been Taken.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'photo.mimetypes' => ' .(jpg, jpeg, png) يجب أن يكون امتداد الصورة احدى هذه الامتدادات',
            'photo.required' => 'صورة التصنيف مطلوبة.',
            'department_id.integer' => 'يجب أن يكون رقم التصنيف من نوع رقم صحيح.',
            'department_id.exists' => 'هذا الرقم غير صحيح.',
            'en.name.required' => 'اسم التصنيف بالأنكليزية مطلوب.',
            'en.name.string' => 'يجب أن يكون اسم التصنيف بالأنكليزية من نوع نصي.',
            'en.name.unique' => 'اسم التصنيف بالأنكليزية موجود مسبقا.',
            'ar.name.required' => 'اسم التصنيف بالعربية مطلوب.',
            'ar.name.string' => 'يجب أن يكون اسم التصنيف بالعربية من نوع نصي.',
            'ar.name.unique' => 'اسم التصنيف بالعربية موجود مسبقا.'
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
