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
        return app()->getLocale() == 'en' ? 
        [
            'id.required' => 'The Id Field Is Required.',
            'id.integer' => 'The Id Must Be a Integer.',
            'id.exists' => 'This Id Is Invalid.',
            'photo.mimetypes' => 'The Photo Extension Must Be SVG.',
            'photoName.required_without' => 'The Social Media Photo Field Is Required.',
            'photoName.string' => 'The Social Media Photo Name must be a String.',
            'type.required' => 'The Social Media Type Field Is Required.',
            'type.string' => 'The Social Media Type Must Be a String.',
            'type.in' => 'The Social Media Type Must Be One Of These (tel, email, application).',
            'info.required' => 'The Social Media Field Is Required.',
            'info.numeric' => 'The Social Media Must Be a Numeric.',
            'info.email' => 'The Social Media Must Be a Email Address.',
            'info.string' => 'The Social Media Must Be a String.',
            'info.unique' => 'The Social Media Has Already Been Taken.'
        ] : 
        [
            'id.required' => 'رقم المعرف مطلوب.',
            'id.integer' => 'يجب أن يكون رقم المعرف من نوع رقم صحيح.',
            'id.exists' => 'هذا الرقم غير صحيح.',
            'photo.mimetypes' => '.SVG يجب أن يكون امتداد الصورة ',
            'photoName.required_without' => 'صورة وسيلة التواصل مطلوبة.',
            'photoName.string' => 'يجب أن يكون اسم صورة وسيلة التواصل من نوع نصي..',
            'type.required' => 'نوع وسيلة التواصل مطلوب.',
            'type.string' => 'يجب أن يكون نوع وسيلة التواصل من نوع نصي.',
            'type.in' => 'يجب أن يكون نوع وسيلة التواصل احدى هذه الأنواع (هاتف, بريد الكتروني, تطبيق).',
            'info.required' => 'وسيلة التواصل مطلوبة.',
            'info.numeric' => 'يجب أن تكون وسيلة التواصل من نوع رقمي.',
            'info.email' => 'يجب أن تكون وسيلة التواصل من نوع بريد الكتروني.',
            'info.string' => 'يجب أن تكون وسيلة التواصل من نوع نصي.',
            'info.unique' => 'وسيلة التواصل موجودة مسبقا.'
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
