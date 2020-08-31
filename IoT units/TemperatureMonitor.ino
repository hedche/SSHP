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
#include "DHT.h"
#include <PubSubClient.h>

// Mobile router's credentials
const char* ssid  =       "SSHPP WiFi";
const char* password =    "12345678";

// Hub's local IP address which has the Mosquitto MQTT Broker installed
const char* mqtt_server = "192.168.0.122";

// HTTP POST request will be made to this core address with the addition of the API Key and value
const char* serverName =  "http://smartrhodes.tech/SSHP/TemperaturePost.php";

// API key value that matches with the TemperaturePost.php file on the back end of the web hosting service
String apiKeyValue =      "SeHVWDv3sukYgmQh";

// Initialize DHT sensor, WiFiClient, PubSubClient, and HTTPClient
DHT dht(2, DHT11);
WiFiClient espClient;
PubSubClient client(espClient);
HTTPClient http;

// Adapted from Rui Santos' work
// The reconnect() function reconnects the acquisition board with the MQTT broker on the Hub
void reconnect() {
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    if (client.connect("ESP8266TandH")) {
      Serial.println("connected");} 
      else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      // Wait 5 seconds before retrying
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

// The setup() function starts the DHT sensor, the Wi-Fi connection and sets the MQTT server
void setup() {
  Serial.begin(115200);
  dht.begin();
  wifi_connect();
  client.setServer(mqtt_server, 1883);
}

void loop() {
  // Only if the acquisition board is connected to the mobile router, run the following code
  if(WiFi.status()== WL_CONNECTED){
    
    // Only if the acquisition board is connected to the MQTT broker on the Hub, run the following code
    if (!client.connected()){
    reconnect();}
    if(!client.loop()){
    client.connect("ESP8266TandH");}
    
    // Get the temperature reading from the DHT11 sensor and assign to float "t"
    float t = dht.readTemperature();
    delay(1000);
    // After waiting for 1 second to allow for the DHT11 sensor to respond with a reading, it then checks if the reading was valid
    if (isnan(t)) {
    Serial.println("Check DHT Sensor");
    return;}

    // Adapted from Rui Santos' work.
    // Compile the HTTP POST request with the header, API Key, and mean power value 
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String httpRequestData = "api_key=" + apiKeyValue + "&value=" + String(t) + "";
    
    // Make the HTTP POST request with the compiled HTTP request, and after free resources
    http.POST(httpRequestData);
    http.end();

    // Converts the "t" float to a string ("temperature") for MQTT publishing
    static char temperature[7];
    dtostrf(t, 6, 2, temperature);

    // Publish MQTT water level reading on the topic: SSHP/Temperature
    client.publish("SSHP/Temperature", temperature);

    // Custom delay of 4 seconds with the 1 second from earlier totalling the delays to 5 seconds
    delay(4000);
    }
    
  else {
    Serial.println("WiFi Disconnected");
    wifi_connect();
  }  
}
