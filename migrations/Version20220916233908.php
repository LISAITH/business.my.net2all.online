<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220916233908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config_messages (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recommandations (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, numero_recommande VARCHAR(255) DEFAULT NULL, email_recommande VARCHAR(255) DEFAULT NULL, user_type INT NOT NULL, status INT DEFAULT NULL, link VARCHAR(255) NOT NULL, pays_id INT NOT NULL, guest_id INT DEFAULT NULL, guest_temp_password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enseignes ADD address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE config_messages');
        $this->addSql('DROP TABLE recommandations');
        $this->addSql('ALTER TABLE enseignes DROP address');
    }
}
