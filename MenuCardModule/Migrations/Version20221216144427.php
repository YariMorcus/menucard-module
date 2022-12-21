<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221216144427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menucard_page (id INT AUTO_INCREMENT NOT NULL, structure_id INT NOT NULL, menucard_id INT DEFAULT NULL, text LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, INDEX IDX_FAA8B4742534008B (structure_id), INDEX IDX_FAA8B47460B8257B (menucard_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menucard_page ADD CONSTRAINT FK_FAA8B4742534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('ALTER TABLE menucard_page ADD CONSTRAINT FK_FAA8B47460B8257B FOREIGN KEY (menucard_id) REFERENCES menucard_menucard (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
