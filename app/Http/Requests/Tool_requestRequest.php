<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class Tool_requestRequest extends FormRequest
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
            'description' => 'required',
            //'activity_required' => 'required',
            'tools_id' => 'required|numeric',
            'tool_requesttypes_id' => 'required|numeric',
            'tool_requestprioritys_id' => 'numeric',
            'tool_requeststatuss_id' => 'numeric',
            //'users_id' => 'required|numeric',
            //'date_inserted' => 'required',
            'date_request' => 'required',
            'processed' => 'boolean',
            //'date_processed' => 'required'
            //continue_12
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
