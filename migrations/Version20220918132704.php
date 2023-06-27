<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918132704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prospection_paiements (id INT AUTO_INCREMENT NOT NULL, particulier_id INT NOT NULL, montant BIGINT NOT NULL, annee_mois VARCHAR(255) NOT NULL, done_at DATETIME NOT NULL, INDEX IDX_7EB0238CA89E0E67 (particulier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prospection_paiements ADD CONSTRAINT FK_7EB0238CA89E0E67 FOREIGN KEY (particulier_id) REFERENCES particuliers (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prospection_paiements DROP FOREIGN KEY FK_7EB0238CA89E0E67');
        $this->addSql('DROP TABLE prospection_paiements');
    }
}
