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
                $rules['id'] = 'required|integer|exists:product_pictures,id';
            }
            else
            {
                $rules['product_id'] = 'required|integer|exists:products,id';
            }
            $rules['photo'] = 'required|mimetypes:image/jpg,image/jpeg,image/png';
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
            'product_id.required' => 'The Product Id Field Is Required.',
            'product_id.integer' => 'The Product Id Must Be a Integer.',
            'product_id.exists' => 'This Product Id Is Invalid.',
            'mimetypes' => 'The :attribute Extension Must Be One Of These (jpg, jpeg, png).',
            'photo.required' => 'The Product Photo Field Is Required.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'product_id.required' => 'رقم المنتج مطلوب.',
            'product_id.integer' => 'يجب أن يكون رقم المنتج من نوع رقم صحيح.',
            'product_id.exists' => 'هذا الرقم غير صحيح.',
            'mimetypes' => '.(jpg, jpeg, png) يجب أن يكون امتداد الصورة احدى هذه الامتدادات',
            'photo.required' => 'صورة المننج مطلوبة.'
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
