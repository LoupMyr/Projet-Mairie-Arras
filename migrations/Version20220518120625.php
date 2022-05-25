<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220518120625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE identite ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE identite ADD CONSTRAINT FK_7E94B58BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7E94B58BA76ED395 ON identite (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE identite DROP FOREIGN KEY FK_7E94B58BA76ED395');
        $this->addSql('DROP INDEX IDX_7E94B58BA76ED395 ON identite');
        $this->addSql('ALTER TABLE identite DROP user_id');
    }
}
