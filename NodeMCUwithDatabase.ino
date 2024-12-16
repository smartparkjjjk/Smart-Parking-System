#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <time.h>
#include <WiFiClient.h>


const int signalPin1 = D0;  // Signal input pin 1
const int signalPin2 = D1;  // Signal input pin 2
const int signalPin3 = D2;  // Signal input pin 3
const int signalPin4 = D3;  // Signal input pin 4
const char* ssid = "EpicSeven_JP";
const char* password = "Glitched_1234";
const char* serverURL1 = "http://smartparkjjjk.onlinewebshop.net/store_signal.php"; // Replace with your server IP or domain
const char* serverURL2 = "http://smartparkjjjk.onlinewebshop.net/store_signal_admin.php";
String PROJECT_API_KEY = "niggaaa";

WiFiClient client; // Create WiFiClient object
int prevSignalState1 = LOW, prevSignalState2 = LOW, prevSignalState3 = LOW, prevSignalState4 = LOW;
String startTime1 = "", startTime2 = "", startTime3 = "", startTime4 = "";

void setup() {
  Serial.begin(9600);
  pinMode(signalPin1, INPUT);
  pinMode(signalPin2, INPUT);
  pinMode(signalPin3, INPUT);
  pinMode(signalPin4, INPUT);

  // Connect to Wi-Fi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("Connected to WiFi");
  configTime(28800, 0, "pool.ntp.org", "time.nist.gov"); // UTC+8 offset in seconds
}

String getCurrentTime() {
  time_t now = time(nullptr);
  struct tm* timeInfo = localtime(&now);
  char timeString[30];
  strftime(timeString, sizeof(timeString), "%Y-%m-%d %H:%M:%S", timeInfo);
  return String(timeString);
}

void loop() {
  int signalState1 = digitalRead(signalPin1);  // Read state of signalPin1
  int signalState2 = digitalRead(signalPin2);  // Read state of signalPin2
  int signalState3 = digitalRead(signalPin3);  // Read state of signalPin3
  int signalState4 = digitalRead(signalPin4);  // Read state of signalPin3

  checkStateChange(signalState1, prevSignalState1, startTime1, "SLOT 1");
  checkStateChange(signalState2, prevSignalState2, startTime2, "SLOT 2");
  checkStateChange(signalState3, prevSignalState3, startTime3, "SLOT 3");
  checkStateChange(signalState4, prevSignalState4, startTime4, "SLOT 4");

  // Print signal states to serial monitor
  Serial.print("Signal1 State: ");
  Serial.println(signalState1);
  Serial.print("Signal2 State: ");
  Serial.println(signalState2);
  Serial.print("Signal3 State: ");
  Serial.println(signalState3);
  Serial.print("Signal4 State: ");
  Serial.println(signalState4);

  // Send both states to the server
  sendDataToServer(signalState1, signalState2, signalState3, signalState4);

  delay(400); // Send data every 400ms
}

void checkStateChange(int currentState, int& prevState, String& startTime, const String& signalName) {
  if (currentState != prevState) {
    String currentTime = getCurrentTime();
    if (currentState == HIGH) {
      startTime = currentTime; // Record start time
      Serial.println(signalName + " turned HIGH at " + startTime);
    } else {
      // Record the end time and send both start and end times to the server
      Serial.println(signalName + " turned LOW at " + currentTime);
      sendSignalStateToDatabase(signalName, startTime, currentTime);
      startTime = ""; // Clear start time
    }
    prevState = currentState;
  }
}

void sendSignalStateToDatabase(const String& signalName, const String& startTime, const String& endTime) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(client, serverURL2); // Pass WiFiClient client and server URL
    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Set content type to form data

    // Prepare the data to send
    String output = "signal=" + signalName + "&startTime=" + startTime + "&endTime=" + endTime;

    int httpResponseCode = http.POST(output); // Send POST request
    if (httpResponseCode > 0) {
      Serial.println("Data sent successfully: " + output);
    } else {
      Serial.println("Error sending data");
    }
    http.end(); // Free resources
  } else {
    Serial.println("WiFi not connected");
  }
}

void sendDataToServer(int state1, int state2, int state3, int state4) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Use the newer begin method with WiFiClient instance
    http.begin(client, serverURL1); // Pass WiFiClient client and server URL

    http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Set content type to form data

    // Prepare the data to send
    String output = "state1=" + String(state1) + "&state2=" + String(state2) + "&state3=" + String(state3) + "&state4=" + String(state4); // Send both states

    int httpResponseCode = http.POST(output); // Send POST request
    if (httpResponseCode > 0) {
      Serial.println("Data sent successfully");
    } else {
      Serial.println("Error sending data");
    }
    http.end(); // Free resources
  } else {
    Serial.println("WiFi not connected");
  }
}