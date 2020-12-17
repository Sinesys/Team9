DROP DOMAIN IF EXISTS birthdate CASCADE;
DROP DOMAIN IF EXISTS user_role CASCADE;
DROP DOMAIN IF EXISTS expires_date CASCADE;
DROP TABLE IF EXISTS system_user CASCADE;
DROP TABLE IF EXISTS authentication CASCADE;
DROP TABLE IF EXISTS user_info CASCADE;
DROP TABLE IF EXISTS maintainer CASCADE;
DROP TABLE IF EXISTS competence CASCADE;
DROP TABLE IF EXISTS mastery CASCADE;
DROP TABLE IF EXISTS access_log CASCADE;
DROP TABLE IF EXISTS activity CASCADE;
DROP TABLE IF EXISTS assignment CASCADE;
DROP TABLE IF EXISTS procedure CASCADE;
DROP TABLE IF EXISTS smp CASCADE;
DROP TABLE IF EXISTS request CASCADE;
DROP TABLE IF EXISTS activity_typology CASCADE;
DROP TABLE IF EXISTS material CASCADE;
DROP TABLE IF EXISTS site CASCADE;
DROP TABLE IF EXISTS requirement CASCADE;
DROP FUNCTION IF EXISTS insert_access_log();
DROP TRIGGER IF EXISTS trigger_insert_access_log ON access_log CASCADE;
DROP FUNCTION IF EXISTS check_availability_interval();
DROP TRIGGER IF EXISTS trigger_check_availability_interval ON assignment CASCADE;

CREATE DOMAIN birthdate AS DATE 
CHECK (VALUE >= CURRENT_DATE - INTERVAL '100 years' AND VALUE <= CURRENT_DATE - INTERVAL '18 years');

CREATE DOMAIN user_role AS CHAR(3)
CHECK (value IN ('ADM', 'DBL', 'PLN', 'MNT'));

CREATE DOMAIN expires_date AS TIMESTAMP(0) 
DEFAULT (CURRENT_TIMESTAMP(0) AT TIME ZONE 'CET') + INTERVAL '10 days'
CHECK (VALUE >= (CURRENT_TIMESTAMP(0) AT TIME ZONE 'CET'));

CREATE TABLE system_user(
	id			VARCHAR(20) PRIMARY KEY,
	password 	VARCHAR(50) NOT NULL,
	role		user_role NOT NULL
);
GRANT ALL ON TABLE system_user TO se_user;

CREATE TABLE authentication(
	id			VARCHAR(20) REFERENCES system_user(id) ON DELETE CASCADE ON UPDATE CASCADE PRIMARY KEY,
	token	 	VARCHAR(50) UNIQUE NOT NULL,
	expires		expires_date
);
GRANT ALL ON TABLE authentication TO se_user;

CREATE TABLE user_info(
	user_id 		VARCHAR(20) NOT NULL REFERENCES system_user(id) ON DELETE CASCADE ON UPDATE CASCADE PRIMARY KEY,
	name 			VARCHAR(20) NOT NULL,
	surname 		VARCHAR(30) NOT NULL,
	email 			VARCHAR(50) NOT NULL,
	phone_number 	VARCHAR(15) NOT NULL,
	birthdate 		birthdate NOT NULL
);
GRANT ALL ON TABLE user_info TO se_user;

CREATE TABLE maintainer(
	user_id	VARCHAR(20) REFERENCES system_user(id) ON DELETE CASCADE ON UPDATE CASCADE PRIMARY KEY
);
GRANT ALL ON TABLE maintainer TO se_user;


CREATE TABLE competence(
	competence_id 	VARCHAR(20) PRIMARY KEY,
	name 			VARCHAR(50) UNIQUE NOT NULL
);
GRANT ALL ON TABLE competence TO se_user;

