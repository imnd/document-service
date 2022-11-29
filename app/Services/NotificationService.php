<?php
/**
 * Created by PhpStorm.
 * User: Ruslan
 * Date: 23.08.2019
 * Time: 15:18
 */

namespace App\Services;

use Dogovor24\Authorization\Services\AuthRequestService;
use Dogovor24\Authorization\Services\AuthUserService;

class NotificationService
{

    public function sendDocumentToEmail($document, $filename)
    {
        $httpClient = (new AuthRequestService())->getHttpClient(false, true);

        $httpClient->request(
            'POST',
            'api/notification/document-request',
            [
                'multipart' => [
                    [
                        'name'     => 'document',
                        'filename' => $filename,
                        'contents' => file_get_contents($document)
                    ],
                    [
                        'name' => 'user_id',
                        'contents' => (new AuthUserService())->getId()
                    ]
                ],
            ]
        );
    }

}
