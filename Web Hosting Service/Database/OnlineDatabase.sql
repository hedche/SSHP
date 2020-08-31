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
  

/* The following SQL script was written by Rui Santos and the source of the code is on his website at
https://randomnerdtutorials.com/control-esp32-esp8266-gpios-from-anywhere/#more-93152 
The purpose of these tables is for the IoT control unit to check the state of the relay or alter it 
check the URL link for more information*/

CREATE TABLE Outputs (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64),
    board INT(6),
    gpio INT(6),
    state INT(6)
);
INSERT INTO `Outputs`(`name`, `board`, `gpio`, `state`) VALUES ("Relay", 1, 2, 0);

CREATE TABLE Boards (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    board INT(6),
    last_request TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO `Boards`(`board`) VALUES (1);