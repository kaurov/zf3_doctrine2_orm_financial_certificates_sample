<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170405004738 extends AbstractMigration
{
    
    /**
     * Returns the description of this migration.
     */
    public function getDescription()
    {
        $description = 'This is the initial migration which creates tables.';
        return $description;
    }
    
    
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        /*
        // Create 'post' table
        $table = $schema->createTable('post');
        $table->addColumn('id', 'integer', ['autoincrement'=>true]);        
        $table->addColumn('title', 'text', ['notnull'=>true]);
        $table->setPrimaryKey(['id']);
        $table->addOption('engine' , 'InnoDB');
        */
        
        $this->addSql("
            -- To create a new database, run MySQL client:
            --   mysql -u root -p
            -- Then in MySQL client command line, type the following (replace <password> with password string):
            --   create database blog;
            --   grant all privileges on blog.* to blog@localhost identified by '<password>';
            --   quit
            -- Then, in shell command line, type:
            --   mysql -u root -p blog < schema.mysql.sql

            set names 'utf8';




            -- Issuer
            CREATE TABLE `issuer` 
            (     
              `id` int(11) PRIMARY KEY AUTO_INCREMENT, -- Unique ID
              `title`   VARCHAR(250) NOT NULL Unique   -- Title 
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='utf8_general_ci';
            CREATE INDEX issuer_name ON issuer (title(10));

            INSERT INTO issuer(id, `title`) VALUES
            (1, 'German Technology GmbH'),
            (2, 'Ukrainian Birga Inc.');


            -- Trading markets
            CREATE TABLE `market` 
            (
              `id` int(11) PRIMARY KEY AUTO_INCREMENT, -- Unique ID
              `name`   VARCHAR(255) NOT NULL,          -- Title 
              UNIQUE KEY `name_key` (`name`)          -- market names must be unique.      
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='utf8_general_ci';
            CREATE INDEX market_name ON market (name(10));

            INSERT INTO market(id, `name`) VALUES(1, 'Frankfurt');
            INSERT INTO market(id, `name`) VALUES(2, 'London');
            INSERT INTO market(id, `name`) VALUES(3, 'Berlin');
            INSERT INTO market(id, `name`) VALUES(4, 'Kyiv');


            -- certificate
            CREATE TABLE `certificate` 
            (     
                `id`                int(11) PRIMARY KEY AUTO_INCREMENT, -- Unique ID
                `isin`              VARCHAR(20) UNIQUE NOT NULL,               -- ISIN 
                `title`             VARCHAR(250) NOT NULL,              
                `type`              ENUM('1','2','3') NOT NULL DEFAULT '1',              -- type. STANDARD = 1; GUARANTEE = 2; BONUS = 3; 
                `id_issuer`         int(11) NOT NULL,                   -- Issuer ID  
                `id_currency`       int(3) NOT NULL,                    -- Currency international code
                `price_issuing`     DECIMAL(7,2) NOT NULL,
                `price_current`     DECIMAL(7,2) NOT NULL,

                `participation_rate`  DECIMAL(7,2)    DEFAULT NULL,
                `barrier_level`       DECIMAL(7,2)    DEFAULT NULL

            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='utf8_general_ci';
            CREATE UNIQUE INDEX certificate_isin ON certificate (isin);
            CREATE INDEX certificate_title ON certificate (title(50));

            INSERT INTO certificate(id, isin, `title`, `type`, `id_issuer`, `id_currency`,`price_issuing`,`price_current`) 
            VALUES
            (
                1,
                'AU0000XVGZA3',
                'TREASURY CORP VICTORIA Sample of Certificate',
                1,
                2,
                840,
                123,123
            ),
            (
                2,
                '103000003133163510',
                'Another BAE Systems Simple of Certificate',
                1,
                1,
                840,
                123,123
            ),
            (
                3,
                'GB0002634946',
                'BAE Systems Simple of Certificate',
                1,
                2,
                978,
                123.33,123.33
            ),
            (
                4,
                'LT0002634946',
                'sample of GUARANTEE Certificate',
                2,
                1,
                978,
                256,256
            ),

            (
                5,
                'UA0002634946',
                'Sample of BONUS Certificate',
                3,
                2,
                978,
                2.34, 2.34
            )
            ;



            -- certificate_market 1 to many relation
            CREATE TABLE `certificate_market` 
            (     
              `id` int(11) PRIMARY KEY AUTO_INCREMENT,      -- Unique ID  
              `id_certificate` int(11),                     -- certificate id
              `id_market` int(11),                          -- market id
               UNIQUE KEY `unique_key` (`id_certificate`, `id_market`), -- Tag names must be unique.
               KEY `id_certificate_key` (`id_certificate`),
               KEY `id_market_key` (`id_market`)      
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='utf8_general_ci';
            CREATE INDEX ind_certificate ON certificate_market (id_certificate);
            CREATE INDEX ind_market ON certificate_market (id_market);

            INSERT INTO certificate_market(id_certificate, `id_market`) VALUES
            (1, 1),(1, 2),(1, 3),(1, 4),
            (2, 1),
            (3, 1),(3, 2),
            (4, 1),(4, 3),(4, 4),
            (5, 1);





            -- certificate
            CREATE TABLE `crt_price_history` 
            (     
                `id`        int(11) PRIMARY KEY AUTO_INCREMENT, -- Unique ID
                `id_certificate`    int(11),                     -- certificate id
                `price`             DECIMAL(7,2) NOT NULL,
                `date`              datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='utf8_general_ci';
            CREATE INDEX ind_crt_prices ON crt_price_history (id_certificate);

            DELIMITER $$
            DROP TRIGGER IF EXISTS `trigger_price_history`$$

            CREATE TRIGGER `trigger_price_history` AFTER UPDATE on `certificate`
            FOR EACH ROW BEGIN
                    IF NEW.price_current != OLD.price_current THEN
                            INSERT INTO crt_price_history (id_certificate, `date`, `price` ) 
                            VALUES 
                            (NEW.id, NOW(), NEW.price_current);
                    END IF;
            END$$

            DELIMITER ;

            UPDATE `certificate` SET `price_current`='8.34' WHERE `id`='5';
            UPDATE `certificate` SET `price_current`='129.00' WHERE `id`='1';


            INSERT INTO crt_price_history (id_certificate, price, `date`) VALUES 
            (1, 121, now()),
            (1, 118, '2017-03-05 02:51:23'),
            (1, 116, '2017-03-04 02:51:23'),
            (1, 112.36, '2017-03-01 02:51:23'),
            (1, 103.37, '2017-02-06 02:51:23'),

            (2, 123.37, '2017-02-06 02:51:23'),
            (2, 133.37, '2017-03-06 02:51:23'),
            (2, 143.37, '2017-04-06 02:51:23'),
            (2, 153.37, '2017-05-06 02:51:23'),

            (5, 12.99, '2017-02-06 02:51:23'),
            (5, 13.45, '2017-02-06 02:51:23'),
            (5, 16.76, '2017-02-06 02:51:23'),
            (5, 19.55, '2017-02-06 02:51:23'),
            (5, 22.34, '2017-02-06 02:51:23'),
            (5, 60.77, '2017-02-06 02:51:23'),
            (5, 75.34, '2017-02-06 02:51:23'),
            (5, 93.21, '2017-02-06 02:51:23'),
            (5, 99.34, '2017-02-06 02:51:23'),
            (5, 128.36, '2017-02-06 02:51:23');



            -- document
            CREATE TABLE `document` (     
                `id` int(11) PRIMARY KEY AUTO_INCREMENT, -- Unique ID  
                `id_certificate` int(11) NOT NULL,     -- Certificate ID this document belongs to  
                `type` varchar(128) NOT NULL, -- type
                `filename` varchar(128) NOT NULL, -- title of file
                `url` varchar(128) NOT NULL, -- path to file
                `date_created` datetime NOT NULL -- Creation date          
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='utf8_general_ci';
            CREATE INDEX ind_crt_docs ON document (id_certificate);

            INSERT INTO document
            (`id_certificate`, `type`, `filename`, `url`, `date_created`) 
            VALUES
            (1, 'pdf', 'Term Sheet', 'http://www.hbs.edu/newventurecompetition/business-track/Documents/FH%20Boston%20Financing%20HBS%20NVC.pdf', '2017-03-27 10:20'),
            (1, 'pdf', 'Another Term Sheet', 'http://www.hbs.edu/newventurecompetition/business-track/Documents/FH%20Boston%20Financing%20HBS%20NVC.pdf', '2017-03-30 10:20');


            UPDATE `zf3certificate`.`certificate` SET `price_current`='173.35' WHERE `id`='5';
            UPDATE `zf3certificate`.`certificate` SET `price_current`='252.00' WHERE `id`='4';
            UPDATE `zf3certificate`.`certificate` SET `price_current`='123.0' WHERE `id`='3';
            UPDATE `zf3certificate`.`certificate` SET `price_current`='125.00' WHERE `id`='2';
            UPDATE `zf3certificate`.`certificate` SET `price_current`='128.00' WHERE `id`='1';



            ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('document');
        $schema->dropTable('crt_price_history');
        $schema->dropSequence('trigger_price_history');
        $this->addSql("DROP TRIGGER IF EXISTS `trigger_price_history`");
        $schema->dropTable('certificate_market');
        $schema->dropTable('market');
        $schema->dropTable('issuer');
        $schema->dropTable('certificate');

    }
}