CREATE TABLE mastery(
	maintainer		VARCHAR(20) REFERENCES maintainer(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
	competence		VARCHAR(20) REFERENCES competence(competence_id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT pk_mastery PRIMARY KEY (maintainer, competence)
);
GRANT ALL ON TABLE mastery TO se_user;

CREATE TABLE access_log(
	user_id			VARCHAR(20) REFERENCES system_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
	access_time		TIMESTAMP(0) DEFAULT (CURRENT_TIMESTAMP(0) AT TIME ZONE 'CET'),
	CONSTRAINT pk_access_log PRIMARY KEY (user_id, access_time)
);
GRANT ALL ON TABLE access_log TO se_user;

CREATE TABLE procedure(
	procedure_id 	VARCHAR(20) PRIMARY KEY,
	description 	TEXT
);
GRANT ALL ON TABLE procedure TO se_user;

CREATE TABLE smp(
	procedure VARCHAR(20) REFERENCES procedure(procedure_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	path VARCHAR(256) NOT NULL
);
GRANT ALL ON TABLE smp TO se_user;

CREATE TABLE request(
	procedure 		VARCHAR(20) REFERENCES procedure(procedure_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	competence 		VARCHAR(20) REFERENCES competence(competence_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	CONSTRAINT pk_request PRIMARY KEY (procedure, competence)
);
GRANT ALL ON TABLE request TO se_user;

CREATE TABLE activity_typology(
	typology_id		VARCHAR(20) PRIMARY KEY,
	description		TEXT
);
GRANT ALL ON TABLE activity_typology TO se_user;

CREATE TABLE material(
	material_id		VARCHAR(20) PRIMARY KEY,
	name 			VARCHAR(50) UNIQUE NOT NULL
);
GRANT ALL ON TABLE material TO se_user;

CREATE TABLE site(
	site_id		VARCHAR(20) PRIMARY KEY,
	area 		VARCHAR(50) NOT NULL,
	department 	VARCHAR(50) NOT NULL
);
GRANT ALL ON TABLE site TO se_user;

CREATE TABLE activity(
	activity_id 		VARCHAR(20) PRIMARY KEY,
	description		 	TEXT,
	scheduled_week		SMALLINT,
	estimated_time		INTEGER NOT NULL,
	site				VARCHAR(20) REFERENCES site(site_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	typology			VARCHAR(20) REFERENCES activity_typology(typology_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	procedure			VARCHAR(20) REFERENCES procedure(procedure_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	interruptible		BOOLEAN NOT NULL
	CONSTRAINT check_scheduled_week CHECK (scheduled_week>=1 AND scheduled_week<=52),
	CONSTRAINT check_estimated_time CHECK (estimated_time>0)
);
GRANT ALL ON TABLE activity TO se_user;

CREATE TABLE assignment(
	activity 		VARCHAR(20) REFERENCES activity(activity_id) ON DELETE CASCADE ON UPDATE CASCADE PRIMARY KEY,
	maintainer 		VARCHAR(20) REFERENCES maintainer(user_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	day				DATE NOT NULL,
	start_time		TIME NOT NULL,
	end_time		TIME NOT NULL,
	CONSTRAINT check_interval CHECK (end_time > start_time),
	CONSTRAINT unique_start UNIQUE(maintainer, day, start_time),
	CONSTRAINT unique_end UNIQUE(maintainer, day, end_time),
	CONSTRAINT unique_interval UNIQUE(maintainer, day, start_time, end_time)
);
GRANT ALL ON TABLE assignment TO se_user;

CREATE TABLE requirement(
	activity 	VARCHAR(20) REFERENCES activity(activity_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	material 	VARCHAR(20) REFERENCES material(material_id) ON DELETE CASCADE ON UPDATE CASCADE NOT NULL,
	CONSTRAINT pk_requirement PRIMARY KEY (activity, material)
);
GRANT ALL ON TABLE requirement TO se_user;

CREATE OR REPLACE FUNCTION insert_access_log() RETURNS TRIGGER AS $BODY$
BEGIN
	INSERT INTO access_log VALUES(new.id);
	RETURN new;
END
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_insert_access_log
BEFORE INSERT ON authentication
FOR EACH ROW EXECUTE PROCEDURE insert_access_log();

CREATE OR REPLACE FUNCTION check_availability_interval() RETURNS TRIGGER AS $BODY$
BEGIN
	IF (1 <= (
			SELECT count(*)
			FROM assignment
			WHERE maintainer=NEW.maintainer AND day=NEW.day AND (
				(NEW.start_time>start_time AND NEW.start_time<end_time) OR 
				(NEW.end_time<end_time AND NEW.end_time>start_time) OR
				(NEW.start_time<=start_time AND NEW.end_time>=end_time)	
				)
			)
		) 
	THEN
		RETURN NULL;
	END IF;
	RETURN NEW;
END;
$BODY$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_check_availability_interval
BEFORE INSERT ON assignment
FOR EACH ROW EXECUTE PROCEDURE check_availability_interval();

INSERT INTO competence VALUES('competence1','A competence');
INSERT INTO competence VALUES('PS','Problem solving');

INSERT INTO system_user VALUES('MANT_TEST','password', 'MNT');
INSERT INTO user_info VALUES('MANT_TEST', 'Unit', 'Test', 'unit.test@gmail.com', '3333333333', '1980-01-01');
INSERT INTO maintainer VALUES ('MANT_TEST');

INSERT INTO system_user VALUES('ADM_TEST','password', 'ADM');
INSERT INTO user_info VALUES('ADM_TEST', 'Unit', 'Test', 'unit.test@gmail.com', '3333333333', '1980-01-01');

INSERT INTO site VALUES('site1', 'area1', 'department1');
INSERT INTO activity_typology VALUES('typology1', 'description');
INSERT INTO procedure VALUES('procedure1','description');
INSERT INTO activity VALUES('activity1','description',32, 45, 'site1','typology1','procedure1', TRUE);
INSERT INTO activity VALUES('activity2','description',32, 45, 'site1','typology1','procedure1', TRUE);
INSERT INTO activity VALUES('activity3','description',32, 45, 'site1','typology1','procedure1', TRUE);
INSERT INTO activity VALUES('activity4','description',32, 45, 'site1','typology1','procedure1', TRUE);

INSERT INTO material VALUES ('material1', 'wood');
INSERT INTO material VALUES ('material2', 'metal');

INSERT INTO assignment VALUES('activity1','MANT_TEST','2021-01-01','10:00','10:30');
INSERT INTO assignment VALUES('activity2','MANT_TEST','2021-01-01','14:20','14:50');
INSERT INTO assignment VALUES('activity3','MANT_TEST','2021-01-02','14:15','14:30');
INSERT INTO assignment VALUES('activity4','MANT_TEST','2021-01-05','11:00','14:30');