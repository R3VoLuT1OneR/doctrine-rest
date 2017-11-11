<?php namespace Pz\Doctrine\Rest\Tests\Entities\Transformers;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Tests\Entities\Role;

class RoleTransformer extends TransformerAbstract
{
    /**
     * @param Role $role
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id'    => $role->getId(),
            'name'  => $role->getName(),
        ];
    }
}
