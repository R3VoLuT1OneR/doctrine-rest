<?php

namespace Pz\Doctrine\Rest\Tests\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171110224438 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
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
     */
    public function down(Schema $schema): void
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
