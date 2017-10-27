<?php

namespace Pz\Doctrine\Rest\Tests\Mocks;

/**
 * Mock class for TreeWalker.
 */
class MockTreeWalker extends \Doctrine\ORM\Query\TreeWalkerAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getExecutor($AST)
    {
        return null;
    }
}
