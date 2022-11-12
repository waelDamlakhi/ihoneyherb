<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\GeneralFunctions;
use Illuminate\Support\Str;

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
        if (Str::contains($this->path(), 'admin/api')) 
        {
            if (Str::contains($this->path(), 'products')) 
            {
                $rules['limit'] = 'required|integer';
            } 
            elseif (Str::contains($this->path(), 'delete-product') || Str::contains($this->path(), 'edit-product')) 
            {
                $rules['id'] = 'required|integer|exists:products,id';
            }
            else
            {
                $rules = [
                    'AED' => 'required|numeric',
                    'department_id' => 'nullable|integer|exists:departments,id',
                    'unit_id' => 'required|integer|exists:units,id',
                    'weight' => 'required|numeric'
                ];
                foreach (config('translatable.locales') as $lang) 
                    $rules += [
                        $lang . ".name" => [
                            'required',
                            'string',
                            Rule::unique('product_translations', 'name')->ignore($this->id, 'product_id')
                        ],
                        $lang . ".description" => 'required|string'
                    ];
                if (Str::contains($this->path(), 'update-product'))
                    $rules += [
                        'id' => 'required|integer|exists:products,id',
                        'photo' => 'nullable|mimetypes:image/jpg,image/jpeg,image/png',
                    ];
                else
                {
                    $unit = Unit::select('type')->find($this->unit_id);
                    $rules += [
                        'photo' => 'required|mimetypes:image/jpg,image/jpeg,image/png',
                        'otherPhoto.*' => 'mimetypes:image/jpg,image/jpeg,image/png',
                        'quantity' => [
                            'required',
                            is_object($unit) ? ($unit->type == 'decimal' ? 'numeric' : 'integer') : ''
                        ]
                    ];
                }
            }
        }
        elseif (Str::contains($this->path(), 'user/api')) 
        {
            $rules = [
                'product_id' => 'required|integer|exists:products,id',
                'rate' => 'required|integer|min:1|max:5',
                'comment' => 'required|string'
            ];
        }
        else 
        {
            if (Str::contains($this->path(), 'product-details'))
                $rules['id'] = 'required|integer|exists:products,id';
            else 
            {
                if (Str::contains($this->path(), 'products')) 
                    $rules = [
                        'limit' => 'required|integer',
                        'sort' => 'required|string|in:id,salesCount,rate,AED,SAR,USD',
                        'order' => 'required|string|in:DESC,ASC',
                        'categories' => 'nullable|array',
                        'categories.*' => 'nullable|integer|exists:departments,id',
                    ];
                $rules['search'] = 'nullable|string';
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
        return [
            'limit.integer' => __('ProductLang.ThelimitMustBeAnInteger'),
            'limit.required' => __('ProductLang.ThelimitFieldIsRequired'),
            'id.required' => __('ProductLang.TheIdFieldIsRequired'),
            'id.integer' => __('ProductLang.TheIdMustBeAnInteger'),
            'id.exists' => __('ProductLang.ThisIdIsInvalid'),
            'product_id.required' => __('ProductLang.TheProductIdFieldIsRequired'),
            'product_id.integer' => __('ProductLang.TheProductIdMustBeAnInteger'),
            'product_id.exists' => __('ProductLang.ThisProductIdIsInvalid'),
            'unit_id.required' => __('ProductLang.TheUnitIdFieldIsRequired'),
            'unit_id.integer' => __('ProductLang.TheUnitIdMustBeAnInteger'),
            'unit_id.exists' => __('ProductLang.ThisUnitIdIsInvalid'),
            'mimetypes' => __('ProductLang.The:attributeExtensionMustBeOneOfThese(jpg,jpeg,png)'),
            'photo.required' => __('ProductLang.TheProductPhotoFieldIsRequired'),
            'quantity.required' => __('ProductLang.TheQuantityFieldIsRequired'),
            'quantity.integer' => __('ProductLang.TheQuantityMustBeAnInteger'),
            'quantity.numeric' => __('ProductLang.TheQuantityMustBeANumeric'),
            'AED.required' => __('ProductLang.ThePriceFieldIsRequired'),
            'AED.numeric' => __('ProductLang.ThePriceMustBeANumeric'),
            'weight.required' => __('ProductLang.TheWeightFieldIsRequired'),
            'weight.numeric' => __('ProductLang.TheWeightMustBeANumeric'),
            'department_id.integer' => __('ProductLang.TheDepartmentIdMustBeAnInteger'),
            'department_id.exists' => __('ProductLang.ThisDepartmentIdIsInvalid'),
            'en.name.required' => __('ProductLang.TheEnglishProductNameFieldIsRequired'),
            'en.name.string' => __('ProductLang.TheEnglishProductNameMustBeAString'),
            'en.name.unique' => __('ProductLang.TheEnglishProductNameHasAlreadyBeenTaken'),
            'ar.name.required' => __('ProductLang.TheArabicProductNameFieldIsRequired'),
            'ar.name.string' => __('ProductLang.TheArabicProductNameMustBeAString'),
            'ar.name.unique' => __('ProductLang.TheArabicProductNameHasAlreadyBeenTaken'),
            'en.description.required' => __('ProductLang.TheEnglishProductDescriptionFieldIsRequired'),
            'en.description.string' => __('ProductLang.TheEnglishProductDescriptionMustBeAString'),
            'ar.description.required' => __('ProductLang.TheArabicProductDescriptionFieldIsRequired'),
            'ar.description.string' => __('ProductLang.TheArabicProductDescriptionMustBeAString'),
            'comment.required' => __('ProductLang.TheCommentFieldIsRequired'),
            'comment.string' => __('ProductLang.TheCommentMustBeAString'),
            'rate.required' => __('ProductLang.TheRateFieldIsRequired'),
            'rate.integer' => __('ProductLang.TheRateMustBeAnInteger'),
            'rate.max' => __('ProductLang.TheRateMustNotBeGreaterThan5'),
            'rate.min' => __('ProductLang.TheRateMustBeAtLeast1'),
            'sort.required' => __('ProductLang.TheSortFieldIsRequired'),
            'sort.string' => __('ProductLang.TheSortMustBeAString'),
            'sort.in' => __('ProductLang.TheSelectedSortIsInvalid'),
            'order.required' => __('ProductLang.TheOrderFieldIsRequired'),
            'order.string' => __('ProductLang.TheOrderMustBeAString'),
            'order.in' => __('ProductLang.TheSelectedOrderIsInvalid'),
            'categories.array' => __('ProductLang.TheCategoriesMustBeAnArray'),
            'categories.*.integer' => __('ProductLang.TheCategoriesIdsMustBeAnInteger'),
            'categories.*.exists' => __('ProductLang.ThisCategoriesIdsIsInvalid'),
            'search.string' => __('ProductLang.TheSearchMustBeAString'),
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
