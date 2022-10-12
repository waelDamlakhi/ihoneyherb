<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\GeneralFunctions;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BannerRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-banner') || Str::contains($this->path(), 'edit-banner')) 
        {
            $rules['id'] = 'required|integer|exists:banner_product,id';
        }
        else
        {
            if (Str::contains($this->path(), 'update-banner')) 
                $rules = [
                    'id' =>'required|integer|exists:banner_product,id',
                    'photo' => 'nullable|mimetypes:image/jpg,image/jpeg,image/png'
                ];
            else
                $rules['photo'] = 'required|mimetypes:image/jpg,image/jpeg,image/png';

            $rules += [
                'banner_id' => 'required|integer|exists:banners,id',
                'product_id' => [
                    'required',
                    'integer',
                    'exists:products,id',
                    Rule::unique('banner_product')->where('banner_id', $this->banner_id)->ignore($this->id)
                ]
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
            'product_id.required' => 'The Product Id Field Is Required.',
            'product_id.integer' => 'The Product Id Must Be a Integer.',
            'product_id.exists' => 'This Product Id Is Invalid.',
            'product_id.unique' => 'This Product Id Has Already Been Added To This Banner.',
            'photo.mimetypes' => 'The Product Banner Photo Extension Must Be One Of These (jpg, jpeg, png).',
            'photo.required' => 'The Product Photo Field Is Required.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'product_id.required' => 'رقم المنتج مطلوب.',
            'product_id.integer' => 'يجب أن يكون رقم المنتج من نوع رقم صحيح.',
            'product_id.exists' => 'هذا الرقم غير صحيح.',
            'product_id.unique' => 'تمت إضافة معرف المنتج هذا بالفعل إلى هذا الشعار.',
            'photo.mimetypes' => '.(jpg, jpeg, png) يجب أن يكون امتداد الصورة احدى هذه الامتدادات',
            'photo.required' => 'صورة لافتة المننج مطلوبة.'
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
