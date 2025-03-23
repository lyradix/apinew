<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323220730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD scene_fk_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687A2017BA2 FOREIGN KEY (scene_fk_id) REFERENCES scene (id)');
        $this->addSql('CREATE INDEX IDX_1599687A2017BA2 ON artist (scene_fk_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687A2017BA2');
        $this->addSql('DROP INDEX IDX_1599687A2017BA2 ON artist');
        $this->addSql('ALTER TABLE artist DROP scene_fk_id');
    }
}
