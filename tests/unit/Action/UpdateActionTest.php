<?php namespace Pz\Doctrine\Rest\Tests\Action;

use Pz\Doctrine\Rest\Action\UpdateAction;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestRequest;
use Pz\Doctrine\Rest\RestResponse;
use Pz\Doctrine\Rest\Tests\Entities\Transformers\UserTransformer;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class UpdateActionTest extends TestCase
{

    public function test_update_user()
    {
        $onUpdate = function($entity, array $change) {
            /** @var User $entity */
            $this->assertEquals(2, $entity->getId());
            $this->assertEquals([
                'email' => ['user2@gmail.com', 'new2@email.com']
            ], $change);
        };

        $action = (new UpdateAction(
            new RestRepository($this->em, $this->em->getClassMetadata(User::class)),
            new UserTransformer()
        ))
            ->beforeUpdate($onUpdate)
            ->afterUpdate($onUpdate);

        $request = new RestRequest(new Request(
            query: [
                'id' => '2',
                'fields' => [
                    'user' => 'email',
                ]
            ],
            content: json_encode([
                'data' => [
                    'attributes' => [
                        'email' => 'new2@email.com',
                    ]
                ]
            ])
        ));

        $request->http()->headers->set('CONTENT_TYPE', RestRequest::JSON_API_CONTENT_TYPE);
        $request->http()->headers->set('Accept', RestRequest::JSON_API_CONTENT_TYPE);
        $response = $action->dispatch($request);

        $this->assertInstanceOf(RestResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset(
            [
                'data' => [
                    'id' => '2',
                    'type' => 'user',
                    'attributes' => [
                        'email' => 'new2@email.com',
                    ],
                    'links' => [
                        'self' => '/user/2',
                    ]
                ],
            ],
            json_decode($response->getContent(), true)
        );
    }
}
