<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120192422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__keyboard AS SELECT id, inventory_id, name, brand, switch_type, keycap_set, description, image FROM keyboard');
        $this->addSql('DROP TABLE keyboard');
        $this->addSql('CREATE TABLE keyboard (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, inventory_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, brand VARCHAR(255) NOT NULL, switch_type VARCHAR(100) DEFAULT NULL, keycap_set VARCHAR(150) DEFAULT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_837480959EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO keyboard (id, inventory_id, name, brand, switch_type, keycap_set, description, image) SELECT id, inventory_id, name, brand, switch_type, keycap_set, description, image FROM __temp__keyboard');
        $this->addSql('DROP TABLE __temp__keyboard');
        $this->addSql('CREATE INDEX IDX_837480959EEA759 ON keyboard (inventory_id)');
        $this->addSql('ALTER TABLE showcase ADD COLUMN brand VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__keyboard AS SELECT id, inventory_id, switch_type, keycap_set, description, name, brand, image FROM keyboard');
        $this->addSql('DROP TABLE keyboard');
        $this->addSql('CREATE TABLE keyboard (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, inventory_id INTEGER NOT NULL, switch_type VARCHAR(100) DEFAULT NULL, keycap_set VARCHAR(150) DEFAULT NULL, description CLOB DEFAULT NULL, name VARCHAR(255) NOT NULL, brand VARCHAR(100) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_837480959EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO keyboard (id, inventory_id, switch_type, keycap_set, description, name, brand, image) SELECT id, inventory_id, switch_type, keycap_set, description, name, brand, image FROM __temp__keyboard');
        $this->addSql('DROP TABLE __temp__keyboard');
        $this->addSql('CREATE INDEX IDX_837480959EEA759 ON keyboard (inventory_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__showcase AS SELECT id, creator_id, name, description, published FROM showcase');
        $this->addSql('DROP TABLE showcase');
        $this->addSql('CREATE TABLE showcase (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, creator_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, published BOOLEAN NOT NULL, CONSTRAINT FK_14B88CD061220EA6 FOREIGN KEY (creator_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO showcase (id, creator_id, name, description, published) SELECT id, creator_id, name, description, published FROM __temp__showcase');
        $this->addSql('DROP TABLE __temp__showcase');
        $this->addSql('CREATE INDEX IDX_14B88CD061220EA6 ON showcase (creator_id)');
    }
}
