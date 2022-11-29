<?php namespace App\Services;

use GuzzleHttp\Exception\ClientException;

class ContentService
{
    const ROLE_ADMIN = 1;
    const ROLE_ACCESS = 2;

    private $httpClient;
    private $content;

    /**
     * ContentService constructor.
     * @param int $entityId
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(int $entityId)
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => env('API_URL') . '/api/content/',
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . request()->bearerToken()
                ]
        ]);

        $this->content = $this->getContentByEntityId($entityId);
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContentByEntityId(int $entityId) {
        try {
            $data = $this->httpClient->request('GET', 'content', [
                'query' => [
                    'filter[entity_id]' => $entityId,
                    'include' => 'roles',
                ]
            ]);
            $content = collect(json_decode($data->getBody()->getContents()))['data'][0];
            return $content;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param int $userId
     * @param bool $access
     * @return bool|\Illuminate\Support\Collection
     */
    public function getRoles(int $userId, $access = false) {

        if(!$this->content)
            return collect([]);

        if($this->content->roles) {
            $roles = collect($this->content->roles)->where('user_id', $userId);

            if($access)
                $roles = $roles->where('id', self::ROLE_ACCESS);

            return $roles;
        }

        return collect([]);

    }
}