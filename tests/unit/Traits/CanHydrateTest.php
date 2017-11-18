<?php namespace Pz\Doctrine\Rest\Tests\Traits;

use Pz\Doctrine\Rest\Exceptions\RestException;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;
use Pz\Doctrine\Rest\Traits\CanHydrate;

class CanHydrateTest extends TestCase
{
    use CanHydrate;

    /**
     * @return RestRepository
     */
    public function repository()
    {
        return new RestRepository($this->em, $this->em->getClassMetadata(User::class));
    }

    public function test_hydrate_attributes()
    {
        /** @var User $user */
        $user = $this->hydrateEntity(User::class, [
            'attributes'=> [
                'name' => 'TestName',
                'email' => 'test@test.com',
            ],
            'relationships' => [
                'role' => [
                    'data' => 1
                ],
                'blogs' => [
                    'data' => [
                        1,
                        [
                            'id' => 4,
                            'type' => 'blog',
                        ],
                        [
                            'attributes' => [
                                'title' => 'test title',
                                'content' => 'test content',
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('TestName', $user->getName());
        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertEquals('Admin', $user->getRole()->getName());
        $this->assertEquals(3, $user->getBlogs()->count());
    }

    public function test_hydrate_exceptions()
    {
        try {
            $this->hydrateEntity(User::class, []);
            $this->fail('Exception should be thrown.');
        } catch (RestException $e) {
            $this->assertEquals([
                [
                    'code' => 'missing-data-members',
                    'source' => ['pointer' => ''],
                    'detail' => 'Missing or not array `data.attributes` or `data.relationships` at pointer level.',
                ]
            ], $e->errors());
        }

        try {
            $this->hydrateEntity(User::class, ['attributes' => ['not_exists' => 1 ]]);
            $this->fail('Exception should be thrown.');
        } catch (RestException $e) {
            $this->assertEquals([
                [
                    'code' => 'unknown-attribute',
                    'source' => ['pointer' => 'not_exists'],
                    'detail' => 'Unknown attribute.',
                ]
            ], $e->errors());
        }

        try {
            $this->hydrateEntity(User::class, ['relationships' => ['not_exists' => 1 ]]);
            $this->fail('Exception should be thrown.');
        } catch (RestException $e) {
            $this->assertEquals([
                [
                    'code' => 'unknown-relation',
                    'source' => ['pointer' => 'not_exists'],
                    'detail' => 'Unknown relation.',
                ]
            ], $e->errors());
        }

        try {
            $this->hydrateEntity(User::class, ['relationships' => ['role' => 1 ]]);
            $this->fail('Exception should be thrown.');
        } catch (RestException $e) {
            $this->assertEquals([
                [
                    'code' => 'missing-data',
                    'source' => ['pointer' => 'role'],
                    'detail' => 'Missing `data` member at pointer level.',
                ]
            ], $e->errors());
        }

        try {
            $this->hydrateEntity(User::class, ['relationships' => ['blogs' => ['data' => 1]]]);
            $this->fail('Exception should be thrown.');
        } catch (RestException $e) {
            $this->assertEquals([
                [
                    'code' => 'missing-data',
                    'source' => ['pointer' => 'blogs'],
                    'detail' => 'Missing `data` member at pointer level.',
                ]
            ], $e->errors());
        }

        try {
            $this->hydrateEntity(User::class, ['relationships' => ['role' => ['data' => (new \stdClass())]]]);
            $this->fail('Exception should be thrown.');
        } catch (RestException $e) {
            $this->assertEquals([
                [
                    'code' => 'missing-data',
                    'source' => ['pointer' => 'role'],
                    'detail' => 'Missing `data` member at pointer level.',
                ]
            ], $e->errors());
        }

        try {
            $this->setProperty(new User(), 'not_exists', 'test');
            $this->fail('Exception should be thrown.');
        } catch (RestException $e) {
            $this->assertEquals([
                [
                    'code' => 'missing-setter',
                    'source' => [
                        'pointer' => 'not_exists',
                        'entity' => User::class,
                        'setter' => 'setNot_exists',
                    ],
                    'detail' => 'Missing field setter.',
                ]
            ], $e->errors());
        }
    }

}
