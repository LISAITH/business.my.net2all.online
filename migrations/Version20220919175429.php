<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220919175429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reglements_ecash DROP INDEX UNIQ_4E2E1848D805F948, ADD INDEX IDX_4E2E1848D805F948 (id_sous_compte_receveur_id)');
        $this->addSql('ALTER TABLE value_prospections CHANGE value value VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE virements_ecash DROP INDEX UNIQ_FDE5CC5C1654DEDD, ADD INDEX IDX_FDE5CC5C1654DEDD (id_compte_receveur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reglements_ecash DROP INDEX IDX_4E2E1848D805F948, ADD UNIQUE INDEX UNIQ_4E2E1848D805F948 (id_sous_compte_receveur_id)');
        $this->addSql('ALTER TABLE value_prospections CHANGE value value TEXT NOT NULL');
        $this->addSql('ALTER TABLE virements_ecash DROP INDEX IDX_FDE5CC5C1654DEDD, ADD UNIQUE INDEX UNIQ_FDE5CC5C1654DEDD (id_compte_receveur_id)');
    }
}
