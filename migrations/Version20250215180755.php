<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215180755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aide ADD form_id INT NOT NULL, CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE aide ADD CONSTRAINT FK_D99184A15FF69B7D FOREIGN KEY (form_id) REFERENCES form_p (id)');
        $this->addSql('CREATE INDEX IDX_D99184A15FF69B7D ON aide (form_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aide DROP FOREIGN KEY FK_D99184A15FF69B7D');
        $this->addSql('DROP INDEX IDX_D99184A15FF69B7D ON aide');
        $this->addSql('ALTER TABLE aide DROP form_id, CHANGE description description VARCHAR(255) NOT NULL');
    }
}
