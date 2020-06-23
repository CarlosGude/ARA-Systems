<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200327033625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add purchase line';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE purchase_line (id VARCHAR(255) NOT NULL, purchase_id VARCHAR(255) NOT NULL, company_id VARCHAR(255) NOT NULL, user_id VARCHAR(255) DEFAULT NULL, product_id VARCHAR(255) NOT NULL, provider_id VARCHAR(255) NOT NULL, tax INT NOT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A1A77C95558FBEB9 (purchase_id), INDEX IDX_A1A77C95979B1AD6 (company_id), INDEX IDX_A1A77C95A76ED395 (user_id), INDEX IDX_A1A77C954584665A (product_id), INDEX IDX_A1A77C95A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_line ADD CONSTRAINT FK_A1A77C95558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
        $this->addSql('ALTER TABLE purchase_line ADD CONSTRAINT FK_A1A77C95979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE purchase_line ADD CONSTRAINT FK_A1A77C95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchase_line ADD CONSTRAINT FK_A1A77C954584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE purchase_line ADD CONSTRAINT FK_A1A77C95A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE purchase_line');
    }
}
