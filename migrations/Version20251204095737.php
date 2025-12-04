<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251204095737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE enigma (id INT AUTO_INCREMENT NOT NULL, `order` INT NOT NULL, title VARCHAR(255) NOT NULL, instruction LONGTEXT NOT NULL, secret_code VARCHAR(255) NOT NULL, data JSON NOT NULL, type_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_2EA9D76EC54C8C93 (type_id), INDEX IDX_2EA9D76EE48FD905 (game_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, welcome_msg LONGTEXT NOT NULL, welcome_img VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, is_game_started TINYINT NOT NULL, game_duration INT NOT NULL, started_at DATETIME DEFAULT NULL, final_code VARCHAR(255) NOT NULL, game_id INT NOT NULL, UNIQUE INDEX UNIQ_9F74B898E48FD905 (game_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, position INT DEFAULT NULL, current_enigma INT NOT NULL, note LONGTEXT DEFAULT NULL, started_at DATETIME DEFAULT NULL, finished_at DATETIME DEFAULT NULL, game_id INT NOT NULL, avatar_id INT NOT NULL, INDEX IDX_C4E0A61FE48FD905 (game_id), INDEX IDX_C4E0A61F86383B10 (avatar_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE thumbnail (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) NOT NULL, information LONGTEXT DEFAULT NULL, enigma_id INT NOT NULL, INDEX IDX_C35726E6457B6BA0 (enigma_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE enigma ADD CONSTRAINT FK_2EA9D76EC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE enigma ADD CONSTRAINT FK_2EA9D76EE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE setting ADD CONSTRAINT FK_9F74B898E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F86383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id)');
        $this->addSql('ALTER TABLE thumbnail ADD CONSTRAINT FK_C35726E6457B6BA0 FOREIGN KEY (enigma_id) REFERENCES enigma (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enigma DROP FOREIGN KEY FK_2EA9D76EC54C8C93');
        $this->addSql('ALTER TABLE enigma DROP FOREIGN KEY FK_2EA9D76EE48FD905');
        $this->addSql('ALTER TABLE setting DROP FOREIGN KEY FK_9F74B898E48FD905');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F86383B10');
        $this->addSql('ALTER TABLE thumbnail DROP FOREIGN KEY FK_C35726E6457B6BA0');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE enigma');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE thumbnail');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
