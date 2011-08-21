CREATE  TABLE %prfx%report_displays (
  page varchar(32) NOT NULL,
  position integer NOT NULL,
  report text NOT NULL,
  title text NOT NULL,
  max_rows integer NOT NULL,
  params text NOT NULL,
  PRIMARY KEY (page, position)
);
