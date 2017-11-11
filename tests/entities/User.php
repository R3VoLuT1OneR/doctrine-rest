<?php namespace Pz\Doctrine\Rest\Tests\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Pz\Doctrine\Rest\Contracts\JsonApiResource;

/**
 * Class User
 *
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User implements JsonApiResource
{
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
     * @ORM\Column(name="email", type="string", unique=true, nullable=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Blog", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $blogs;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id")
     */
    protected $role;

    /**
     * @return string
     */
    public static function getResourceKey()
    {
        return 'user';
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->blogs = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'user';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection|Blog[]
     */
    public function getBlogs()
    {
        return $this->blogs;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }
}
