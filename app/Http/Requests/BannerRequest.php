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
        return
        [
            'id.required' => __('BannerLang.TheIdFieldIsRequired'),
            'id.integer' => __('BannerLang.TheIdMustBeAInteger'),
            'id.exists' => __('BannerLang.ThisIdIsInvalid'),
            'product_id.required' => __('BannerLang.TheProductIdFieldIsRequired'),
            'product_id.integer' => __('BannerLang.TheProductIdMustBeAInteger'),
            'product_id.exists' => __('BannerLang.ThisProductIdIsInvalid'),
            'product_id.unique' => __('BannerLang.ThisProductHasAlreadyBeenAddedToThisBanner'),
            'photo.mimetypes' => __('BannerLang.TheProductBannerPhotoExtensionMustBeOneOfThese(jpg,jpeg,png)'),
            'photo.required' => __('BannerLang.TheProductPhotoFieldIsRequired')
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
