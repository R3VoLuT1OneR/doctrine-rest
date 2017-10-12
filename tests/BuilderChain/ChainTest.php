<?php namespace Tests\BuilderChain;

use Pz\Doctrine\Rest\BuilderChain\Chain;
use Pz\Doctrine\Rest\BuilderChain\MemberInterface;
use Pz\Doctrine\Rest\BuilderChain\Exceptions\InvalidChainMember;
use Pz\Doctrine\Rest\BuilderChain\Exceptions\InvalidChainMemberResponse;

use stdClass;
use ReflectionClass;
use Mockery as m;

class ChainTest extends \PHPUnit\Framework\TestCase
{

    public function test_chain_invalid_member_response()
    {
        $this->expectException(InvalidChainMemberResponse::class);
        $object = new stdClass();

        /** @var Chain $chain */
        $chain = m::mock(Chain::class)->makePartial()
            ->shouldReceive('buildClass')->andReturn(stdClass::class)
            ->getMock();

        $chain->add(m::mock(MemberInterface::class)
            ->shouldReceive('handle')->with($object)->andReturn('fsasfs')
            ->getMock());

        $chain->process($object);
    }

    public function test_chain_invalid_member_exception_on_process()
    {
        $this->expectException(InvalidChainMember::class);

        $chainClass = new ReflectionClass(Chain::class);
        $membersProperty = $chainClass->getProperty('members');
        $membersProperty->setAccessible(true);

        $chain = new Chain();
        $membersProperty->setValue($chain, [null]);
        $chain->process(new stdClass());
    }

    public function test_chain_invalid_member_exception()
    {
        $this->expectException(InvalidChainMember::class);

        Chain::create(['test']);
    }

    public function test_chain_process()
    {
        $object = new stdClass();

        $member1 = m::mock(MemberInterface::class)
            ->shouldReceive('handle')->with($object)->andReturn($object)
            ->getMock();
        $member2 = m::mock(MemberInterface::class)
            ->shouldReceive('handle')->with($object)->andReturn($object)
            ->getMock();
        $member3 = function($qb) { return $qb; };

        $chain = new Chain([$member1, $member2, $member3]);

        $this->assertEquals($object, $chain->process($object));
    }
}
