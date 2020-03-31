<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class ToolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:3|max:255'
            'name' => 'required|min:3|max:45',
            'brand' => 'required|min:3|max:45',
            'model' => 'required|min:3|max:45',
            //'description' => 'required',
            'serial_number' => 'required|min:3|max:45',
            'num_inv_general' => 'required|min:3|max:45',
            'tool_types_id' => 'required|numeric',
            'active' => 'boolean'
            //continue_9
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
