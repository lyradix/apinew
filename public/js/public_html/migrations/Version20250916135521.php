<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916135521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist ADD time_stamp DATE NOT NULL');
        $this->addSql('ALTER TABLE info ADD time_stamp DATE NOT NULL');
        $this->addSql('ALTER TABLE partners ADD time_stamp DATE NOT NULL');
        $this->addSql('ALTER TABLE poi ADD time_stamp DATE NOT NULL');
        $this->addSql('ALTER TABLE scene ADD time_stamp DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partners DROP time_stamp');
        $this->addSql('ALTER TABLE scene DROP time_stamp');
        $this->addSql('ALTER TABLE artist DROP time_stamp');
        $this->addSql('ALTER TABLE poi DROP time_stamp');
        $this->addSql('ALTER TABLE info DROP time_stamp');
    }
}
