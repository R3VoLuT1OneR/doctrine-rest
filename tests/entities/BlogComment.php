<?php namespace Pz\Doctrine\Rest\Tests\Entities;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use Pz\Doctrine\Rest\Contracts\JsonApiResource;

/**
 * Class BlogComment
 *
 * @ORM\Entity()
 * @ORM\Table(name="blog_comment")
 */
class BlogComment implements JsonApiResource
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Blog
     *
     * @ORM\ManyToOne(targetEntity="Blog", inversedBy="comments")
     * @ORM\JoinColumn(name="blog_id", nullable=false)
     */
    protected $blog;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=1023, nullable=false)
     */
    protected $content;

    /**
     * @return string
     */
    public static function getResourceKey()
    {
        return 'blog_comment';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Blog $blog
     *
     * @return $this
     */
    public function setBlog(Blog $blog)
    {
        $this->blog = $blog;
        return $this;
    }

    /**
     * @return Blog
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this->content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
