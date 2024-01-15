<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231006101335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE exercise_type_exercise_type_category (exercise_type_id INT NOT NULL, exercise_type_category_id INT NOT NULL, INDEX IDX_E656F9321F597BD6 (exercise_type_id), INDEX IDX_E656F932BEB79D48 (exercise_type_category_id), PRIMARY KEY(exercise_type_id, exercise_type_category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_exercise_type_category (user_id INT NOT NULL, exercise_type_category_id INT NOT NULL, INDEX IDX_3E966692A76ED395 (user_id), INDEX IDX_3E966692BEB79D48 (exercise_type_category_id), PRIMARY KEY(user_id, exercise_type_category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE exercise_type_exercise_type_category ADD CONSTRAINT FK_E656F9321F597BD6 FOREIGN KEY (exercise_type_id) REFERENCES exercise_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_type_exercise_type_category ADD CONSTRAINT FK_E656F932BEB79D48 FOREIGN KEY (exercise_type_category_id) REFERENCES exercise_type_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_exercise_type_category ADD CONSTRAINT FK_3E966692A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_exercise_type_category ADD CONSTRAINT FK_3E966692BEB79D48 FOREIGN KEY (exercise_type_category_id) REFERENCES exercise_type_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exercise_type DROP FOREIGN KEY FK_D5FB359B12469DE2');
        $this->addSql('DROP INDEX IDX_D5FB359B12469DE2 ON exercise_type');
        $this->addSql('ALTER TABLE exercise_type DROP category_id');
        $this->addSql('ALTER TABLE exercise_type_category ADD global TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD evaluate_global_categories TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exercise_type_exercise_type_category DROP FOREIGN KEY FK_E656F9321F597BD6');
        $this->addSql('ALTER TABLE exercise_type_exercise_type_category DROP FOREIGN KEY FK_E656F932BEB79D48');
        $this->addSql('ALTER TABLE user_exercise_type_category DROP FOREIGN KEY FK_3E966692A76ED395');
        $this->addSql('ALTER TABLE user_exercise_type_category DROP FOREIGN KEY FK_3E966692BEB79D48');
        $this->addSql('DROP TABLE exercise_type_exercise_type_category');
        $this->addSql('DROP TABLE user_exercise_type_category');
        $this->addSql('ALTER TABLE exercise_type ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exercise_type ADD CONSTRAINT FK_D5FB359B12469DE2 FOREIGN KEY (category_id) REFERENCES exercise_type_category (id)');
        $this->addSql('CREATE INDEX IDX_D5FB359B12469DE2 ON exercise_type (category_id)');
        $this->addSql('ALTER TABLE exercise_type_category DROP global');
        $this->addSql('ALTER TABLE user DROP evaluate_global_categories');
    }
}
