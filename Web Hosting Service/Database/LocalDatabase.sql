/* Create the TemperatureData table for Temperature values to be stored in */
CREATE TABLE TemperatureData (
  ID int(255) NOT NULL,
  Temperature varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  DateTime timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Make the 'ID' column primary and autoincrement with each addition of Temperature to the table*/
ALTER TABLE TemperatureData
  ADD PRIMARY KEY (ID);

ALTER TABLE TemperatureData
  MODIFY ID int(255) NOT NULL AUTO_INCREMENT;
  
  
/* Create the PowerData table for Power values to be stored in */
CREATE TABLE PowerData (
  ID int(255) NOT NULL,
  Power varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  DateTime timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Make the 'ID' column primary and autoincrement with each addition of Power to the table*/
ALTER TABLE PowerData
  ADD PRIMARY KEY (ID);

ALTER TABLE PowerData
  MODIFY ID int(255) NOT NULL AUTO_INCREMENT;
  
  
/* Create the WaterLevelData table for Watel level values to be stored in */
CREATE TABLE WaterLevelData (
  ID int(255) NOT NULL,
  Level varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  DateTime timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Make the 'ID' column primary and autoincrement with each addition of Water level to the table*/
ALTER TABLE WaterLevelData
  ADD PRIMARY KEY (ID);

ALTER TABLE WaterLevelData
  MODIFY ID int(255) NOT NULL AUTO_INCREMENT;