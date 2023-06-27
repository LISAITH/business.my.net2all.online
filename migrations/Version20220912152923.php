<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912152923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE decouvertes (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, reponse_id INT DEFAULT NULL, INDEX IDX_A1D06A861E27F6BF (question_id), INDEX IDX_A1D06A86CF18BB82 (reponse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_decouvertes (id INT AUTO_INCREMENT NOT NULL, libelle LONGTEXT NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse_decouvertes (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, libelle LONGTEXT NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_A87B5F831E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE decouvertes ADD CONSTRAINT FK_A1D06A861E27F6BF FOREIGN KEY (question_id) REFERENCES question_decouvertes (id)');
        $this->addSql('ALTER TABLE decouvertes ADD CONSTRAINT FK_A1D06A86CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse_decouvertes (id)');
        $this->addSql('ALTER TABLE reponse_decouvertes ADD CONSTRAINT FK_A87B5F831E27F6BF FOREIGN KEY (question_id) REFERENCES question_decouvertes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE decouvertes DROP FOREIGN KEY FK_A1D06A861E27F6BF');
        $this->addSql('ALTER TABLE decouvertes DROP FOREIGN KEY FK_A1D06A86CF18BB82');
        $this->addSql('ALTER TABLE reponse_decouvertes DROP FOREIGN KEY FK_A87B5F831E27F6BF');
        $this->addSql('DROP TABLE decouvertes');
        $this->addSql('DROP TABLE question_decouvertes');
        $this->addSql('DROP TABLE reponse_decouvertes');
    }
}
