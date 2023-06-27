<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918085319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE virements_ecash DROP INDEX UNIQ_FDE5CC5C1654DEDD, ADD INDEX IDX_FDE5CC5C1654DEDD (id_compte_receveur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE virements_ecash DROP INDEX IDX_FDE5CC5C1654DEDD, ADD UNIQUE INDEX UNIQ_FDE5CC5C1654DEDD (id_compte_receveur_id)');
    }
}
