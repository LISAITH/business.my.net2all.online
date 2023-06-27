<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220914101916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prospections ADD potentiel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE prospections ADD CONSTRAINT FK_D02897563E7A70EB FOREIGN KEY (potentiel_id) REFERENCES reponse_decouvertes (id)');
        $this->addSql('CREATE INDEX IDX_D02897563E7A70EB ON prospections (potentiel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prospections DROP FOREIGN KEY FK_D02897563E7A70EB');
        $this->addSql('DROP INDEX IDX_D02897563E7A70EB ON prospections');
        $this->addSql('ALTER TABLE prospections DROP potentiel_id');
    }
}
