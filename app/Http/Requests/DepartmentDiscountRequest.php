<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use App\Traits\GeneralFunctions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class DepartmentDiscountRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-department-discount') || Str::contains($this->path(), 'edit-department-discount')) 
        {
            $rules['id'] = 'required|integer|exists:department_discounts,id';
        }
        else 
        {
            if (Str::contains($this->path(), 'update-department-discount'))
                $rules['id'] = 'required|integer|exists:department_discounts,id';
            $rules += [
                'department_id' => 'required|integer|exists:departments,id',
                'discount' => 'required|numeric|max:100',
                'start' => 'required|date|after_or_equal:today',
                'end' => 'required|date|after:start',
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
            'department_id.required' => 'The Category Id Field Is Required.',
            'department_id.integer' => 'The Category Id Must Be a Integer.',
            'department_id.exists' => 'This Category Id Is Invalid.',
            'discount.required' => 'The Dicount Field Is Required.',
            'discount.numeric' => 'The Dicount Must Be a Numeric.',
            'discount.max' => 'The Discount Must Not Be Greater Than 100.',
            'start.required' => 'The Start Date Field Is Required.',
            'start.date' => 'The Start Date Must Be a Date.',
            'end.required' => 'The End Date Field Is Required.',
            'end.date' => 'The End Date Must Be a Date.',
            'start.after_or_equal' => 'The Start Date Must Be A Date After Or Equal To Today.',
            'end.after' => 'The End Date Must Be A Date After Start Date.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'department_id.required' => 'رقم التصنيف مطلوب.',
            'department_id.integer' => 'يجب أن يكون رقم التصنيف من نوع رقم صحيح.',
            'department_id.exists' => 'هذا الرقم غير صحيح.',
            'discount.required' => 'قيمة التخفيض مطلوب.',
            'discount.numeric' => 'يجب أن تكون قيمة التخفيض من نوع رقمي.',
            'discount.max' => 'لا يمكن أن تكون قيمة التخفيض أكبر من 100.',
            'start.required' => 'تاريخ البداية مطلوب.',
            'start.date' => 'يجب أن يكون تاريخ البداية من نوع تاريخ.',
            'end.required' => 'تاريخ النهاية مطلوب.',
            'end.date' => 'يجب أن يكون تاريخ النهاية من نوع تاريخ.',
            'start.after_or_equal' => 'يجب أن يكون تاريخ البداية أكبر أو يساوي تاريخ اليوم.',
            'end.after' => 'يجب أن يكون تاريخ النهاية أكبر من تاريخ البداية.'
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
