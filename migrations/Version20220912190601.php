<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220912190601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement (id INT AUTO_INCREMENT NOT NULL, distributeur_id INT DEFAULT NULL, point_vente_id INT DEFAULT NULL, numero_serie VARCHAR(255) NOT NULL, numero_activation VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_351268BB29EB7ACA (distributeur_id), INDEX IDX_351268BBEFA24D68 (point_vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activation_abonnements (id INT AUTO_INCREMENT NOT NULL, abonnement_id INT NOT NULL, enseigne_id INT NOT NULL, point_vente_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_91F70E3BF1D74413 (abonnement_id), UNIQUE INDEX UNIQ_91F70E3B6C2A0A71 (enseigne_id), INDEX IDX_91F70E3BEFA24D68 (point_vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_communautes (id INT AUTO_INCREMENT NOT NULL, id_enseigne INT NOT NULL, id_communautes INT NOT NULL, id_entreprise INT NOT NULL, is_installed TINYINT(1) DEFAULT NULL, nim LONGTEXT DEFAULT NULL, type_ifu VARCHAR(10) DEFAULT NULL, is_treated TINYINT(1) DEFAULT NULL, is_set TINYINT(1) DEFAULT NULL, is_admin TINYINT(1) DEFAULT NULL, baseurl VARCHAR(255) DEFAULT NULL, api_key LONGTEXT DEFAULT NULL, mode VARCHAR(10) DEFAULT NULL, can_send_sms VARCHAR(25) DEFAULT NULL, title_sms VARCHAR(255) DEFAULT NULL, installation_status INT DEFAULT NULL, is_user_validator INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_services (id INT AUTO_INCREMENT NOT NULL, id_enseigne INT NOT NULL, id_services INT NOT NULL, id_entreprise INT NOT NULL, is_installed TINYINT(1) DEFAULT NULL, nim LONGTEXT DEFAULT NULL, type_ifu VARCHAR(10) DEFAULT NULL, is_treated TINYINT(1) DEFAULT NULL, is_set TINYINT(1) DEFAULT NULL, is_admin TINYINT(1) DEFAULT NULL, baseurl VARCHAR(255) DEFAULT NULL, api_key LONGTEXT DEFAULT NULL, mode VARCHAR(10) DEFAULT NULL, can_send_sms VARCHAR(25) DEFAULT NULL, title_sms VARCHAR(255) DEFAULT NULL, installation_status INT DEFAULT NULL, is_user_validator INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banks (id INT AUTO_INCREMENT NOT NULL, nom_bank VARCHAR(255) NOT NULL, url_image VARCHAR(255) DEFAULT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collaboration (id INT AUTO_INCREMENT NOT NULL, enseigne_id INT NOT NULL, service_id INT NOT NULL, user_id INT NOT NULL, joined_at DATETIME NOT NULL, profiles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', droits LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', identification_number VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collaboration_request (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, service_id INT NOT NULL, enseigne_id INT NOT NULL, expediteur_id INT NOT NULL, expediteur_type VARCHAR(255) NOT NULL, destinataire_id INT NOT NULL, destinataire_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE communaute (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, etat INT DEFAULT NULL, type INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, app_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte_ecash (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT DEFAULT NULL, particulier_id INT DEFAULT NULL, solde DOUBLE PRECISION NOT NULL, numero_compte VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_49ECD2EE9731415A (numero_compte), UNIQUE INDEX UNIQ_49ECD2EEA4AEAFEA (entreprise_id), UNIQUE INDEX UNIQ_49ECD2EEA89E0E67 (particulier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distributeur (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, partenaire_id INT DEFAULT NULL, nom_distributeur VARCHAR(255) NOT NULL, cle_distributeur VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_97E6871A76ED395 (user_id), INDEX IDX_97E687198DE13AC (partenaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE droits (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, numero INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enseignes (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, nom_enseigne VARCHAR(255) NOT NULL, code_enseigne VARCHAR(255) NOT NULL, url_image LONGTEXT NOT NULL, status TINYINT(1) NOT NULL, phone VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, is_validated TINYINT(1) DEFAULT NULL, is_360_installed TINYINT(1) DEFAULT NULL, INDEX IDX_1139B55BA4AEAFEA (entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprises (id INT AUTO_INCREMENT NOT NULL, pays_id INT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenoms VARCHAR(255) NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, url_image VARCHAR(255) NOT NULL, num_tel VARCHAR(255) NOT NULL, INDEX IDX_56B1B7A9A6E44244 (pays_id), INDEX IDX_56B1B7A9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE factures (id INT AUTO_INCREMENT NOT NULL, enseigne VARCHAR(255) NOT NULL, client_name VARCHAR(255) NOT NULL, client_id INT NOT NULL, facture_id INT NOT NULL, facture_ref VARCHAR(255) NOT NULL, prix VARCHAR(255) NOT NULL, prixttc VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, pdflink VARCHAR(255) NOT NULL, data_nom VARCHAR(255) NOT NULL, data_prenoms VARCHAR(255) NOT NULL, data_phone VARCHAR(255) NOT NULL, data_logo VARCHAR(255) NOT NULL, particulier_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formule (id INT AUTO_INCREMENT NOT NULL, nom_formule VARCHAR(255) NOT NULL, prix NUMERIC(10, 0) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formule_services (formule_id INT NOT NULL, services_id INT NOT NULL, INDEX IDX_B81169082A68F4D1 (formule_id), INDEX IDX_B8116908AEF5A6C1 (services_id), PRIMARY KEY(formule_id, services_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE frais_operations (id INT AUTO_INCREMENT NOT NULL, frais_reglement DOUBLE PRECISION NOT NULL, frais_ecash_vers_sous_compte DOUBLE PRECISION NOT NULL, frais_inter_ecash DOUBLE PRECISION NOT NULL, frais_ecash_bank DOUBLE PRECISION NOT NULL, frais_inter_bank DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE join_enseigne (id INT AUTO_INCREMENT NOT NULL, enseigne_id INT NOT NULL, user_id INT NOT NULL, type_user VARCHAR(255) NOT NULL, service_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE join_enseigne_communaute (id INT AUTO_INCREMENT NOT NULL, enseigne_id INT NOT NULL, user_id INT NOT NULL, type_user VARCHAR(255) NOT NULL, communaute_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kits (id INT AUTO_INCREMENT NOT NULL, numero_serie VARCHAR(255) NOT NULL, numero_activation VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaire (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom_partenaire VARCHAR(255) NOT NULL, code_pays VARCHAR(255) NOT NULL, cle_partenaire VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_32FFA373A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE particulier_profil_prospections (id INT AUTO_INCREMENT NOT NULL, particulier_id INT NOT NULL, profil_prospection_id INT NOT NULL, parent_id INT DEFAULT NULL, parent_parent VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5C8EBD9A89E0E67 (particulier_id), INDEX IDX_5C8EBD9261FCDE0 (profil_prospection_id), INDEX IDX_5C8EBD9727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE particuliers (id INT AUTO_INCREMENT NOT NULL, pays_id INT DEFAULT NULL, user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, prenoms VARCHAR(255) NOT NULL, num_tel VARCHAR(255) NOT NULL, genre VARCHAR(255) DEFAULT NULL, INDEX IDX_3FD6A879A6E44244 (pays_id), INDEX IDX_3FD6A879A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pays (id INT AUTO_INCREMENT NOT NULL, libelle_pays VARCHAR(255) NOT NULL, indicatif VARCHAR(255) NOT NULL, status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE point_vente (id INT AUTO_INCREMENT NOT NULL, distributeur_id INT NOT NULL, user_id INT DEFAULT NULL, nom_point_vente VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, type SMALLINT NOT NULL, INDEX IDX_2BBFAADF29EB7ACA (distributeur_id), UNIQUE INDEX UNIQ_2BBFAADFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil_prospections (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profiles (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, numero INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prospections (id INT AUTO_INCREMENT NOT NULL, particulier_id INT DEFAULT NULL, user_id INT DEFAULT NULL, entreprise_id INT NOT NULL, enseigne_id INT NOT NULL, validator_id INT DEFAULT NULL, done_at DATETIME NOT NULL, status TINYINT(1) NOT NULL, validated_at DATETIME DEFAULT NULL, INDEX IDX_D0289756A89E0E67 (particulier_id), UNIQUE INDEX UNIQ_D0289756A76ED395 (user_id), UNIQUE INDEX UNIQ_D0289756A4AEAFEA (entreprise_id), UNIQUE INDEX UNIQ_D02897566C2A0A71 (enseigne_id), INDEX IDX_D0289756B0644AEC (validator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reglements_ecash (id INT AUTO_INCREMENT NOT NULL, id_sous_compte_envoyeur_id INT DEFAULT NULL, id_sous_compte_receveur_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, date_transaction DATETIME NOT NULL, INDEX IDX_4E2E184826F7B8E8 (id_sous_compte_envoyeur_id), UNIQUE INDEX UNIQ_4E2E1848D805F948 (id_sous_compte_receveur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, etat BIGINT DEFAULT NULL, type BIGINT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, app_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_compte (id INT AUTO_INCREMENT NOT NULL, compte_ecash_id INT DEFAULT NULL, service_id INT DEFAULT NULL, solde DOUBLE PRECISION NOT NULL, numero_sous_compte VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1FE9A0975056AF58 (numero_sous_compte), INDEX IDX_1FE9A097DF8F311D (compte_ecash_id), INDEX IDX_1FE9A097ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE souscription_formules (id INT AUTO_INCREMENT NOT NULL, enseigne_id INT NOT NULL, formule_id INT NOT NULL, point_vente_id INT NOT NULL, is_validated TINYINT(1) NOT NULL, date_debut DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_fin DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6CBB6C976C2A0A71 (enseigne_id), INDEX IDX_6CBB6C972A68F4D1 (formule_id), INDEX IDX_6CBB6C97EFA24D68 (point_vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, sys_name VARCHAR(255) NOT NULL, num INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, numero VARCHAR(255) DEFAULT NULL, status TINYINT(1) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE virements_bancaires (id INT AUTO_INCREMENT NOT NULL, id_compte_ecash_id INT DEFAULT NULL, numero_bancaire VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, date_transaction DATETIME NOT NULL, INDEX IDX_C638484E36D1F295 (id_compte_ecash_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE virements_ecash (id INT AUTO_INCREMENT NOT NULL, id_compte_envoyeur_id INT DEFAULT NULL, id_compte_receveur_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, date_transaction DATETIME NOT NULL, INDEX IDX_FDE5CC5CE8A69F7D (id_compte_envoyeur_id), UNIQUE INDEX UNIQ_FDE5CC5C1654DEDD (id_compte_receveur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE virements_inter_bancaire (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, numero_bank_envoyeur VARCHAR(255) NOT NULL, numero_bank_receveur VARCHAR(255) NOT NULL, montant DOUBLE PRECISION NOT NULL, date_transaction DATETIME NOT NULL, INDEX IDX_329AE03879F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB29EB7ACA FOREIGN KEY (distributeur_id) REFERENCES distributeur (id)');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBEFA24D68 FOREIGN KEY (point_vente_id) REFERENCES point_vente (id)');
        $this->addSql('ALTER TABLE activation_abonnements ADD CONSTRAINT FK_91F70E3BF1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement (id)');
        $this->addSql('ALTER TABLE activation_abonnements ADD CONSTRAINT FK_91F70E3B6C2A0A71 FOREIGN KEY (enseigne_id) REFERENCES enseignes (id)');
        $this->addSql('ALTER TABLE activation_abonnements ADD CONSTRAINT FK_91F70E3BEFA24D68 FOREIGN KEY (point_vente_id) REFERENCES point_vente (id)');
        $this->addSql('ALTER TABLE compte_ecash ADD CONSTRAINT FK_49ECD2EEA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprises (id)');
        $this->addSql('ALTER TABLE compte_ecash ADD CONSTRAINT FK_49ECD2EEA89E0E67 FOREIGN KEY (particulier_id) REFERENCES particuliers (id)');
        $this->addSql('ALTER TABLE distributeur ADD CONSTRAINT FK_97E6871A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE distributeur ADD CONSTRAINT FK_97E687198DE13AC FOREIGN KEY (partenaire_id) REFERENCES partenaire (id)');
        $this->addSql('ALTER TABLE enseignes ADD CONSTRAINT FK_1139B55BA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprises (id)');
        $this->addSql('ALTER TABLE entreprises ADD CONSTRAINT FK_56B1B7A9A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE entreprises ADD CONSTRAINT FK_56B1B7A9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formule_services ADD CONSTRAINT FK_B81169082A68F4D1 FOREIGN KEY (formule_id) REFERENCES formule (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formule_services ADD CONSTRAINT FK_B8116908AEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA373A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE particulier_profil_prospections ADD CONSTRAINT FK_5C8EBD9A89E0E67 FOREIGN KEY (particulier_id) REFERENCES particuliers (id)');
        $this->addSql('ALTER TABLE particulier_profil_prospections ADD CONSTRAINT FK_5C8EBD9261FCDE0 FOREIGN KEY (profil_prospection_id) REFERENCES profil_prospections (id)');
        $this->addSql('ALTER TABLE particulier_profil_prospections ADD CONSTRAINT FK_5C8EBD9727ACA70 FOREIGN KEY (parent_id) REFERENCES particuliers (id)');
        $this->addSql('ALTER TABLE particuliers ADD CONSTRAINT FK_3FD6A879A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id)');
        $this->addSql('ALTER TABLE particuliers ADD CONSTRAINT FK_3FD6A879A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE point_vente ADD CONSTRAINT FK_2BBFAADF29EB7ACA FOREIGN KEY (distributeur_id) REFERENCES distributeur (id)');
        $this->addSql('ALTER TABLE point_vente ADD CONSTRAINT FK_2BBFAADFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE prospections ADD CONSTRAINT FK_D0289756A89E0E67 FOREIGN KEY (particulier_id) REFERENCES particuliers (id)');
        $this->addSql('ALTER TABLE prospections ADD CONSTRAINT FK_D0289756A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE prospections ADD CONSTRAINT FK_D0289756A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprises (id)');
        $this->addSql('ALTER TABLE prospections ADD CONSTRAINT FK_D02897566C2A0A71 FOREIGN KEY (enseigne_id) REFERENCES enseignes (id)');
        $this->addSql('ALTER TABLE prospections ADD CONSTRAINT FK_D0289756B0644AEC FOREIGN KEY (validator_id) REFERENCES particuliers (id)');
        $this->addSql('ALTER TABLE reglements_ecash ADD CONSTRAINT FK_4E2E184826F7B8E8 FOREIGN KEY (id_sous_compte_envoyeur_id) REFERENCES sous_compte (id)');
        $this->addSql('ALTER TABLE reglements_ecash ADD CONSTRAINT FK_4E2E1848D805F948 FOREIGN KEY (id_sous_compte_receveur_id) REFERENCES sous_compte (id)');
        $this->addSql('ALTER TABLE sous_compte ADD CONSTRAINT FK_1FE9A097DF8F311D FOREIGN KEY (compte_ecash_id) REFERENCES compte_ecash (id)');
        $this->addSql('ALTER TABLE sous_compte ADD CONSTRAINT FK_1FE9A097ED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
        $this->addSql('ALTER TABLE souscription_formules ADD CONSTRAINT FK_6CBB6C976C2A0A71 FOREIGN KEY (enseigne_id) REFERENCES enseignes (id)');
        $this->addSql('ALTER TABLE souscription_formules ADD CONSTRAINT FK_6CBB6C972A68F4D1 FOREIGN KEY (formule_id) REFERENCES formule (id)');
        $this->addSql('ALTER TABLE souscription_formules ADD CONSTRAINT FK_6CBB6C97EFA24D68 FOREIGN KEY (point_vente_id) REFERENCES point_vente (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE virements_bancaires ADD CONSTRAINT FK_C638484E36D1F295 FOREIGN KEY (id_compte_ecash_id) REFERENCES compte_ecash (id)');
        $this->addSql('ALTER TABLE virements_ecash ADD CONSTRAINT FK_FDE5CC5CE8A69F7D FOREIGN KEY (id_compte_envoyeur_id) REFERENCES compte_ecash (id)');
        $this->addSql('ALTER TABLE virements_ecash ADD CONSTRAINT FK_FDE5CC5C1654DEDD FOREIGN KEY (id_compte_receveur_id) REFERENCES compte_ecash (id)');
        $this->addSql('ALTER TABLE virements_inter_bancaire ADD CONSTRAINT FK_329AE03879F37AE5 FOREIGN KEY (id_user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BB29EB7ACA');
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBEFA24D68');
        $this->addSql('ALTER TABLE activation_abonnements DROP FOREIGN KEY FK_91F70E3BF1D74413');
        $this->addSql('ALTER TABLE activation_abonnements DROP FOREIGN KEY FK_91F70E3B6C2A0A71');
        $this->addSql('ALTER TABLE activation_abonnements DROP FOREIGN KEY FK_91F70E3BEFA24D68');
        $this->addSql('ALTER TABLE compte_ecash DROP FOREIGN KEY FK_49ECD2EEA4AEAFEA');
        $this->addSql('ALTER TABLE compte_ecash DROP FOREIGN KEY FK_49ECD2EEA89E0E67');
        $this->addSql('ALTER TABLE distributeur DROP FOREIGN KEY FK_97E6871A76ED395');
        $this->addSql('ALTER TABLE distributeur DROP FOREIGN KEY FK_97E687198DE13AC');
        $this->addSql('ALTER TABLE enseignes DROP FOREIGN KEY FK_1139B55BA4AEAFEA');
        $this->addSql('ALTER TABLE entreprises DROP FOREIGN KEY FK_56B1B7A9A6E44244');
        $this->addSql('ALTER TABLE entreprises DROP FOREIGN KEY FK_56B1B7A9A76ED395');
        $this->addSql('ALTER TABLE formule_services DROP FOREIGN KEY FK_B81169082A68F4D1');
        $this->addSql('ALTER TABLE formule_services DROP FOREIGN KEY FK_B8116908AEF5A6C1');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373A76ED395');
        $this->addSql('ALTER TABLE particulier_profil_prospections DROP FOREIGN KEY FK_5C8EBD9A89E0E67');
        $this->addSql('ALTER TABLE particulier_profil_prospections DROP FOREIGN KEY FK_5C8EBD9261FCDE0');
        $this->addSql('ALTER TABLE particulier_profil_prospections DROP FOREIGN KEY FK_5C8EBD9727ACA70');
        $this->addSql('ALTER TABLE particuliers DROP FOREIGN KEY FK_3FD6A879A6E44244');
        $this->addSql('ALTER TABLE particuliers DROP FOREIGN KEY FK_3FD6A879A76ED395');
        $this->addSql('ALTER TABLE point_vente DROP FOREIGN KEY FK_2BBFAADF29EB7ACA');
        $this->addSql('ALTER TABLE point_vente DROP FOREIGN KEY FK_2BBFAADFA76ED395');
        $this->addSql('ALTER TABLE prospections DROP FOREIGN KEY FK_D0289756A89E0E67');
        $this->addSql('ALTER TABLE prospections DROP FOREIGN KEY FK_D0289756A76ED395');
        $this->addSql('ALTER TABLE prospections DROP FOREIGN KEY FK_D0289756A4AEAFEA');
        $this->addSql('ALTER TABLE prospections DROP FOREIGN KEY FK_D02897566C2A0A71');
        $this->addSql('ALTER TABLE prospections DROP FOREIGN KEY FK_D0289756B0644AEC');
        $this->addSql('ALTER TABLE reglements_ecash DROP FOREIGN KEY FK_4E2E184826F7B8E8');
        $this->addSql('ALTER TABLE reglements_ecash DROP FOREIGN KEY FK_4E2E1848D805F948');
        $this->addSql('ALTER TABLE sous_compte DROP FOREIGN KEY FK_1FE9A097DF8F311D');
        $this->addSql('ALTER TABLE sous_compte DROP FOREIGN KEY FK_1FE9A097ED5CA9E6');
        $this->addSql('ALTER TABLE souscription_formules DROP FOREIGN KEY FK_6CBB6C976C2A0A71');
        $this->addSql('ALTER TABLE souscription_formules DROP FOREIGN KEY FK_6CBB6C972A68F4D1');
        $this->addSql('ALTER TABLE souscription_formules DROP FOREIGN KEY FK_6CBB6C97EFA24D68');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649C54C8C93');
        $this->addSql('ALTER TABLE virements_bancaires DROP FOREIGN KEY FK_C638484E36D1F295');
        $this->addSql('ALTER TABLE virements_ecash DROP FOREIGN KEY FK_FDE5CC5CE8A69F7D');
        $this->addSql('ALTER TABLE virements_ecash DROP FOREIGN KEY FK_FDE5CC5C1654DEDD');
        $this->addSql('ALTER TABLE virements_inter_bancaire DROP FOREIGN KEY FK_329AE03879F37AE5');
        $this->addSql('DROP TABLE abonnement');
        $this->addSql('DROP TABLE activation_abonnements');
        $this->addSql('DROP TABLE api_communautes');
        $this->addSql('DROP TABLE api_services');
        $this->addSql('DROP TABLE banks');
        $this->addSql('DROP TABLE collaboration');
        $this->addSql('DROP TABLE collaboration_request');
        $this->addSql('DROP TABLE communaute');
        $this->addSql('DROP TABLE compte_ecash');
        $this->addSql('DROP TABLE distributeur');
        $this->addSql('DROP TABLE droits');
        $this->addSql('DROP TABLE enseignes');
        $this->addSql('DROP TABLE entreprises');
        $this->addSql('DROP TABLE factures');
        $this->addSql('DROP TABLE formule');
        $this->addSql('DROP TABLE formule_services');
        $this->addSql('DROP TABLE frais_operations');
        $this->addSql('DROP TABLE join_enseigne');
        $this->addSql('DROP TABLE join_enseigne_communaute');
        $this->addSql('DROP TABLE kits');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE particulier_profil_prospections');
        $this->addSql('DROP TABLE particuliers');
        $this->addSql('DROP TABLE pays');
        $this->addSql('DROP TABLE point_vente');
        $this->addSql('DROP TABLE profil_prospections');
        $this->addSql('DROP TABLE profiles');
        $this->addSql('DROP TABLE prospections');
        $this->addSql('DROP TABLE reglements_ecash');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE sous_compte');
        $this->addSql('DROP TABLE souscription_formules');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE virements_bancaires');
        $this->addSql('DROP TABLE virements_ecash');
        $this->addSql('DROP TABLE virements_inter_bancaire');
    }
}
