<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912094743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE join_enseigne_communaute (id INT AUTO_INCREMENT NOT NULL, enseigne_id INT NOT NULL, user_id INT NOT NULL, type_user VARCHAR(255) NOT NULL, communaute_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE param_prospections (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, done_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE value_prospections (id INT AUTO_INCREMENT NOT NULL, param_prospection_id INT NOT NULL, value BIGINT NOT NULL, done_at DATETIME NOT NULL, INDEX IDX_728D139C73C3DC28 (param_prospection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE value_prospections ADD CONSTRAINT FK_728D139C73C3DC28 FOREIGN KEY (param_prospection_id) REFERENCES param_prospections (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE value_prospections DROP FOREIGN KEY FK_728D139C73C3DC28');
        $this->addSql('DROP TABLE join_enseigne_communaute');
        $this->addSql('DROP TABLE param_prospections');
        $this->addSql('DROP TABLE value_prospections');
    }
}
