<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Traits\GeneralFunctions;

class QuantityRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-quantityAdjustmentOperation') || Str::contains($this->path(), 'edit-quantityAdjustmentOperation')) 
        {
            $rules['id'] = 'required|integer|exists:quantity_adjustments,id';
        }
        elseif (Str::contains($this->path(), 'quantityAdjustmentOperations'))
        {
            $rules['limit'] = 'required|integer';
        }
        else 
        {
            $product = Product::with('unit')->find($this->product_id);
            $rules = [
                'product_id' => 'required|integer|exists:products,id',
                'operation_type' => 'required|in:out,in',
                'description' => 'nullable',
                'quantity' => [
                    'required',
                    !is_null($product) ? ($product->unit->type == 'decimal' ? 'numeric' : 'integer') : ''
                ]
            ];
            if (Str::contains($this->path(), 'update-quantityAdjustmentOperation'))
                $rules['id'] = 'required|integer|exists:quantity_adjustments,id';
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
            'quantity.required' => __('ProductLang.TheQuantityFieldIsRequired'),
            'quantity.integer' => __('ProductLang.TheQuantityMustBeAnInteger'),
            'quantity.numeric' => __('ProductLang.TheQuantityMustBeANumeric'),
            'operation_type.in' => __('ProductLang.TheSelectedOperationTypeIsInvalid'),
            'operation_type.required' => __('ProductLang.TheOperationTypeFieldIsRequired'),
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
