<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205114504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE configuration (id INT AUTO_INCREMENT NOT NULL, age_min INT NOT NULL, age_max INT NOT NULL, rayon INT NOT NULL, genres_visibles JSON NOT NULL, etat_notif TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lienss (id INT AUTO_INCREMENT NOT NULL, exp_date DATE NOT NULL, utilise TINYINT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_D9799016FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, temps DATE NOT NULL, lien_photo VARCHAR(50) DEFAULT NULL, est_lu TINYINT NOT NULL, rencontre_id INT NOT NULL, auteur_id INT NOT NULL, INDEX IDX_B6BD307F6CFC0818 (rencontre_id), INDEX IDX_B6BD307F60BB6FE6 (auteur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE moderateur (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_6DDC3554FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, contenu VARCHAR(50) NOT NULL, type INT NOT NULL, lu TINYINT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_BF5476CAFB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE photo_de_profil (id INT AUTO_INCREMENT NOT NULL, profil_id INT DEFAULT NULL, INDEX IDX_F0FAB692275ED078 (profil_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE photo_profil (id INT AUTO_INCREMENT NOT NULL, lien_photo VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, age INT NOT NULL, genre VARCHAR(30) NOT NULL, ville VARCHAR(50) NOT NULL, presentation LONGTEXT DEFAULT NULL, utilisateur_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E6D6B297FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rencontre (id INT AUTO_INCREMENT NOT NULL, statut INT NOT NULL, date_creation DATE NOT NULL, utilisateur_id INT NOT NULL, utilisateur2_id INT NOT NULL, INDEX IDX_460C35EDFB88E14F (utilisateur_id), INDEX IDX_460C35ED2241569D (utilisateur2_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE signalement (id INT AUTO_INCREMENT NOT NULL, date_s DATE NOT NULL, statut INT NOT NULL, motif VARCHAR(50) NOT NULL, auteur_id INT NOT NULL, cible_id INT NOT NULL, INDEX IDX_F4B5511460BB6FE6 (auteur_id), INDEX IDX_F4B55114A96E5E09 (cible_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, mdp VARCHAR(255) NOT NULL, image_identite VARCHAR(50) DEFAULT NULL, accord_gdpr TINYINT NOT NULL, is_modo TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE lienss ADD CONSTRAINT FK_D9799016FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F6CFC0818 FOREIGN KEY (rencontre_id) REFERENCES rencontre (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE moderateur ADD CONSTRAINT FK_6DDC3554FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE photo_de_profil ADD CONSTRAINT FK_F0FAB692275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE rencontre ADD CONSTRAINT FK_460C35EDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE rencontre ADD CONSTRAINT FK_460C35ED2241569D FOREIGN KEY (utilisateur2_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B5511460BB6FE6 FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114A96E5E09 FOREIGN KEY (cible_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lienss DROP FOREIGN KEY FK_D9799016FB88E14F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F6CFC0818');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F60BB6FE6');
        $this->addSql('ALTER TABLE moderateur DROP FOREIGN KEY FK_6DDC3554FB88E14F');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAFB88E14F');
        $this->addSql('ALTER TABLE photo_de_profil DROP FOREIGN KEY FK_F0FAB692275ED078');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297FB88E14F');
        $this->addSql('ALTER TABLE rencontre DROP FOREIGN KEY FK_460C35EDFB88E14F');
        $this->addSql('ALTER TABLE rencontre DROP FOREIGN KEY FK_460C35ED2241569D');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B5511460BB6FE6');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B55114A96E5E09');
        $this->addSql('DROP TABLE configuration');
        $this->addSql('DROP TABLE lienss');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE moderateur');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE photo_de_profil');
        $this->addSql('DROP TABLE photo_profil');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE rencontre');
        $this->addSql('DROP TABLE signalement');
        $this->addSql('DROP TABLE utilisateur');
    }
}
