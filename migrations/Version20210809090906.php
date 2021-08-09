<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210809090906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rent_board_case (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', lease_size INT DEFAULT NULL, complainant VARCHAR(255) NOT NULL, complainant_address VARCHAR(255) NOT NULL, complainant_phone INT NOT NULL, complainant_zip VARCHAR(255) NOT NULL, case_state VARCHAR(255) DEFAULT \'submitted\' NOT NULL, complainant_cpr VARCHAR(255) NOT NULL, has_vacated TINYINT(1) NOT NULL, lease_address VARCHAR(255) NOT NULL, lease_zip VARCHAR(255) NOT NULL, lease_city VARCHAR(255) NOT NULL, lease_started DATETIME DEFAULT NULL, lease_agreed_rent INT DEFAULT NULL, lease_interior_maintenance VARCHAR(255) DEFAULT NULL, lease_regulated_rent INT DEFAULT NULL, lease_rent_at_collection_time INT DEFAULT NULL, lease_security_deposit INT DEFAULT NULL, previous_cases_at_lease LONGTEXT DEFAULT NULL, prepaid_rent INT DEFAULT NULL, fee_paid TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rent_board_case ADD CONSTRAINT FK_87C6448BF396750 FOREIGN KEY (id) REFERENCES case_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE resident_complaint_board_case CHANGE prepaid_rent prepaid_rent INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rent_board_case');
        $this->addSql('ALTER TABLE resident_complaint_board_case CHANGE prepaid_rent prepaid_rent INT NOT NULL');
    }
}
