<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602092406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687A75FB8A3');
        $this->addSql('ALTER TABLE days DROP FOREIGN KEY FK_EBE4FC6647B5E288');
        $this->addSql('DROP TABLE days');
        $this->addSql('DROP INDEX IDX_1599687A75FB8A3 ON artist');
        $this->addSql('ALTER TABLE artist DROP jour_fk_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE days (id INT AUTO_INCREMENT NOT NULL, user_fk_id INT DEFAULT NULL, jour VARCHAR(30) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_EBE4FC6647B5E288 (user_fk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE days ADD CONSTRAINT FK_EBE4FC6647B5E288 FOREIGN KEY (user_fk_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE artist ADD jour_fk_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687A75FB8A3 FOREIGN KEY (jour_fk_id) REFERENCES days (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1599687A75FB8A3 ON artist (jour_fk_id)');
    }
}
