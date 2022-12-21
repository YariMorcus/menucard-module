<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221212150946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menucard_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menucard_dish (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, highlighted TINYINT(1) NOT NULL, price_amount VARCHAR(255) DEFAULT \'0\' NOT NULL, price_currency_code CHAR(3) DEFAULT \'EUR\' NOT NULL COMMENT \'(DC2Type:currency)\', INDEX IDX_7BDF8EEC12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menucard_menucard (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_card_dish (menu_card_id INT NOT NULL, dish_id INT NOT NULL, INDEX IDX_600EE92EC217879E (menu_card_id), INDEX IDX_600EE92E148EB0CB (dish_id), PRIMARY KEY(menu_card_id, dish_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menucard_dish ADD CONSTRAINT FK_7BDF8EEC12469DE2 FOREIGN KEY (category_id) REFERENCES menucard_category (id)');
        $this->addSql('ALTER TABLE menu_card_dish ADD CONSTRAINT FK_600EE92EC217879E FOREIGN KEY (menu_card_id) REFERENCES menucard_menucard (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_card_dish ADD CONSTRAINT FK_600EE92E148EB0CB FOREIGN KEY (dish_id) REFERENCES menucard_dish (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
    }
}
