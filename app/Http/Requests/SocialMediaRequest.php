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
                    'photo' => 'nullable|mimetypes:image/jpg,image/jpeg,image/png'
                ];
            else
                $rules['photo'] = 'required|mimetypes:image/jpg,image/jpeg,image/png';
    
                $rules['type'] = 'required|string|in:tel,email,application';
                $rules['info'] = [
                    'required',
                    Rule::when($this->type === 'tel', 'numeric'),
                    Rule::when($this->type === 'email', 'email'),
                    Rule::when($this->type === 'application', 'string'),
                    Rule::unique('social_media')->where(function ($query) {
                        return $query->where(['type' => $this->type, 'info' => $this->info]);
                    })->ignore($this->id)
                ];
        }

        return $rules;
    }
    
    protected function failedValidation(Validator $validator)
    {
        $response = $this->makeResponse("Failed", 422, "InVailed Inputs", $validator->errors());
        throw new ValidationException($validator, $response);
    }
}
