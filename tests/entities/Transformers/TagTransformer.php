<?php namespace Pz\Doctrine\Rest\Tests\Entities\Transformers;

use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Tests\Entities\Tag;

class TagTransformer extends TransformerAbstract
{
    /**
     * @param Tag $tag
     *
     * @return array
     */
    public function transform(Tag $tag)
    {
        return [
            'id' => $tag->getId(),
            'name' => $tag->getName(),
        ];
    }
}
