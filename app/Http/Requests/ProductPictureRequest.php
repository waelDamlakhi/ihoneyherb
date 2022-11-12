<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\GeneralFunctions;
use Illuminate\Support\Str;

class ProductPictureRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-product-pictures')) 
        {
            $rules['id'] = 'required|integer|exists:product_pictures,id';
        }
        else
        {
            if (Str::contains($this->path(), 'update-product-pictures')) 
            {
                $rules = [
                    'id' => 'required|integer|exists:product_pictures,id',
                    'photo' => 'required|mimetypes:image/jpg,image/jpeg,image/png'
                ];
            }
            else
            {
                $rules = [
                    'product_id' => 'required|integer|exists:products,id',
                    'photo.*' => 'required|mimetypes:image/jpg,image/jpeg,image/png'
                    ];
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
            'id.required' => __('ProductLang.TheIdFieldIsRequired'),
            'id.integer' => __('ProductLang.TheIdMustBeAnInteger'),
            'id.exists' => __('ProductLang.ThisIdIsInvalid'),
            'product_id.required' => __('ProductLang.TheProductIdFieldIsRequired'),
            'product_id.integer' => __('ProductLang.TheProductIdMustBeAnInteger'),
            'product_id.exists' => __('ProductLang.ThisProductIdIsInvalid'),
            'mimetypes' => __('ProductLang.The:attributeExtensionMustBeOneOfThese(jpg,jpeg,png)'),
            'photo.required' => __('ProductLang.TheProductPhotoFieldIsRequired'),
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
