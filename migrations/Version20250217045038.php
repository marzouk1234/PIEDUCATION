<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217045038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, nbchapitre INT NOT NULL, description LONGTEXT NOT NULL, date DATE NOT NULL, pdf VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FDCA8C9CFF7747B4 (titre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, date DATE NOT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, date_inscription DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription_cours (inscription_id INT NOT NULL, cours_id INT NOT NULL, INDEX IDX_AF83D8D15DAC5993 (inscription_id), INDEX IDX_AF83D8D17ECF78B0 (cours_id), PRIMARY KEY(inscription_id, cours_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resultat (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT NOT NULL, note INT NOT NULL, date_creation DATE NOT NULL, appreciation VARCHAR(255) NOT NULL, INDEX IDX_E7DB5DE2456C5646 (evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, nom VARCHAR(180) NOT NULL, prenom VARCHAR(180) NOT NULL, date_naissance DATE DEFAULT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription_cours ADD CONSTRAINT FK_AF83D8D15DAC5993 FOREIGN KEY (inscription_id) REFERENCES inscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inscription_cours ADD CONSTRAINT FK_AF83D8D17ECF78B0 FOREIGN KEY (cours_id) REFERENCES cours (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2456C5646 FOREIGN KEY (evaluation_id) REFERENCES evaluation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription_cours DROP FOREIGN KEY FK_AF83D8D15DAC5993');
        $this->addSql('ALTER TABLE inscription_cours DROP FOREIGN KEY FK_AF83D8D17ECF78B0');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2456C5646');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE inscription_cours');
        $this->addSql('DROP TABLE resultat');
        $this->addSql('DROP TABLE user');
    }
}
