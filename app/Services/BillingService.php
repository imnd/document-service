<?php namespace App\Services;

use App\Entity;
use Dogovor24\Authorization\Services\AuthUserService;
use GuzzleHttp\Exception\ClientException;
use Dogovor24\Queue\Jobs\Billing\CreateProductOrderJob;

class BillingService
{
    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => config('api.billing_url'),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->bearerToken()
            ]
        ]);
    }

    public function validateBillingOrder($validator, int $documentId)
    {
        try {
            $data = $this->httpClient->get("is-product-paid/document/$documentId");
            $status = json_decode($data->getBody()->getContents());
            // Payment required. No active subscription found.
            if ($status->code == 402) {
                $validator->errors()->add('order_id', $status->order_id);
            }
        } catch (ClientException $exception) {
            if ($exception->getCode() == 404) {
                $validator->errors()->add('product', 'not found');

                $entity = Entity::find($documentId);
                $data = $entity ? $entity->data()->whereNotNull('payload->title')->first() : null;

                CreateProductOrderJob::dispatch(
                    (new AuthUserService())->getId(),
                    $documentId,
                    $data ? $data->payload['title'][app()->getLocale()] : null
                );
            }
        }
    }
}
