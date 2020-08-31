/*
  Rui Santos
  Complete project details at https://RandomNerdTutorials.com
  
  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.
  
  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  Various projects by Rui Santos (above) have been modified and combined 
  by Monty Rhodes for the design of a platform to integrate IoT and low cost SSHP
  
*/
// Libraries used
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <PubSubClient.h>

// Mobile router's credentials
const char* ssid  =       "SSHPP WiFi";
const char* password =    "12345678";

// Hub's local IP address which has the Mosquitto MQTT Broker installed
const char* mqtt_server = "192.168.0.122";

// HTTP POST request will be made to this core address with the addition of the API Key and value
const char* serverName =  "http://smartrhodes.tech/SSHP/WaterLevelPost.php";

// API key value that matches with the WaterLevelPost.php file on the back end of the web hosting service
String apiKeyValue =      "Zn2kepJtetOWiFuu";

// Initialize DHT sensor, WiFiClient, PubSubClient, and HTTPClient
WiFiClient espClient;
PubSubClient client(espClient);
HTTPClient http;

// Adapted from Rui Santos' work
// The reconnect() function reconnects the acquisition board with the MQTT broker on the Hub
void reconnect() {
  while (!client.connected()) {
    Serial.print("Connecting to MQTT broker...");
    if (client.connect("ESP8266")) {Serial.println(" Connected!");} 
      else {
      Serial.println("FAILED: Attempting reconnect in 5 seconds");
      delay(5000);
    }}}

// wifi_connect() function adapted from: https://github.com/esp8266/Arduino/blob/master/doc/esp8266wifi/readme.rst
// The wifi_connect() function connects the acquisition to the mobile router, and stays in a loop until it connects
// After a successful connection it prints the wireless signal strength
void wifi_connect() {
  WiFi.begin(ssid, password);
  Serial.println("Connecting to Wi-Fi");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");}
  Serial.println("");
  Serial.print("Connected to WiFi successfully with signal strength: ");
  Serial.print(WiFi.RSSI());
  Serial.println(" dBm");}

// The wifi_connect() function is executed and the MQTT server details are specified
void setup() {
  Serial.begin(115200);
  wifi_connect();
  client.setServer(mqtt_server, 1883);}

void loop() {
  // Only if the acquisition board is connected to the mobile router, run the following code
  if(WiFi.status() == WL_CONNECTED){
    
    // Only if the acquisition board is connected to the MQTT broker on the Hub, run the following code
    if (!client.connected()) {
    reconnect();}
    if(!client.loop())
    client.connect("ESP8266");

    // The reading is taken from the Analogue to Digital Converter pin
    float reading = analogRead(A0);
    
    // Check if the reading from the sensor was successful, if not exit the loop
    if (isnan(reading)) {
    Serial.println("Check Water Level Sensor is connected");
    return;}

    // Adapted from Rui Santos' work.
    // Compile the HTTP request with the header, API Key, and water level value 
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String httpRequestData = "api_key=" + apiKeyValue + "&value=" + String(reading) + "";
    
    // Make the HTTP POST request with the compiled HTTP request, and after free resources
    http.POST(httpRequestData);
    http.end();

    // Convert the water level value to a string for it to be published via MQTT
    static char level[7];
    dtostrf(reading, 6, 2, level);

    // Publish MQTT water level reading on the topic: SSHP/WaterLevel
    client.publish("SSHP/WaterLevel", level);

    // Custom delay of 10 seconds
    delay(10000);
    }
    
  else {
    Serial.println("WiFi Disconnected");
    wifi_connect();
  }
}
