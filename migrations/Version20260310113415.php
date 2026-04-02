<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260310113415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration CHANGE genres_visibles genres_visibles JSON NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE lien_photo lien_photo VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE image_identite image_identite VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configuration CHANGE genres_visibles genres_visibles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE message CHANGE lien_photo lien_photo VARCHAR(50) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE utilisateur CHANGE image_identite image_identite VARCHAR(50) DEFAULT \'NULL\'');
    }
}
