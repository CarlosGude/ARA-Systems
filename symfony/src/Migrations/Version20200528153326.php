<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200528153326 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_provider ADD company_id VARCHAR(255) DEFAULT NULL, ADD user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_provider ADD CONSTRAINT FK_5974190B979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE product_provider ADD CONSTRAINT FK_5974190BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5974190B979B1AD6 ON product_provider (company_id)');
        $this->addSql('CREATE INDEX IDX_5974190BA76ED395 ON product_provider (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_provider DROP FOREIGN KEY FK_5974190B979B1AD6');
        $this->addSql('ALTER TABLE product_provider DROP FOREIGN KEY FK_5974190BA76ED395');
        $this->addSql('DROP INDEX IDX_5974190B979B1AD6 ON product_provider');
        $this->addSql('DROP INDEX IDX_5974190BA76ED395 ON product_provider');
        $this->addSql('ALTER TABLE product_provider DROP company_id, DROP user_id');
    }
}
