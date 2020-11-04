<?php namespace Doctrine\Rest\Tests\Entities\Transformers;

use League\Fractal\TransformerAbstract;
use Doctrine\Rest\Tests\Entities\Blog;
use Doctrine\Rest\Tests\Entities\BlogComment;
use Doctrine\Rest\Tests\Entities\User;

class BlogCommentTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'blog',
    ];

    /**
     * @param BlogComment $comment
     *
     * @return array
     */
    public function transform(BlogComment $comment)
    {
        return [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
        ];
    }

    /**
     * @param BlogComment $comment
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeBlog(BlogComment $comment)
    {
        return $this->item($comment->getBlog(), new BlogTransformer(), Blog::getResourceKey());
    }

    /**
     * @param BlogComment $comment
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(BlogComment $comment)
    {
        return $this->item($comment->getUser(), new UserTransformer(), User::getResourceKey());
    }
}
