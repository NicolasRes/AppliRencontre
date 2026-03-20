<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320180925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration CHANGE genres_visibles genres_visibles JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur DROP accord_gdpr');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration CHANGE genres_visibles genres_visibles JSON NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD accord_gdpr TINYINT NOT NULL');
    }
}
