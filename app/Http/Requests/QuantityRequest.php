<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        $rules = array();
        if (Str::contains($this->path(), 'delete-quantityAdjustmentOperation') || Str::contains($this->path(), 'edit-quantityAdjustmentOperation')) 
        {
            $rules['id'] = 'required|integer|exists:quantity_adjustments,id';
        }
        else 
        {
            $product = Product::select('id')->with(
                [
                    'unit' => function ($unit)
                    {
                        $unit->select('type');
                    }
                ]
            )->find($this->product_id);
            if (Str::contains($this->path(), 'update-quantityAdjustmentOperation'))
                $rules['id'] = 'required|integer|exists:quantity_adjustments,id';
            $rules += [
                'product_id' => 'required|integer|exists:products,id',
                'operation_type' => 'required|in:out,in',
                'description' => 'nullable',
                'quantity' => [
                    'required',
                    Rule::when($product->unit->type == 'decimal', 'numeric'),
                    Rule::when($product->unit->type == 'integer', 'integer')
                ]
            ];
        }
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
