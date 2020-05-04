<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200428104758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add images to client';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client ADD image_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404553DA5256D FOREIGN KEY (image_id) REFERENCES media_object (id)');
        $this->addSql('CREATE INDEX IDX_C74404553DA5256D ON client (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404553DA5256D');
        $this->addSql('DROP INDEX IDX_C74404553DA5256D ON client');
        $this->addSql('ALTER TABLE client DROP image_id');
    }
}
