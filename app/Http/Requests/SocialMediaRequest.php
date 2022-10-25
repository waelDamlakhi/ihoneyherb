<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\GeneralFunctions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class SocialMediaRequest extends FormRequest
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
        if (Str::contains($this->path(), 'delete-socialMedia') || Str::contains($this->path(), 'edit-socialMedia')) 
        {
            $rules['id'] = 'required|integer|exists:social_media,id';
        }
        else 
        {
            if (Str::contains($this->path(), 'update-socialMedia'))
                $rules = [
                    'id' => 'required|integer|exists:social_media,id',
                    'photoName' => 'nullable|string'
                ];
            else
                $rules['photoName'] = 'required_without:photo|string';
            $rules += [
                'photo' => 'nullable|mimetypes:image/svg',
                'type' => 'required|string|in:tel,email,application',
                'info' => [
                    'required',
                    Rule::when($this->type === 'tel', 'numeric'),
                    Rule::when($this->type === 'email', 'email'),
                    Rule::when($this->type === 'application', 'string'),
                    Rule::unique('social_media')->where(function ($query) {
                        return $query->where(['type' => $this->type, 'info' => $this->info]);
                    })->ignore($this->id)
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
            'id.required' => __('SocialMediaLang.TheIdFieldIsRequired'),
            'id.integer' => __('SocialMediaLang.TheIdMustBeAInteger'),
            'id.exists' => __('SocialMediaLang.ThisIdIsInvalid'),
            'photo.mimetypes' => __('SocialMediaLang.ThePhotoExtensionMustBeSVG'),
            'photoName.required_without' => __('SocialMediaLang.TheSocialMediaPhotoFieldIsRequired'),
            'photoName.string' => __('SocialMediaLang.TheSocialMediaPhotoNameMustBeAString'),
            'type.required' => __('SocialMediaLang.TheSocialMediaTypeFieldIsRequired'),
            'type.string' => __('SocialMediaLang.TheSocialMediaTypeMustBeAString'),
            'type.in' => __('SocialMediaLang.TheSocialMediaTypeMustBeOneOfThese(tel,email,application)'),
            'info.required' => __('SocialMediaLang.TheSocialMediaFieldIsRequired'),
            'info.numeric' => __('SocialMediaLang.TheSocialMediaustBeANumeric'),
            'info.email' => __('SocialMediaLang.TheSocialMediaMustBeAEmailAddress'),
            'info.string' => __('SocialMediaLang.TheSocialMediaMustBeAString'),
            'info.unique' => __('SocialMediaLang.TheSocialMediaHasAlreadyBeenTaken')
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
