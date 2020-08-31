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
#include <ArduinoJson.h>

// Mobile router's credentials
const char* ssid  =       "SSHPP WiFi";
const char* password =    "12345678";

// Hub's local IP address which has the Mosquitto MQTT Broker installed
const char* mqtt_server = "192.168.0.122";

// The HTTP GET request that checks the online database for state of the relay
const char* serverName =  "http://smartrhodes.tech/SSHP/esp-outputs-action.php?action=outputs_state&board=1";

// Initialize WiFiClient, PubSubClient, and HTTPClient
WiFiClient espClient;
PubSubClient client(espClient);
HTTPClient http;

// The pin that the relay "In" is connected to
int relayPin = D2;

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

// Adapted from Rui Santos' work
// The callback() function is used so that when a message is published on the "SSHP/Relay" topic
// The message then determines the state of the relay
void callback(String topic, byte* message, unsigned int length) {
  Serial.print("Message arrived on topic: ");
  Serial.println(topic);
  Serial.print("Message: ");
  
  // A loop to analyse the message and put the full word into a string called "messageTemp"
  String messageTemp;
  for (int i = 0; i < length; i++) {
    Serial.print((char)message[i]);
    messageTemp += (char)message[i];
  }

  // Only if the topic is "SSHP/Relay" then analyse the message
  if(topic == "SSHP/Relay"){
      if(messageTemp == "on"){
        // Change the relay pin state to LOW which translates to 'on'
        digitalWrite(relayPin, LOW);
        // Then make an HTTP GET request to syncronise this change with the online database
        http.begin("http://smartrhodes.tech/SSHP/esp-outputs-action.php?action=output_update&id=1&state=1");
        http.GET();
      }
      else if(messageTemp == "off"){
        // Change the relay pin state to HIGH which translates to 'off'
        digitalWrite(relayPin, HIGH);
        // Then make an HTTP GET request to syncronise this change with the online database
        http.begin("http://smartrhodes.tech/SSHP/esp-outputs-action.php?action=output_update&id=1&state=0");
        http.GET();
      }
  }
  Serial.println();
}

// Adapted from Rui Santos' work
// The reconnect() function reconnects the acquisition board with the MQTT broker on the Hub
// It also subscribes the board to the "SSHP/Relay" topic for the callback function
void reconnect() {
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    if (client.connect("ESP8266Relay")) {
      Serial.println("connected");
      client.subscribe("SSHP/Relay");} 
      else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      // Wait 5 seconds before retrying
      delay(5000);
    }}}

// The setup() function states the relay pin number and turns it on
// Then starts the Wi-Fi connection and sets the MQTT server and callback() function
void setup() {
  pinMode(relayPin, OUTPUT);
  digitalWrite(relayPin, LOW);
  Serial.begin(115200);
  wifi_connect();
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);
}

void loop() {
  // Only if the acquisition board is connected to the mobile router, run the following code
  if(WiFi.status()== WL_CONNECTED){
    
    // Only if the acquisition board is connected to the MQTT broker on the Hub, run the following code
    if (!client.connected()) {
      reconnect();}
    if(!client.loop())
      client.connect("ESP8266Relay");

      // Begin the HTTP GET request and make it
      http.begin(serverName);
      http.GET();

      // Prepare a JSON object to analyse the received string from the HTTP GET request
      const size_t capacity = JSON_OBJECT_SIZE(1) + 10;
      DynamicJsonDocument doc(capacity);

      // Parse the received string as a JSON object
      deserializeJson(doc, (http.getString()));
      JsonObject obj = doc.as<JsonObject>();

      // Get the state value (1 or 0) of the relay from the JSON object and assign it to the string "state"
      String state = obj["2"];

      // Print to the serial monitor the state of the relay
      Serial.print("HTTP GET state received: ");
      Serial.println(state);

      // According to the state received from the online database, change the relay to on or off
      if(state == "1"){
        digitalWrite(relayPin, LOW);
        }
      else if(state == "0"){
        digitalWrite(relayPin, HIGH);
        }

      // Custom delay for how frequent the acquisition board should query the online database for the state of the relay
      delay(2000);
      }
      
   else {
    Serial.println("WiFi Disconnected");
    wifi_connect();
  }  
}
