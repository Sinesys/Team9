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

INSERT INTO system_user VALUES('admin1','admin', 'ADM');
INSERT INTO user_info VALUES('admin1','Pasquale','Policastro', 'p.policastro6@studenti.unisa.it', '3992932548', '1998-04-05');
INSERT INTO system_user VALUES('admin2','admin', 'ADM');
INSERT INTO user_info VALUES('admin2', 'Carmine', 'Oro', 'c.oro@studenti.unisa.it', '3898323768', '1998-05-21');
INSERT INTO system_user VALUES('admin3','admin', 'ADM');
INSERT INTO user_info VALUES('admin3', 'Alice', 'Marzolo', 'a.marzolo@studenti.unisa.it', '3339025602', '1999-02-19');
INSERT INTO system_user VALUES('admin4','admin', 'ADM');
INSERT INTO user_info VALUES('admin4', 'Lorenzo', 'Pagliara', 'l.pagliara5@studenti.unisa.it', '3335422102', '1999-01-06');

INSERT INTO system_user VALUES('planner1','admin', 'PLN');
INSERT INTO user_info VALUES('planner1', 'Leonardo', 'Rossi', 'l.rossi@gmail.com', '3339021102', '1990-12-19');
INSERT INTO system_user VALUES('planner2','admin', 'PLN');
INSERT INTO user_info VALUES('planner2','Sofia','Ferrari', 's.ferrari@gmail.com', '3332589634', '1988-10-15');
INSERT INTO system_user VALUES('planner3','admin', 'PLN');
INSERT INTO user_info VALUES('planner3','Francesco','Esposito', 'f.esposito@gmail.com', '3391679512', '1992-01-025');
INSERT INTO system_user VALUES('planner4','admin', 'PLN');
INSERT INTO user_info VALUES('planner4','Giulia','Bianchi', 'g.bianchi@gmail.com', '3334568794', '1986-03-17');
INSERT INTO system_user VALUES('planner5','admin', 'PLN');
INSERT INTO user_info VALUES('planner5','Alessandro','Romano', 'a.romano@gmail.com', '3397321864', '1994-06-12');
INSERT INTO system_user VALUES('planner6','admin', 'PLN');
INSERT INTO user_info VALUES('planner6','Aurora','Ricci', 'a.ricci@gmail.com', '3332684264', '1990-04-08');
INSERT INTO system_user VALUES('planner7','admin', 'PLN');
INSERT INTO user_info VALUES('planner7','Mattia','De Luca', 'm.deluca@gmail.com', '3391597536', '1996-05-31');
INSERT INTO system_user VALUES('planner8','admin', 'PLN');
INSERT INTO user_info VALUES('planner8','Ginevra','Giordano', 'g.giordano@gmail.com', '3332244556', '1998-07-01');
INSERT INTO system_user VALUES('planner9','admin', 'PLN');
INSERT INTO user_info VALUES('planner9','Giovanni','Di Lorenzo', 'g.dilorenzo@gmail.com', '3398765234', '1993-08-04');
INSERT INTO system_user VALUES('planner10','admin', 'PLN');
INSERT INTO user_info VALUES('planner10','Emma','Lombardi', 'e.lombardi@gmail.com', '3337779512', '1999-08-13');

INSERT INTO competence VALUES('KNTM','knowledge of the machinery');
INSERT INTO competence VALUES('ARTD','ability to read technical diagrams');
INSERT INTO competence VALUES('MA','manual ability');
INSERT INTO competence VALUES('AUS','ability to use software');
INSERT INTO competence VALUES('AS','analytical skills');
INSERT INTO competence VALUES('PS','problem solving');
INSERT INTO competence VALUES('CS','communication skills');
INSERT INTO competence VALUES('ATWI','ability to work independently');
INSERT INTO competence VALUES('ATW','ability to work as part of a team');
INSERT INTO competence VALUES('SOI','speed of intervention');
INSERT INTO competence VALUES('FLX','flexibility');

