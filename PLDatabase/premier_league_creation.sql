ALTER TABLE Players DROP FOREIGN KEY Players_Club;
ALTER TABLE Manager DROP FOREIGN KEY Manager_Club;
ALTER TABLE Stats DROP FOREIGN KEY Stats_Club;
ALTER TABLE Sponsors DROP FOREIGN KEY Sponsors_Club;

DROP TABLE IF EXISTS Club, Players, Manager, Stats, Sponsors;

CREATE TABLE Club(
    ClubID TINYINT(2) UNSIGNED NOT NULL AUTO_INCREMENT,
    ClubName VARCHAR(25) NOT NULL,
    Stadium VARCHAR(25),
    PRIMARY KEY(ClubID)
)ENGINE = InnoDB;

CREATE TABLE Players(
    ClubID TINYINT(2) UNSIGNED NOT NULL,
    ShirtNum TINYINT(2) UNSIGNED NOT NULL,
    FName VARCHAR(20),
    LName VARCHAR(20),
    DOB DATE,
    PrimaryPos ENUM('Goalkeeper', 'Defender', 'Midfielder', 'Forward', 'Fullback', 'Left Back', 'Right Back', 'Center Back', 'Defensive Midfielder', 'Central Midfielder', 'Left Midfielder', 'Right Midfielder', 'Wide Midfielder', 'Left Winger', 'Right Winger', 'Attacking Midfielder', 'Winger'),
    Salary INTEGER(9) UNSIGNED,
    CONSTRAINT PK_Players PRIMARY KEY (ClubID, ShirtNum)
)ENGINE = InnoDB;
ALTER TABLE Players ADD CONSTRAINT Players_Club FOREIGN KEY (ClubID) REFERENCES Club(ClubID) ON DELETE CASCADE;

CREATE TABLE Manager(
    ClubID TINYINT(2) UNSIGNED NOT NULL,
    FName VARCHAR(20),
    LName VARCHAR(20),
    Salary FLOAT(9) UNSIGNED,
    YearsInPosition TINYINT(2) UNSIGNED,
    PRIMARY KEY(ClubID)
)ENGINE = InnoDB;
ALTER TABLE Manager ADD CONSTRAINT Manager_Club FOREIGN KEY (ClubID) REFERENCES Club(ClubID) ON DELETE CASCADE;

CREATE TABLE Stats(
    ClubID TINYINT(2) UNSIGNED NOT NULL,
    Wins TINYINT(2) UNSIGNED,
    Draws TINYINT(2) UNSIGNED,
    Losses TINYINT(2) UNSIGNED,
    GD TINYINT(2),
    PRIMARY KEY(ClubID)
)ENGINE = InnoDB;
ALTER TABLE Stats ADD CONSTRAINT Stats_Club FOREIGN KEY (ClubID) REFERENCES Club(ClubID) ON DELETE CASCADE;

CREATE TABLE Sponsors(
    ClubID TINYINT(2) UNSIGNED NOT NULL,
    SponsorName VARCHAR(20) NOT NULL,
    SponsorType ENUM('Shirt', 'Kit'),
    StartDate YEAR,
    CONSTRAINT PK_Sponsors PRIMARY KEY (ClubID, SponsorType)
)ENGINE = InnoDB;
ALTER TABLE Sponsors ADD CONSTRAINT Sponsors_Club FOREIGN KEY (ClubID) REFERENCES Club(ClubID) ON DELETE CASCADE; 