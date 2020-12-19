<?php

namespace Dt\Media\Http\Requests;

// use Dt\Core\Http\Requests\ValidationRequest;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UploadImagesRequest extends FormRequest
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
            'files.*' => "mimes:jpg,png,jpeg|max:20000"
        ];
    }

    /**
     * @param  Validator  $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'error'  => $validator->errors()
        ], Response::HTTP_EXPECTATION_FAILED);

        throw new ValidationException($validator, $response);
    }

}
