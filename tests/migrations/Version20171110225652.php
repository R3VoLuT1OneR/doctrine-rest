<?php

namespace Pz\Doctrine\Rest\Tests\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Pz\Doctrine\Rest\Tests\Entities\User;
use Pz\Doctrine\Rest\Tests\TestCase;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171110225652 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $users = [
            ['email' => 'user1@test.com',  'name'    => 'User1Name', 'role' => 1],
            ['email' => 'user2@gmail.com', 'name'    => 'User2Name', 'role' => 2],
            ['email' => 'user3@test.com',  'name'    => 'User3Name', 'role' => 2],
            ['email' => 'user4@test.com',  'name'    => 'User4Name', 'role' => 2],
            ['email' => 'user5@test.com',  'name'    => 'User5Name', 'role' => 1],
        ];

        $tags = [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4'],
        ];

        $blogs = [
            [
                'user' => 1,
                'title' => 'User1 blog title 1',
                'content' => 'User1 blog content 1',
            ],
            [
                'user' => 1,
                'title' => 'User1 blog title 2',
                'content' => 'User1 blog content 2',
            ],
            [
                'user' => 1,
                'title' => 'User1 blog title 3',
                'content' => 'User1 blog content 3',
            ],
            [
                'user' => 3,
                'title' => 'User3 blog title 1',
                'content' => 'User3 blog content 1',
            ]
        ];

        $comments = [
            [
                'user' => 1,
                'blog' => 1,
                'content' => 'Comment content 1',
            ],
            [
                'user' => 1,
                'blog' => 1,
                'content' => 'Comment content 2',
            ],
            [
                'user' => 3,
                'blog' => 1,
                'content' => 'Comment content 3',
            ],
            [
                'user' => 2,
                'blog' => 1,
                'content' => 'Comment content 4',
            ],
            [
                'user' => 3,
                'blog' => 2,
                'content' => 'Comment content 5',
            ],
            [
                'user' => 1,
                'blog' => 2,
                'content' => 'Comment content 6',
            ],
            [
                'user' => 4,
                'blog' => 2,
                'content' => 'Comment content 7',
            ],
            [
                'user' => 4,
                'blog' => 2,
                'content' => 'Comment content 8',
            ],
        ];

        $this->addSql("INSERT INTO `role` (name) VALUES ('Admin')");
        $this->addSql("INSERT INTO `role` (name) VALUES ('User')");

        foreach ($users as $data) {
            $name = $data['name'];
            $email = $data['email'];
            $role = $data['role'];
            $this->addSql("INSERT INTO `user` (name, email, role_id) VALUES ('$name', '$email', $role)");
        }

        foreach ($tags as $tag) {
            $name = $tag['name'];
            $this->addSql("INSERT INTO `tag` (name) VALUES ('$name')");
        }

        $this->addSql("INSERT INTO `user_tag` (user_id, tag_id) VALUES (1,1),(1,2),(1,3)");

        foreach ($blogs as $blog) {
            $userId = $blog['user'];
            $title = $blog['title'];
            $content = $blog['content'];
            $this->addSql("INSERT INTO `blog` (user_id, title, content) VALUES ($userId, '$title', '$content')");
        }

        foreach ($comments as $comment) {
            $userId = $comment['user'];
            $blogId = $comment['blog'];
            $content = $comment['content'];
            $this->addSql("INSERT INTO `blog_comment` (user_id, blog_id, content) VALUES ($userId, $blogId, '$content')");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
