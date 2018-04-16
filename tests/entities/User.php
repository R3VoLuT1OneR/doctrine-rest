<?php namespace Pz\Doctrine\Rest\Tests\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Email()
     * @Assert\NotNull()
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotNull()
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Blog", mappedBy="user", cascade={"all"})
     */
    protected $blogs;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Tag",
     *     orphanRemoval=true,
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist", "remove"}
     * )
     * @ORM\JoinTable(
     *     name="user_tag",
     *     joinColumns={
     *         @ORM\JoinColumn(
     *             name="user_id",
     *             referencedColumnName="id",
     *             nullable=false,
     *             onDelete="CASCADE"
     *         )
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(
     *             name="tag_id",
     *             referencedColumnName="id",
     *             nullable=false,
     *             onDelete="CASCADE"
     *         )
     *     },
     * )
     */
    protected $tags;

    /**
     * @var Role
     *
     * @ORM\OneToOne(targetEntity="Role", cascade={"persist"})
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
        $this->tags = new ArrayCollection();
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
     * @param array $blogs
     *
     * @return $this
     */
    public function setBlogs($blogs)
    {
        $this->blogs = $blogs;
        return $this;
    }

    /**
     * @return ArrayCollection|Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTags(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTags(Tag $tag)
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    /**
     * @param array|Tag[] $tags
     *
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = new ArrayCollection($tags);
        return $this;
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
