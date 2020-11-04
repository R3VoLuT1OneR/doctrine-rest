<?php  namespace Doctrine\Rest\Resource;

use League\Fractal\TransformerAbstract;
use Doctrine\Rest\ResourceInterface;

abstract class AbstractTransformer extends TransformerAbstract
{
    abstract function transform(ResourceInterface $resource): array;
}