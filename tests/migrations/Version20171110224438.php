<?php

namespace Doctrine\Rest\Tests\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Rest\Tests\TestCase;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171110224438 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE blog (id INTEGER NOT NULL, user_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, content CLOB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C0155143A76ED395 ON blog (user_id)');
        $this->addSql('CREATE TABLE role (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE blog_comment (id INTEGER NOT NULL, blog_id INTEGER NOT NULL, user_id INTEGER NOT NULL, content VARCHAR(1023) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7882EFEFDAE07E97 ON blog_comment (blog_id)');
        $this->addSql('CREATE INDEX IDX_7882EFEFA76ED395 ON blog_comment (user_id)');
        $this->addSql('CREATE TABLE user (id INTEGER NOT NULL, role_id INTEGER DEFAULT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON user (role_id)');

        $this->addSql('CREATE TABLE user_tag (user_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(user_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_E89FD608A76ED395 ON user_tag (user_id)');
        $this->addSql('CREATE INDEX IDX_E89FD608BAD26311 ON user_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postUp(Schema $schema)
    {
        $em = TestCase::generateEntityManager();

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

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE blog_comment');
        $this->addSql('DROP TABLE user');

        $this->addSql('DROP TABLE user_tag');
        $this->addSql('DROP TABLE tag');
    }
}
