<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260326080113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moderateur DROP FOREIGN KEY `FK_6DDC3554FB88E14F`');
        $this->addSql('DROP TABLE moderateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE moderateur (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_6DDC3554FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE moderateur ADD CONSTRAINT `FK_6DDC3554FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
    }
}
