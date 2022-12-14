<?php

namespace App\Http\Requests;

use App\Models\DepartmentDiscount;
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
        if (Str::contains($this->path(), 'delete-department-discount') || Str::contains($this->path(), 'edit-department-discount')) 
        {
            $rules['id'] = 'required|integer|exists:department_discounts,id';
        }
        else 
        {
            $rules = [
                'department_id' => 'required|integer|exists:departments,id',
                'discount' => 'required|numeric|max:100',
                'end' => 'required|date|after:start',
            ];
            if (Str::contains($this->path(), 'update-department-discount'))
            {
                $departmentDiscount = DepartmentDiscount::selectRaw('IF(start < CURRENT_DATE, true, false) AS isStarted, start')->find($this->id);
                $rules += [
                    'id' => 'required|integer|exists:department_discounts,id',
                    'start' => [
                        'required',
                        'date',
                        is_object($departmentDiscount) ? ($departmentDiscount->isStarted ? 'date_equals:' . $departmentDiscount->start : 'after_or_equal:today') : ''
                    ]
                ];
            }
            else
                $rules['start'] = 'required|date|after_or_equal:today';
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
            'department_id.required' => __('CategoryLang.TheCategoryIdFieldIsRequired'),
            'department_id.integer' => __('CategoryLang.TheCategoryIdMustBeAInteger'),
            'department_id.exists' => __('CategoryLang.ThisCategoryIdIsInvalid'),
            'discount.required' => __('CategoryLang.TheDicountFieldIsRequired'),
            'discount.numeric' => __('CategoryLang.TheDicountMustBeANumeric'),
            'discount.max' => __('CategoryLang.TheDiscountMustNotBeGreaterThan100'),
            'start.required' => __('CategoryLang.TheStartDateFieldIsRequired'),
            'start.date' => __('CategoryLang.TheStartDateMustBeADate'),
            'end.required' => __('CategoryLang.TheEndDateFieldIsRequired'),
            'end.date' => __('CategoryLang.TheEndDateMustBeADate'),
            'start.after_or_equal' => __('CategoryLang.TheStartDateMustBeADateAfterOrEqualToToday'),
            'start.date_equals' => __('CategoryLang.TheStartDateMustBeADateEqualTo:date'),
            'end.after' => __('CategoryLang.TheEndDateMustBeADateAfterStartDate')
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
