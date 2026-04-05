<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260402072702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY `FK_B6BD307F60BB6FE6`');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY `FK_B6BD307F6CFC0818`');
        $this->addSql('DROP INDEX IDX_B6BD307F6CFC0818 ON message');
        $this->addSql('DROP INDEX IDX_B6BD307F60BB6FE6 ON message');
        $this->addSql('ALTER TABLE message DROP contenu, DROP temps, DROP lien_photo, DROP est_lu, DROP rencontre_id, DROP auteur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message ADD contenu LONGTEXT NOT NULL, ADD temps DATE NOT NULL, ADD lien_photo VARCHAR(50) DEFAULT NULL, ADD est_lu TINYINT NOT NULL, ADD rencontre_id INT NOT NULL, ADD auteur_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT `FK_B6BD307F60BB6FE6` FOREIGN KEY (auteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT `FK_B6BD307F6CFC0818` FOREIGN KEY (rencontre_id) REFERENCES rencontre (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F6CFC0818 ON message (rencontre_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F60BB6FE6 ON message (auteur_id)');
    }
}
