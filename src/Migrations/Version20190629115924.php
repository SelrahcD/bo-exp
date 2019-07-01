<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190629115924 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE experience_version_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE experience_version (id INT NOT NULL, experience_id INT NOT NULL, version INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA37385146E90E27 ON experience_version (experience_id)');
        $this->addSql('ALTER TABLE experience_version ADD CONSTRAINT FK_FA37385146E90E27 FOREIGN KEY (experience_id) REFERENCES experience (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE experience DROP description');
        $this->addSql('ALTER TABLE experience ALTER state TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE experience ALTER state DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN experience.state IS NULL');
    }

    public function down(Schema $schema) : void
    {
    }
}
