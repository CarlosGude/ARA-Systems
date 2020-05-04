<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504161419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add reference to client, product and provider';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client ADD reference VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD barcode VARCHAR(255) NOT NULL, CHANGE reference reference INT NOT NULL');
        $this->addSql('ALTER TABLE provider ADD reference VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE client DROP reference');
        $this->addSql('ALTER TABLE product DROP barcode, CHANGE reference reference VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE provider DROP reference');
    }
}
