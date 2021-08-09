<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210809082732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resident_complaint_board_case ADD complainant_cpr VARCHAR(255) NOT NULL, ADD has_vacated TINYINT(1) NOT NULL, ADD lease_address VARCHAR(255) NOT NULL, ADD lease_zip VARCHAR(255) NOT NULL, ADD lease_city VARCHAR(255) NOT NULL, ADD lease_started DATETIME DEFAULT NULL, ADD lease_agreed_rent INT DEFAULT NULL, ADD lease_interior_maintenance VARCHAR(255) DEFAULT NULL, ADD lease_regulated_rent INT DEFAULT NULL, ADD lease_rent_at_collection_time INT DEFAULT NULL, ADD lease_security_deposit INT DEFAULT NULL, ADD previous_cases_at_lease LONGTEXT DEFAULT NULL, ADD prepaid_rent INT NOT NULL, ADD fee_paid TINYINT(1) DEFAULT NULL, CHANGE lease_size lease_size INT DEFAULT NULL, CHANGE complainant complainant VARCHAR(255) NOT NULL, CHANGE complainant_address complainant_address VARCHAR(255) NOT NULL, CHANGE complainant_phone complainant_phone INT NOT NULL, CHANGE complainant_zip complainant_zip VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resident_complaint_board_case DROP complainant_cpr, DROP has_vacated, DROP lease_address, DROP lease_zip, DROP lease_city, DROP lease_started, DROP lease_agreed_rent, DROP lease_interior_maintenance, DROP lease_regulated_rent, DROP lease_rent_at_collection_time, DROP lease_security_deposit, DROP previous_cases_at_lease, DROP prepaid_rent, DROP fee_paid, CHANGE lease_size lease_size INT NOT NULL, CHANGE complainant complainant VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE complainant_address complainant_address VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE complainant_phone complainant_phone INT DEFAULT NULL, CHANGE complainant_zip complainant_zip VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
