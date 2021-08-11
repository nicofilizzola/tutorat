<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811093454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_code (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom (id INT AUTO_INCREMENT NOT NULL, faculty_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_497D309D680CAB68 (faculty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faculty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, short VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, tutor_id INT NOT NULL, classroom_id INT DEFAULT NULL, subject_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, face_to_face INT NOT NULL, link VARCHAR(255) DEFAULT NULL, participants LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', date_time DATETIME NOT NULL, student_limit INT NOT NULL, is_valid TINYINT(1) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, time_format INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_D044D5D4208F64F1 (tutor_id), INDEX IDX_D044D5D46278D5A8 (classroom_id), INDEX IDX_D044D5D423EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_user (session_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4BE2D663613FECDF (session_id), INDEX IDX_4BE2D663A76ED395 (user_id), PRIMARY KEY(session_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, faculty_id INT NOT NULL, title VARCHAR(255) NOT NULL, semester INT NOT NULL, short VARCHAR(255) NOT NULL, INDEX IDX_FBCE3E7A680CAB68 (faculty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, faculty_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, year INT NOT NULL, is_valid INT NOT NULL, is_verified TINYINT(1) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649680CAB68 (faculty_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classroom ADD CONSTRAINT FK_497D309D680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4208F64F1 FOREIGN KEY (tutor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D46278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D423EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE session_user ADD CONSTRAINT FK_4BE2D663613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_user ADD CONSTRAINT FK_4BE2D663A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649680CAB68 FOREIGN KEY (faculty_id) REFERENCES faculty (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D46278D5A8');
        $this->addSql('ALTER TABLE classroom DROP FOREIGN KEY FK_497D309D680CAB68');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A680CAB68');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649680CAB68');
        $this->addSql('ALTER TABLE session_user DROP FOREIGN KEY FK_4BE2D663613FECDF');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D423EDC87');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4208F64F1');
        $this->addSql('ALTER TABLE session_user DROP FOREIGN KEY FK_4BE2D663A76ED395');
        $this->addSql('DROP TABLE admin_code');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('DROP TABLE faculty');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE session_user');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE `user`');
    }
}
