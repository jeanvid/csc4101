<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120165608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE keyboard ADD COLUMN image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__keyboard AS SELECT id, inventory_id, name, brand, switch_type, keycap_set, description FROM keyboard');
        $this->addSql('DROP TABLE keyboard');
        $this->addSql('CREATE TABLE keyboard (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, inventory_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, brand VARCHAR(100) DEFAULT NULL, switch_type VARCHAR(100) DEFAULT NULL, keycap_set VARCHAR(150) DEFAULT NULL, description CLOB DEFAULT NULL, CONSTRAINT FK_837480959EEA759 FOREIGN KEY (inventory_id) REFERENCES inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO keyboard (id, inventory_id, name, brand, switch_type, keycap_set, description) SELECT id, inventory_id, name, brand, switch_type, keycap_set, description FROM __temp__keyboard');
        $this->addSql('DROP TABLE __temp__keyboard');
        $this->addSql('CREATE INDEX IDX_837480959EEA759 ON keyboard (inventory_id)');
    }
}
