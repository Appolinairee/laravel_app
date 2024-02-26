<?php

namespace App\Http\Requests\Message;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MessageStoreRequest extends FormRequest
{
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
        return [
            'content' => $this->input('type') === 'text' ? '' : 'required|string',
            'image' => $this->input('type') === 'image' ? 'required|image|mimes:jpeg,png,jpg,svg|max:2048' : '',
            'type' => 'required|in:image,text',
            'receiver_type' => 'required|in:atoun,user,vendor',
            'receiver_id' => $this->input('receiver_type') === 'vendor' ? 'required|exists:creators,id' : (($this->input('receiver_type') !== 'atoun')? 'required|exists:users,id' : ''),
        ];
    }

    
    /**
    * Determine if the user is authorized to make this request.
    **/
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Erreur de validation',
            'errorsList' => $validator->errors()
        ]));
    }
}