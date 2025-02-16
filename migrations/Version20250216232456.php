<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216232456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aide (id INT AUTO_INCREMENT NOT NULL, form_id INT NOT NULL, sujet VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_D99184A15FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_p (id INT AUTO_INCREMENT NOT NULL, contenu VARCHAR(255) NOT NULL, date_pub DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', sujet VARCHAR(255) NOT NULL, auteur VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE aide ADD CONSTRAINT FK_D99184A15FF69B7D FOREIGN KEY (form_id) REFERENCES form_p (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aide DROP FOREIGN KEY FK_D99184A15FF69B7D');
        $this->addSql('DROP TABLE aide');
        $this->addSql('DROP TABLE form_p');
    }
}
