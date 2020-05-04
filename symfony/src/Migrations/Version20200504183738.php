<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504183738 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_media_object DROP FOREIGN KEY FK_997013594584665A');
        $this->addSql('ALTER TABLE product_media_object DROP FOREIGN KEY FK_9970135964DE5A5');
        $this->addSql('ALTER TABLE product_media_object DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE product_media_object ADD id VARCHAR(255) NOT NULL, CHANGE product_id product_id VARCHAR(255) DEFAULT NULL, CHANGE media_object_id media_object_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_media_object ADD CONSTRAINT FK_997013594584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_media_object ADD CONSTRAINT FK_9970135964DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE product_media_object ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_media_object DROP FOREIGN KEY FK_997013594584665A');
        $this->addSql('ALTER TABLE product_media_object DROP FOREIGN KEY FK_9970135964DE5A5');
        $this->addSql('ALTER TABLE product_media_object DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE product_media_object DROP id, CHANGE product_id product_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE media_object_id media_object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE product_media_object ADD CONSTRAINT FK_997013594584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_media_object ADD CONSTRAINT FK_9970135964DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_media_object ADD PRIMARY KEY (product_id, media_object_id)');
    }
}
