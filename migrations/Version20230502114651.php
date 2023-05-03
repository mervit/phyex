<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502114651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figurant ADD nickname VARCHAR(255) DEFAULT NULL, ADD age VARCHAR(255) NOT NULL, ADD sport_hours_per_week VARCHAR(255) NOT NULL, DROP firstname, DROP surname, DROP birth_year, CHANGE weight weight VARCHAR(255) NOT NULL, CHANGE height height VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD completed_education_level VARCHAR(255) NOT NULL, ADD completed_education_university_name VARCHAR(255) DEFAULT NULL, ADD completed_education_faculty_name VARCHAR(255) DEFAULT NULL, DROP student');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figurant ADD firstname VARCHAR(255) NOT NULL, ADD surname VARCHAR(255) NOT NULL, ADD birth_year INT NOT NULL, DROP nickname, DROP age, DROP sport_hours_per_week, CHANGE weight weight INT NOT NULL, CHANGE height height INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD student TINYINT(1) NOT NULL, DROP completed_education_level, DROP completed_education_university_name, DROP completed_education_faculty_name');
    }
}
