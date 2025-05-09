<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming authorization logic is handled elsewhere, or allow all for now
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(?int $id = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('countries')->ignore($id),
            ],
            'iso3' => [
                'required',
                'string',
                'max:3',
                Rule::unique('countries')->ignore($id),
            ],
            'iso2' => [
                'required',
                'string',
                'max:2',
                Rule::unique('countries')->ignore($id),
            ],
            'numeric_code' => [
                'required',
                'integer',
                Rule::unique('countries')->ignore($id),
            ],
            'phonecode' => [
                'required',
                'integer',
            ],
            'capital' => [
                'required',
                'string',
                'max:50',
            ],
            'currency' => [
                'required',
                'string',
                'max:4',
            ],
            'currency_name' => [
                'required',
                'string',
                'max:50',
            ],
            'currency_symbol' => [
                'required',
                'string',
                'max:50',
            ],
            'tld' => [
                'required',
                'string',
                'max:3',
            ],
            'native' => [
                'required',
                'string',
                'max:150',
            ],
            'region' => [
                'required',
                'string',
                'max:50',
            ],
            'region_id' => [
                'required',
                'integer',
            ],
            'subregion' => [
                'required',
                'string',
                'max:20',
            ],
            'subregion_id' => [
                'required',
                'integer',
            ],
            'nationality' => [
                'required',
                'string',
                'max:150',
            ],
            'timezones' => [
                'required',
                'string', // Migration defines as text, model casts to array. Using string for validation based on migration.
            ],
            'latitude' => [
                'required',
                'numeric',
            ],
            'longitude' => [
                'required',
                'numeric',
            ],
            'emoji' => [
                'required',
                'string',
                'max:200',
            ],
            'emojiU' => [
                'required',
                'string',
                'max:200',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => '🌍 Hold on! The country name is a must-have. Please fill it in.',
            'name.unique' => '🗺️ This country name is already on the map! Please choose a different one.',
            'name.max' => '📏 The country name is a bit too long. Keep it under :max characters.',

            'iso3.required' => '🔑 Don\'t forget the ISO3 code! It\'s needed for identification.',
            'iso3.unique' => '🔒 This ISO3 code is already in use. Is there another code you can use?',
            'iso3.max' => '✂️ The ISO3 code is too lengthy. It should be no more than :max characters.',

            'iso2.required' => '🔑 The ISO2 code is essential. Please provide it for country representation.',
            'iso2.unique' => '🔒 This ISO2 code is already assigned. Try a different one.',
            'iso2.max' => '✂️ The ISO2 code exceeds the maximum length of :max characters.',

            'numeric_code.required' => '🔢 A numeric code is required for this country. Don\'t skip this!',
            'numeric_code.unique' => '🔢 This numeric code is already associated with another country.',
            'numeric_code.integer' => '🔢 The numeric code must be a whole number.',

            'phonecode.required' => '📞 The phone code is necessary for communication. Please enter it.',
            'phonecode.integer' => '📞 The phone code must be a number.',

            'capital.required' => '🏛️ Please enter the capital city. It\'s a required field.',
            'capital.max' => '🏛️ The capital city name is too long. Maximum :max characters allowed.',

            'currency.required' => '💰 The currency code is required. Please provide it for financial transactions.',
            'currency.max' => '💰 The currency code is too long. Maximum :max characters allowed.',

            'currency_name.required' => '💵 The currency name is required. Please enter it.',
            'currency_name.max' => '💵 The currency name is too long. Maximum :max characters allowed.',

            'currency_symbol.required' => '💲 The currency symbol is required. Please provide it.',
            'currency_symbol.max' => '💲 The currency symbol is too long. Maximum :max characters allowed.',

            'tld.required' => '🌐 The Top-Level Domain (TLD) is required. Please enter it for web addresses.',
            'tld.max' => '🌐 The TLD is too long. Maximum :max characters allowed.',

            'native.required' => '🗣️ The native name is required. Please provide it.',
            'native.max' => '🗣️ The native name is too long. Maximum :max characters allowed.',

            'region.required' => '🗺️ The region is required. Please select one.',
            'region.max' => '🗺️ The region name is too long. Maximum :max characters allowed.',

            'region_id.required' => '🆔 The region ID is required. Please provide it.',
            'region_id.integer' => '🆔 The region ID must be an integer.',

            'subregion.required' => '🗺️ The subregion is required. Please select one.',
            'subregion.max' => '🗺️ The subregion name is too long. Maximum :max characters allowed.',

            'subregion_id.required' => '🆔 The subregion ID is required. Please provide it.',
            'subregion_id.integer' => '🆔 The subregion ID must be an integer.',

            'nationality.required' => '🧑‍🤝‍🧑 The nationality is required. Please enter it.',
            'nationality.max' => '🧑‍🤝‍🧑 The nationality name is too long. Maximum :max characters allowed.',

            'timezones.required' => '⏰ Timezone information is required. Please provide it.',
            'timezones.string' => '⏰ Timezone information must be in a valid format.',

            'latitude.required' => '⬆️ Latitude is required. Please enter it for geographical location.',
            'latitude.numeric' => '⬆️ Latitude must be a number.',

            'longitude.required' => '➡️ Longitude is required. Please enter it for geographical location.',
            'longitude.numeric' => '➡️ Longitude must be a number.',

            'emoji.required' => '✨ The emoji is required. Please provide it.',
            'emoji.max' => '✨ The emoji is too long. Maximum :max characters allowed.',

            'emojiU.required' => '✨ The EmojiU is required. Please provide it.',
            'emojiU.max' => '✨ The EmojiU is too long. Maximum :max characters allowed.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Country Name',
            'iso3' => 'ISO3 Code',
            'iso2' => 'ISO2 Code',
            'numeric_code' => 'Numeric Code',
            'phonecode' => 'Phone Code',
            'capital' => 'Capital',
            'currency' => 'Currency',
            'currency_name' => 'Currency Name',
            'currency_symbol' => 'Currency Symbol',
            'tld' => 'TLD',
            'native' => 'Native Name',
            'region' => 'Region',
            'region_id' => 'Region ID',
            'subregion' => 'Subregion',
            'subregion_id' => 'Subregion ID',
            'nationality' => 'Nationality',
            'timezones' => 'Timezones',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'emoji' => 'Emoji',
            'emojiU' => 'EmojiU',
        ];
    }
}
