<?php namespace Pz\Doctrine\Rest\Tests\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tag")
 */
class Tag implements JsonApiResource
{
    /**
     * @return string
     */
    public static function getResourceKey()
    {
        return 'tag';
    }

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="tags")
     */
    protected $users;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
