<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\GeneralFunctions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UnitRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-unit') || Str::contains($this->path(), 'edit-unit')) 
        {
            $rules['id'] = 'required|integer|exists:units,id';
        }
        else 
        {
            if (Str::contains($this->path(), 'update-unit'))
                $rules['id'] = 'required|integer|exists:units,id';
            $rules['type'] = 'required|in:decimal,integer';
    
            foreach (config('translatable.locales') as $lang) 
                $rules[$lang . ".name"] = [
                    'required',
                    'string',
                    Rule::unique('unit_translations', 'name')->ignore($this->id, 'unit_id')
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
        return 
        [
            'id.required' => __('UnitLang.TheIdFieldIsRequired'),
            'id.integer' => __('UnitLang.TheIdMustBeAInteger'),
            'id.exists' => __('UnitLang.ThisIdIsInvalid'),
            'type.required' => __('UnitLang.TheUnitTypeFieldIsRequired'),
            'type.string' => __('UnitLang.TheUnitTypeMustBeAString'),
            'type.in' => __('UnitLang.TheUnitTypeMustBeOneOfThese(decimal,integer)'),
            'en.name.required' => __('UnitLang.TheEnglishUnitNameFieldIsRequired'),
            'en.name.string' => __('UnitLang.TheEnglishUnitNameMustBeAString'),
            'en.name.unique' => __('UnitLang.TheEnglishUnitNameHasAlreadyBeenTaken'),
            'ar.name.required' => __('UnitLang.TheArabicUnitNameFieldIsRequired'),
            'ar.name.string' => __('UnitLang.TheArabicUnitNameMustBeAString'),
            'ar.name.unique' => __('UnitLang.TheArabicUnitNameHasAlreadyBeenTaken')
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
