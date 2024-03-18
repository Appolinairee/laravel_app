<?php

namespace App\Http\Requests\Message;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

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
            'type' => 'required|in:image,text',
            'text' => $this->input('type') === 'text' ? 'required|string' : '',
            'image' => $this->input('type') === 'image' ? 'required|image|mimes:jpeg,png,jpg,svg|max:2048' : '',
            'receiver_id' => 'sometimes|exists:users,id',
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
        ],  JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}