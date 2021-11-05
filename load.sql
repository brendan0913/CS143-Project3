DROP TABLE IF EXISTS Laureate;
DROP TABLE IF EXISTS Person;
DROP TABLE IF EXISTS Birth;
DROP TABLE IF EXISTS Organization;
DROP TABLE IF EXISTS Affiliation;
DROP TABLE IF EXISTS Prize;

-- Laureate(lid, nid)
-- Person(lid, givenName, familyName, gender)
-- Organization(lid, orgName)
-- Birth(lid, date, city, country) // Also used for organization foundings
-- Prize(nid, awardYear, category, sortOrder, affilId)
-- Affiliation(affilId, name, city, country)

CREATE TABLE Laureate(lid INT NOT NULL,
                    nid INT NOT NULL,
                    PRIMARY KEY(lid, nid));

CREATE TABLE Person(lid INT PRIMARY KEY NOT NULL,
                    givenName VARCHAR(50),
                    familyName VARCHAR(50),
                    gender VARCHAR(10));

CREATE TABLE Organization(lid INT PRIMARY KEY NOT NULL,
                          orgName VARCHAR(100));

CREATE TABLE Birth(lid INT PRIMARY KEY NOT NULL,
                    date DATE,
                    city VARCHAR(50),
                    country VARCHAR(50));

CREATE TABLE Prize(nid INT NOT NULL,
                    awardYear VARCHAR(20),
                    category VARCHAR(50),
                    sortOrder INT,
                    affilId INT);

CREATE TABLE Affiliation(affilId INT PRIMARY KEY NOT NULL,
                        name VARCHAR(120),
                        city VARCHAR(50),
                        country VARCHAR(50));

LOAD DATA LOCAL INFILE './Laureate.del' INTO TABLE Laureate FIELDS TERMINATED BY '|';
LOAD DATA LOCAL INFILE './Person.del' INTO TABLE Person FIELDS TERMINATED BY '|';
LOAD DATA LOCAL INFILE './Organization.del' INTO TABLE Organization FIELDS TERMINATED BY '|';
LOAD DATA LOCAL INFILE './Birth.del' INTO TABLE Birth FIELDS TERMINATED BY '|';
LOAD DATA LOCAL INFILE './Prize.del' INTO TABLE Prize FIELDS TERMINATED BY '|';
LOAD DATA LOCAL INFILE './Affiliation.del' INTO TABLE Affiliation FIELDS TERMINATED BY '|';
