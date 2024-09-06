<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240820191622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E929C1004E FOREIGN KEY (video_id) REFERENCES videos (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E929C1004E ON users (video_id)');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA643229C1004E');
        $this->addSql('DROP INDEX IDX_29AA643229C1004E ON videos');
        $this->addSql('ALTER TABLE videos CHANGE user_id video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA643229C1004E FOREIGN KEY (video_id) REFERENCES videos (id)');
        $this->addSql('CREATE INDEX IDX_29AA643229C1004E ON videos (video_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E929C1004E');
        $this->addSql('DROP INDEX IDX_1483A5E929C1004E ON users');
        $this->addSql('ALTER TABLE users DROP video_id');
        $this->addSql('ALTER TABLE videos DROP FOREIGN KEY FK_29AA643229C1004E');
        $this->addSql('DROP INDEX IDX_29AA643229C1004E ON videos');
        $this->addSql('ALTER TABLE videos CHANGE video_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE videos ADD CONSTRAINT FK_29AA643229C1004E FOREIGN KEY (user_id) REFERENCES videos (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_29AA643229C1004E ON videos (user_id)');
    }
}
