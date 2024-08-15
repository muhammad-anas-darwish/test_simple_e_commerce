<?php

namespace App\Http\Requests;

use App\Rules\AvailableQuantity;
use Illuminate\Foundation\Http\FormRequest;

class AddItemToCartRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'quantity_change' => ['required', 'integer', 'between:-99999,99999', new AvailableQuantity($this->product_id)],
        ];
    }
}
