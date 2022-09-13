<?php

namespace App\Http\Requests;

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
        if (Str::contains($this->path(), 'delete-product') || Str::contains($this->path(), 'edit-product')) 
        {
            $rules['id'] = 'required|integer|exists:products,id';
        }
        elseif (Str::contains($this->path(), 'update-product-pictures')) 
        {
            $rules = [
                'id' => 'required|integer|exists:product_pictures,id',
                'photo' => 'required|mimetypes:image/jpg,image/jpeg,image/png',
            ];
        }
        else
        {
            if (Str::contains($this->path(), 'update-product'))
                $rules = [
                    'id' => 'required|integer|exists:products,id',
                    'photo' => 'nullable|mimetypes:image/jpg,image/jpeg,image/png',
                ];
            else
            {
                $rules = [
                    'quantity' => 'required|integer',
                    'photo' => 'required|mimetypes:image/jpg,image/jpeg,image/png',
                    'otherPhoto.*' => 'mimetypes:image/jpg,image/jpeg,image/png'
                ];
            }
            $rules += [
                'AED' => 'required|numeric',
                'department_id' => 'nullable|integer|exists:departments,id'
            ];
            foreach (config('translatable.locales') as $lang) 
                $rules += [
                    $lang . ".name" => [
                        'required',
                        'string',
                        Rule::unique('product_translations', 'name')->ignore($this->id, 'product_id')
                    ],
                    $lang . ".unit" => 'required|string',
                    $lang . ".description" => 'required|string'
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
            'mimetypes' => 'The :attribute Extension Must Be One Of These (jpg, jpeg, png).',
            'photo.required' => 'The Product Photo Field Is Required.',
            'quantity.required' => 'The Quantity Field Is Required.',
            'quantity.integer' => 'The Quantity Must Be a integer.',
            'AED.required' => 'The Price Field Is Required.',
            'AED.numeric' => 'The Price Must Be a Numeric.',
            'department_id.integer' => 'The Department Id Must Be a Integer.',
            'department_id.exists' => 'This Department Id Is Invalid..',
            'en.name.required' => 'The English Product Name Field Is Required.',
            'en.name.string' => 'The English Product Name Must Be a String.',
            'en.name.unique' => 'The English Product Name Has Already Been Taken.',
            'ar.name.required' => 'The Arabic Product Name Field Is Required.',
            'ar.name.string' => 'The Arabic Product Name Must Be a String.',
            'ar.name.unique' => 'The Arabic Product Name Has Already Been Taken.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'mimetypes' => '.(jpg, jpeg, png) يجب أن يكون امتداد الصورة احدى هذه الامتدادات',
            'photo.required' => 'صورة المننج الرئيسية مطلوبة.',
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.integer' => 'يجب أن تكون الكمية من نوع رقم صحيح.',
            'AED.required' => 'السعر مطلوب.',
            'AED.numeric' => 'يجب أن تكون السعر من نوع رقمي.',
            'department_id.integer' => 'يجب أن يكون رقم التصنيف من نوع رقم صحيح.',
            'department_id.exists' => 'هذا الرقم غير صحيح.',
            'en.name.required' => 'اسم التصنيف بالأنكليزية مطلوب.',
            'en.name.string' => 'يجب أن يكون اسم المنتج بالأنكليزية من نوع نصي.',
            'en.name.unique' => 'اسم المنتج بالأنكليزية موجود مسبقا.',
            'ar.name.required' => 'اسم المنتج بالعربية مطلوب.',
            'ar.name.string' => 'يجب أن يكون اسم المنتج بالعربية من نوع نصي.',
            'ar.name.unique' => 'اسم المنتج بالعربية موجود مسبقا.'
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
