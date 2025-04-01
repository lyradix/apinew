<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401090903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE days ADD user_fk_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE days ADD CONSTRAINT FK_EBE4FC6647B5E288 FOREIGN KEY (user_fk_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_EBE4FC6647B5E288 ON days (user_fk_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE days DROP FOREIGN KEY FK_EBE4FC6647B5E288');
        $this->addSql('DROP INDEX IDX_EBE4FC6647B5E288 ON days');
        $this->addSql('ALTER TABLE days DROP user_fk_id');
    }
}
