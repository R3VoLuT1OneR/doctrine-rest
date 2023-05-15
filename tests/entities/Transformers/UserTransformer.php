<?php namespace Pz\Doctrine\Rest\Tests\Entities\Transformers;

use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Tests\Entities\Blog;
use Pz\Doctrine\Rest\Tests\Entities\Role;
use Pz\Doctrine\Rest\Tests\Entities\User;

class UserTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'blogs', 'role',
    ];

    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }

    /**
     * @param User     $user
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeBlogs(User $user)
    {
        return $this->collection($user->getBlogs(), new BlogTransformer(), Blog::getResourceKey());
    }

    /**
     * @param User $user
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeRole(User $user)
    {
        if ($role = $user->getRole()) {
            return $this->item($role, new RoleTransformer(), Role::getResourceKey());
        }

        return null;
    }
}
