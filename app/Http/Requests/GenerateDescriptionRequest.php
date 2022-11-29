<?php

namespace App\Http\Requests;

use Dogovor24\Authorization\Services\AuthAbilityService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Http\FormRequest;

class GenerateDescriptionRequest extends FormRequest
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
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $client = new Client([
                    'base_uri' => env('API_URL'),
                    'headers'  => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . request()->bearerToken()
                    ]
                ]);
                $response = $client->request('GET', '/api/content/content/' . request()->contentId);

                if ($response->getStatusCode() == 200) {
                    $contentInfo = json_decode($response->getBody()->getContents());
                    if (
                           $contentInfo->data->is_ready
                        &&
                              $contentInfo->data->is_published
                           || (new AuthAbilityService())->userHasAbility('document-entity-view')
                        && isset($contentInfo->data->payload->description_id)
                    )
                        request()->merge(["description_id" => $contentInfo->data->payload->description_id]);
                    else
                        $validator->errors()->add('contentId', 'Content is not ready or published');
                } else
                    $validator->errors()->add('contentId', 'Cannot get content');
            } catch (ClientException $exception) {
                $validator->errors()->add('contentId', 'Content service error');
            }
        });
    }
}
