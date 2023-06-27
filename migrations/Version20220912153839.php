<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912153839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE decouvertes ADD prospection_id INT NOT NULL');
        $this->addSql('ALTER TABLE decouvertes ADD CONSTRAINT FK_A1D06A86CE4F4C9 FOREIGN KEY (prospection_id) REFERENCES prospections (id)');
        $this->addSql('CREATE INDEX IDX_A1D06A86CE4F4C9 ON decouvertes (prospection_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE decouvertes DROP FOREIGN KEY FK_A1D06A86CE4F4C9');
        $this->addSql('DROP INDEX IDX_A1D06A86CE4F4C9 ON decouvertes');
        $this->addSql('ALTER TABLE decouvertes DROP prospection_id');
    }
}
