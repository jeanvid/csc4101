<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120152139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE showcase ADD COLUMN name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__showcase AS SELECT id, creator_id FROM showcase');
        $this->addSql('DROP TABLE showcase');
        $this->addSql('CREATE TABLE showcase (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, CONSTRAINT FK_14B88CD061220EA6 FOREIGN KEY (creator_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO showcase (id, creator_id) SELECT id, creator_id FROM __temp__showcase');
        $this->addSql('DROP TABLE __temp__showcase');
        $this->addSql('CREATE INDEX IDX_14B88CD061220EA6 ON showcase (creator_id)');
    }
}
