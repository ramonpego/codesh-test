<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:draft,trash,published'],
            'url' => ['sometimes', 'url'],
            'creator' => ['sometimes', 'string'],
            'product_name' => ['sometimes', 'string'],
            'quantity' => ['sometimes', 'string'],
            'brands' => ['sometimes', 'string'],
            'categories' => ['sometimes', 'string'],
            'labels' => ['sometimes', 'string'],
            'cities' => ['nullable', 'string'],
            'purchase_places' => ['nullable', 'string'],
            'stores' => ['nullable', 'string'],
            'ingredients_text' => ['nullable', 'string'],
            'traces' => ['nullable', 'string'],
            'serving_size' => ['nullable', 'string'],
            'serving_quantity' => ['nullable', 'string'],
            'nutriscore_score' => ['nullable', 'string'],
            'nutriscore_grade' => ['nullable', 'string'],
            'main_category' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
        ];
    }
}
