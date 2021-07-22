<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210722123751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD classroom_id INT DEFAULT NULL, ADD subject_id INT NOT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D46278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D423EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('CREATE INDEX IDX_D044D5D46278D5A8 ON session (classroom_id)');
        $this->addSql('CREATE INDEX IDX_D044D5D423EDC87 ON session (subject_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D46278D5A8');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D423EDC87');
        $this->addSql('DROP INDEX IDX_D044D5D46278D5A8 ON session');
        $this->addSql('DROP INDEX IDX_D044D5D423EDC87 ON session');
        $this->addSql('ALTER TABLE session DROP classroom_id, DROP subject_id');
    }
}
