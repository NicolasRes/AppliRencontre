<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260311191713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration ADD utilisateur_id INT NOT NULL, CHANGE age_min age_min INT DEFAULT NULL, CHANGE age_max age_max INT DEFAULT NULL, CHANGE rayon rayon INT DEFAULT NULL, CHANGE genres_visibles genres_visibles JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_A5E2A5D7FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A5E2A5D7FB88E14F ON configuration (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration DROP FOREIGN KEY FK_A5E2A5D7FB88E14F');
        $this->addSql('DROP INDEX UNIQ_A5E2A5D7FB88E14F ON configuration');
        $this->addSql('ALTER TABLE configuration DROP utilisateur_id, CHANGE age_min age_min INT NOT NULL, CHANGE age_max age_max INT NOT NULL, CHANGE rayon rayon INT NOT NULL, CHANGE genres_visibles genres_visibles JSON NOT NULL');
    }
}
