CREATE TABLE IF NOT EXISTS %prfx%.open_hours (
hourid INT(11) NOT NULL AUTO_INCREMENT,
siteid INT(11) NOT NULL,
day INT NOT NULL,
start_time INT NOT NULL,
end_time INT NOT NULL,
by_appointment BOOLEAN DEFAULT FALSE,
effective_start_date DATE,
effective_end_date DATE,
public_note VARCHAR(128),
private_note VARCHAR(128),
PRIMARY KEY (hourid),
CONSTRAINT chk_StartTime CHECK (start_time<2400),
CONSTRAINT chk_EndTime CHECK (end_time<2400),
CONSTRAINT chk_NotTooManyStartMinutes CHECK (MOD(start_time,100)<60),
CONSTRAINT chk_NotTooManyEndMinutes CHECK (MOD(end_time,100)<60),
CONSTRAINT chk_EndTimeAfterStartTime CHECK (start_time<end_time),
CONSTRAINT chk_EndDateAfterStartDate CHECK (effective_start_date<effective_end_date),
CONSTRAINT chk_Day CHECK (day<7 AND day>=0)
)   DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
