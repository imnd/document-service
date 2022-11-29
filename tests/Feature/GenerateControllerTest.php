<?php

namespace Tests\Feature;

use Tests\TestCase,
    App\Entity,
    Tests\Traits\EntityTrait
;

class GenerateControllerTest extends TestCase
{
    use EntityTrait;
    
    /**
     * @test
     * @return void
     */
    public function checkShow()
    {
        $this->setMethod('GET');

        $entity = $this->createEntity(Entity::TYPE_DOCUMENT);
        $entityData = $this->createEntityData($entity, $this->user->id);
        foreach ([
            'docx' => 'msword', //'vnd.openxmlformats-officedocument.wordprocessingml.document',
            'rtf' => 'msword', //'rtf',
            'odt' => 'msword', //'vnd.oasis.opendocument.text'
        ] as $format => $MIMEType) {
            $this->setRoute('show', [
                'id' => $entity->id,
                'format' => $format,
                'uuid' => $entityData->user_id,
            ]);
            $response = $this->getRequestResponse();
            $this->assertTrue($response->headers->get('content-type') == "application/$MIMEType");
        }
    }
}
