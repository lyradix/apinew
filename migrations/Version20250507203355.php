<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507203355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, scene_fk_id INT DEFAULT NULL, jour_fk_id INT DEFAULT NULL, nom VARCHAR(30) NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, famous_song VARCHAR(45) NOT NULL, genre VARCHAR(30) NOT NULL, description LONGTEXT NOT NULL, source VARCHAR(30) NOT NULL, lien LONGTEXT NOT NULL, INDEX IDX_1599687A2017BA2 (scene_fk_id), INDEX IDX_1599687A75FB8A3 (jour_fk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE days (id INT AUTO_INCREMENT NOT NULL, user_fk_id INT DEFAULT NULL, jour VARCHAR(30) NOT NULL, INDEX IDX_EBE4FC6647B5E288 (user_fk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE info (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(30) NOT NULL, type VARCHAR(30) NOT NULL, descriptif LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partners (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(30) NOT NULL, front_page TINYINT(1) NOT NULL, type VARCHAR(30) NOT NULL, link LONGTEXT NOT NULL, partner_id VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poi (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(30) NOT NULL, properties JSON NOT NULL, geometry GEOMETRY DEFAULT NULL COMMENT \'(DC2Type:geometry)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scene (id INT AUTO_INCREMENT NOT NULL, poi_fk_id INT DEFAULT NULL, nom VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_D979EFDA9FC238B5 (poi_fk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(64) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687A2017BA2 FOREIGN KEY (scene_fk_id) REFERENCES scene (id)');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687A75FB8A3 FOREIGN KEY (jour_fk_id) REFERENCES days (id)');
        $this->addSql('ALTER TABLE days ADD CONSTRAINT FK_EBE4FC6647B5E288 FOREIGN KEY (user_fk_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE scene ADD CONSTRAINT FK_D979EFDA9FC238B5 FOREIGN KEY (poi_fk_id) REFERENCES poi (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687A2017BA2');
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687A75FB8A3');
        $this->addSql('ALTER TABLE days DROP FOREIGN KEY FK_EBE4FC6647B5E288');
        $this->addSql('ALTER TABLE scene DROP FOREIGN KEY FK_D979EFDA9FC238B5');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE days');
        $this->addSql('DROP TABLE info');
        $this->addSql('DROP TABLE partners');
        $this->addSql('DROP TABLE poi');
        $this->addSql('DROP TABLE scene');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
