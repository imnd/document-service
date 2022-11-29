<?php

namespace App\Http\Requests;

use App\Entity;
use App\Services\BillingService;
use App\Services\Document\DocumentService;
use Illuminate\Foundation\Http\FormRequest;

class GenerateRequest extends FormRequest
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
            'format' => 'in:' . DocumentService::getFormats(),

//            'options'   => 'required|array',
//            'options.*' => ['required', 'distinct', new TreeOptionsRule],
            //'fields'   => 'filled|array',
            //'fields.*' => 'filled|array',
            //'fields.*.id'    => 'required|integer|exists:fields',
            //'fields.*.value' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator) {
            $documentId = request()->documentId;

            $document = Entity::where('id', $documentId)
                ->where('type', config('entities.types.document'))
                ->accessible(request()->get('uuid'))
                ->first();

            if (empty($document)) {
                abort(404);
            }

            request()->merge(['documentEntity' => $document]);

            if (env('APP_ENV')!=='testing') {
                app(BillingService::class)->validateBillingOrder($validator, $documentId);
            }
        });
    }
}
