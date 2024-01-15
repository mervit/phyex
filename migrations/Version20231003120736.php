<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231003120736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exercise_type_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exercise_type ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise_type ADD CONSTRAINT FK_D5FB359B12469DE2 FOREIGN KEY (category_id) REFERENCES exercise_type_category (id)');
        $this->addSql('CREATE INDEX IDX_D5FB359B12469DE2 ON exercise_type (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercise_type DROP FOREIGN KEY FK_D5FB359B12469DE2');
        $this->addSql('DROP TABLE exercise_type_category');
        $this->addSql('DROP INDEX IDX_D5FB359B12469DE2 ON exercise_type');
        $this->addSql('ALTER TABLE exercise_type DROP category_id');
    }
}
