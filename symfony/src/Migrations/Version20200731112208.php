<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200731112208 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add SIZE Entity';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE size (id VARCHAR(255) NOT NULL, company_id VARCHAR(255) DEFAULT NULL, user_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, reference VARCHAR(2) NOT NULL, UNIQUE INDEX UNIQ_F7C0246A989D9B62 (slug), INDEX IDX_F7C0246A979B1AD6 (company_id), INDEX IDX_F7C0246AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE size ADD CONSTRAINT FK_F7C0246A979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE size ADD CONSTRAINT FK_F7C0246AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE image_id image_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE color CHANGE company_id company_id VARCHAR(255) DEFAULT NULL, CHANGE user_id user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase CHANGE user_id user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_line CHANGE user_id user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_media_object CHANGE product_id product_id VARCHAR(255) DEFAULT NULL, CHANGE media_object_id media_object_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE media_object CHANGE file_path file_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE user_id user_id VARCHAR(255) DEFAULT NULL, CHANGE company_id company_id VARCHAR(255) DEFAULT NULL, CHANGE image_id image_id VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_provider CHANGE product_id product_id VARCHAR(255) DEFAULT NULL, CHANGE provider_id provider_id VARCHAR(255) DEFAULT NULL, CHANGE user_id user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company CHANGE image_id image_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE category CHANGE user_id user_id VARCHAR(255) DEFAULT NULL, CHANGE tax tax INT DEFAULT NULL, CHANGE min_stock min_stock INT DEFAULT NULL, CHANGE max_stock max_stock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE user_id user_id VARCHAR(255) DEFAULT NULL, CHANGE image_id image_id VARCHAR(255) DEFAULT NULL, CHANGE product_length product_length DOUBLE PRECISION DEFAULT NULL, CHANGE product_height product_height DOUBLE PRECISION DEFAULT NULL, CHANGE product_width product_width DOUBLE PRECISION DEFAULT NULL, CHANGE kilograms kilograms DOUBLE PRECISION DEFAULT NULL, CHANGE location location VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE provider CHANGE company_id company_id VARCHAR(255) DEFAULT NULL, CHANGE image_id image_id VARCHAR(255) DEFAULT NULL, CHANGE user_id user_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE size');
        $this->addSql('ALTER TABLE category CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE tax tax INT DEFAULT NULL, CHANGE min_stock min_stock INT DEFAULT NULL, CHANGE max_stock max_stock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE image_id image_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE company_id company_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE address address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE color CHANGE company_id company_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE company CHANGE image_id image_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE media_object CHANGE file_path file_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE image_id image_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE product_length product_length DOUBLE PRECISION DEFAULT \'NULL\', CHANGE product_height product_height DOUBLE PRECISION DEFAULT \'NULL\', CHANGE product_width product_width DOUBLE PRECISION DEFAULT \'NULL\', CHANGE kilograms kilograms DOUBLE PRECISION DEFAULT \'NULL\', CHANGE location location VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product_media_object CHANGE product_id product_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE media_object_id media_object_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product_provider CHANGE product_id product_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE provider_id provider_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE provider CHANGE image_id image_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE company_id company_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE purchase CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE purchase_line CHANGE user_id user_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE image_id image_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
