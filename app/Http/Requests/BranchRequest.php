<?php

namespace App\Http\Requests;

use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\GeneralFunctions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BranchRequest extends FormRequest
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
        if (Str::contains($this->path(), 'create-branch')) 
        {
            $rules = [
                'country' => 'required|string',
                'city' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) 
                    {
                        $city = Branch::whereHas(
                            'parent', 
                            function ($country)
                            {
                                $country->where('address', '!=', $this->country);
                            }
                        )->where('address', $value)->first();
                        if ($city) 
                            $fail(__('BranchLang.ThisCityHasAlreadyBeenTakenBy:') . $city->parent->address);
                    }
                ],
                'address' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) 
                    {
                        $address = Branch::whereHas(
                            'parent', 
                            function ($city)
                            {
                                $city->where('address', '!=', $this->city);
                            }
                        )->where('address', $value)->first();
                        if ($address) 
                            $fail(__('BranchLang.ThisAddressHasAlreadyBeenTakenBy:') . $address->parent->address);
                    }
                ]
            ];
        }
        else
        {
            $action = (Str::contains($this->path(), 'branches') ? 'GET' : (Str::contains($this->path(), 'edit-branch') || Str::contains($this->path(), 'update-branch') ? 'EDIT' : 'DELETE'));
            $rules = [
                'country_id' => [
                    'integer',
                    Rule::when($action == 'GET', 'required_with:city_id,address_id'),
                    Rule::when(in_array($action, ['EDIT', 'DELETE']), 'required'),
                    Rule::exists('branches', 'id')->where('branch_id', null)
                ],
                'city_id' => [
                    'integer',
                    Rule::when(in_array($action, ['GET', 'DELETE']), 'required_with:address_id'),
                    Rule::when($action == 'EDIT', 'required'),
                    Rule::exists('branches', 'id')->where('branch_id', $this->country_id)
                ],
                'address_id' => [
                    'integer',
                    Rule::when($action == 'EDIT', 'required'),
                    Rule::when($action == 'DELETE', 'required_with:phone_id'),
                    Rule::exists('branches', 'id')->where('branch_id', $this->city_id)
                ],
            ];
            if ($action != 'GET') 
                $rules['phone_id'] = [
                    'integer',
                    Rule::when($action == 'EDIT', 'required'),
                    Rule::exists('branches', 'id')->where('branch_id', $this->address_id)
                ];
        }
        if (Str::contains($this->path(), 'create-branch') || Str::contains($this->path(), 'update-branch'))
            $rules['phone'] = [
                'required',
                'integer',
                Rule::unique('branches', 'address')->ignore($this->phone_id)
            ];
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
            'country.required' => __('BranchLang.TheCountryFieldIsRequired'),
            'city.required' => __('BranchLang.TheCityFieldIsRequired'),
            'address.required' => __('BranchLang.TheAddressFieldIsRequired'),
            'phone.required' => __('BranchLang.ThePhoneFieldIsRequired'),
            'country.string' => __('BranchLang.TheCountryMustBeAString'),
            'city.string' => __('BranchLang.TheCityMustBeAString'),
            'address.string' => __('BranchLang.TheAddressMustBeAString'),
            'phone.integer' => __('BranchLang.ThePhoneMustBeAnInteger'),
            'phone.unique' => __('BranchLang.ThePhoneHasAlreadyBeenTaken'),
            'country_id.required' => __('BranchLang.TheCountryIdFieldIsRequired'),
            'country_id.integer' => __('BranchLang.TheCountryIdMustBeAnInteger'),
            'country_id.required_with' => __('BranchLang.TheCountryIdFieldIsRequiredWhenCityIdORAddressIdIsPresent'),
            'country_id.exists' => __('BranchLang.TheSelectedCountryIdIsInvalid'),
            'city_id.required' => __('BranchLang.TheCityIdFieldIsRequired'),
            'city_id.integer' => __('BranchLang.TheCityIdMustBeAnInteger'),
            'city_id.required_with' => __('BranchLang.TheCityIdFieldIsRequiredWhenAddressIdIsPresent'),
            'city_id.exists' => __('BranchLang.TheSelectedCityIdIsInvalid'),
            'address_id.required' => __('BranchLang.TheAddressIdFieldIsRequired'),
            'address_id.integer' => __('BranchLang.TheAddressIdMustBeAnInteger'),
            'address_id.required_with' => __('BranchLang.TheAddressIdFieldIsRequiredWhenPhoneIdIsPresent'),
            'address_id.exists' => __('BranchLang.TheSelectedAddressIdIsInvalid'),
            'phone.required' => __('BranchLang.ThePhoneIdFieldIsRequired'),
            'phone.integer' => __('BranchLang.ThePhoneIdMustBeAnInteger'),
            'phone.exists' => __('BranchLang.TheSelectedPhoneIdIsInvalid'),
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