INSERT INTO system_user VALUES('maintainer1','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer1', 'Edoardo', 'Costantini', 'e.costantini@gmail.com', '3334567798', '1980-01-05');
INSERT INTO maintainer VALUES ('maintainer1');
INSERT INTO system_user VALUES('maintainer2','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer2', 'Anna', 'Battaglia', 'a.battaglia@gmail.com', '3331515300', '1978-02-19');
INSERT INTO maintainer VALUES ('maintainer2');
INSERT INTO system_user VALUES('maintainer3','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer3', 'Tommaso', 'Basile', 't.basile@gmail.com', '3339024198', '1979-03-10');
INSERT INTO maintainer VALUES ('maintainer3');
INSERT INTO system_user VALUES('maintainer4','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer4', 'Beatrice', 'Martino', 'b.martino@gmail.com', '3396542325', '1980-03-13');
INSERT INTO maintainer VALUES ('maintainer4');
INSERT INTO system_user VALUES('maintainer5','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer5', 'Riccardo', 'Benedetti', 'r.benedetti@gmail.com', '3339547820', '1981-04-29');
INSERT INTO maintainer VALUES ('maintainer5');
INSERT INTO system_user VALUES('maintainer6','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer6', 'Greta', 'Silvestri', 'g.silvestri@gmail.com', '3394512378', '1982-05-28');
INSERT INTO maintainer VALUES ('maintainer6');
INSERT INTO system_user VALUES('maintainer7','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer7', 'Gabriele', 'Amato', 'g.amato@gmail.com', '333915476', '1983-08-07');
INSERT INTO maintainer VALUES ('maintainer7');
INSERT INTO system_user VALUES('maintainer8','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer8', 'Giorgia', 'Cattaneo', 'g.cattaneo@gmail.com', '3396215422', '1992-09-19');
INSERT INTO maintainer VALUES ('maintainer8');
INSERT INTO system_user VALUES('maintainer9','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer9', 'Andrea', 'Pellegrini', 'a.pellegrini@gmail.com', '3336599862', '1993-11-09');
INSERT INTO maintainer VALUES ('maintainer9');
INSERT INTO system_user VALUES('maintainer10','admin', 'MNT');
INSERT INTO user_info VALUES('maintainer10', 'Paola', 'Leone', 'p.leone@gmail.com', '3399957060', '1990-12-25');
INSERT INTO maintainer VALUES ('maintainer10');

INSERT INTO system_user VALUES('dbl2','admin', 'DBL');
INSERT INTO user_info VALUES('dbl2', 'Edoardo', 'Costantini', 'e.costantini@gmail.com', '3334567798', '1980-01-05');

INSERT INTO mastery VALUES('maintainer1', 'KNTM');
INSERT INTO mastery VALUES('maintainer1', 'ARTD');
INSERT INTO mastery VALUES('maintainer1', 'PS');

INSERT INTO site VALUES('site1', 'area1', 'department1');
INSERT INTO activity_typology VALUES('typology1', 'description');
INSERT INTO procedure VALUES('procedure1','description');
INSERT INTO activity VALUES('activity1','description',32, 45, 'site1','typology1','procedure1', TRUE);
INSERT INTO site VALUES('site2', 'area1', 'department1');
INSERT INTO activity_typology VALUES('typology2', 'description');
INSERT INTO procedure VALUES('procedure2','description');
INSERT INTO activity VALUES('activity2','description',32, 60, 'site1','typology1','procedure1', TRUE);
INSERT INTO site VALUES('site3', 'area3', 'department4');
INSERT INTO activity_typology VALUES('typology3', 'description');
INSERT INTO procedure VALUES('procedure3','description');
INSERT INTO activity VALUES('activity3','description',32, 45, 'site3','typology3','procedure3', TRUE);
INSERT INTO site VALUES('site4', 'area1', 'department1');
INSERT INTO activity_typology VALUES('typology4', 'description');
INSERT INTO procedure VALUES('procedure4','description');
INSERT INTO activity VALUES('activity4','description',32, 60, 'site4','typology4','procedure4', TRUE);
INSERT INTO site VALUES('site5', 'area1', 'department1');
INSERT INTO activity_typology VALUES('typology5', 'description');
INSERT INTO procedure VALUES('procedure5','description');
INSERT INTO activity VALUES('activity5','description',32, 60, 'site5','typology5','procedure5', TRUE);
INSERT INTO site VALUES('site6', 'area1', 'department1');
INSERT INTO activity_typology VALUES('typology6', 'description');
INSERT INTO procedure VALUES('procedure6','description');
INSERT INTO activity VALUES('activity6','description',32, 60, 'site6','typology6','procedure6', TRUE);
INSERT INTO site VALUES('site7', 'area1', 'department1');
INSERT INTO activity_typology VALUES('typology7', 'description');
INSERT INTO procedure VALUES('procedure7','description');
INSERT INTO activity VALUES('activity7','description',32, 60, 'site7','typology7','procedure7', TRUE);

INSERT INTO material VALUES ('material1', 'wood');
INSERT INTO material VALUES ('material2', 'metal');
INSERT INTO material VALUES ('material3', 'iron');
INSERT INTO material VALUES ('material4', 'gold');
INSERT INTO material VALUES ('material5', 'silver');