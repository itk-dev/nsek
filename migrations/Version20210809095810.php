<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210809095810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fence_review_case (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', complainant VARCHAR(255) NOT NULL, complainant_address VARCHAR(255) NOT NULL, complainant_zip VARCHAR(255) NOT NULL, case_state VARCHAR(255) DEFAULT \'submitted\' NOT NULL, complainant_cpr VARCHAR(255) NOT NULL, conditions LONGTEXT NOT NULL, complainant_claim LONGTEXT NOT NULL, complainant_cadastral_number VARCHAR(255) NOT NULL, accused VARCHAR(255) NOT NULL, accused_address VARCHAR(255) NOT NULL, accused_cpr VARCHAR(255) NOT NULL, accused_cadastral_number VARCHAR(255) DEFAULT NULL, accused_zip VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fence_review_case ADD CONSTRAINT FK_ADDF7F8EBF396750 FOREIGN KEY (id) REFERENCES case_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fence_review_case');
    }
}
