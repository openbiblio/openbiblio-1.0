CREATE TABLE calendar (
  calendar integer NOT NULL,
  date date NOT NULL,
  open enum('Yes', 'No', 'Unset') NOT NULL,
  PRIMARY KEY (calendar, date)
);
