<?php namespace Pz\Doctrine\Rest\Tests\Entities\Transformers;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Tests\Entities\Blog;
use Pz\Doctrine\Rest\Tests\Entities\User;

class BlogTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user',
    ];

    /**
     * @param Blog $blog
     *
     * @return array
     */
    public function transform($blog)
    {
        return [
            'id' => $blog->getId(),
            'title' => $blog->getTitle(),
            'content' => $blog->getContent(),
        ];
    }

    /**
     * @param Blog $blog
     *
     * @return array
     */
    public function includeUser(Blog $blog)
    {
        return $this->item($blog->getUser(), new UserTransformer(), User::getResourceKey());
    }
}
