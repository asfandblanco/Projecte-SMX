/* -----------------------------------------------------------------------------
  - Project: RFID attendance system using ESP32 (OLED removed)
  - Author:  Original by ElectronicsTechHaIs
  - Date:  6/03/2020
 -----------------------------------------------------------------------------*/
//*******************************Libreries********************************
//ESP32----------------------------
#include <WiFi.h>
#include <HTTPClient.h>
#include <time.h>
//RFID-----------------------------
#include <SPI.h>
#include <MFRC522.h>
//************************************************************************
// Pines del RC522 conectados al ESP32
#define SS_PIN  21   // SDA → CS/SS
#define RST_PIN 22   // RST → Reset

MFRC522 mfrc522(SS_PIN, RST_PIN); // Create MFRC522 instance.
//************************************************************************
/* Set these to your desired credentials. */
const char *ssid = "DIGIFIBRA-qbX4";
const char *password = "4cxCUcgxqW7d";
const char* device_token  = "05396ba7a2e574e3";
//************************************************************************
int timezone = 1 * 3600;   //Replace "x" with your timezone.
int time_dst = 0;
String getData, Link;
String OldCardID = "";
unsigned long previousMillis2 = 0;
String URL = "http://192.168.1.136/rfidattendance/getdata.php"; //computer IP or server domain
//************************************************************************
void setup() {
  delay(1000);
  Serial.begin(115200);
  // Inicializar SPI con los pines estándar
  SPI.begin(18, 19, 23, SS_PIN); // SCK=18, MISO=19, MOSI=23, SS=21
  mfrc522.PCD_Init();            // Init MFRC522 card

  connectToWiFi();                // Connect to Wi-Fi
  configTime(timezone, time_dst, "pool.ntp.org","time.nist.gov");
}
//************************************************************************
void loop() {
  //check if there's a connection to Wi-Fi or not
  if(!WiFi.isConnected()){
    connectToWiFi();    //Retry to connect to Wi-Fi
  }
  //---------------------------------------------
  if (millis() - previousMillis2 >= 15000) {
    previousMillis2 = millis();
    OldCardID="";
  }
  delay(50);
  //---------------------------------------------
  //look for new card
  if ( ! mfrc522.PICC_IsNewCardPresent()) {
    return; //go to start of loop if there is no card present
  }
  // Select one of the cards
  if ( ! mfrc522.PICC_ReadCardSerial()) {
    return; //if read card serial(0) returns 1, the uid struct contains the ID of the read card.
  }
  String CardID ="";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    CardID += mfrc522.uid.uidByte[i];
  }
  //---------------------------------------------
  if( CardID == OldCardID ){
    return;
  }
  else{
    OldCardID = CardID;
  }
  //---------------------------------------------
  SendCardID(CardID);
  delay(1000);
}
//************send the Card UID to the website*************
void SendCardID( String Card_uid ){
  Serial.println("Sending the Card ID");
  if(WiFi.isConnected()){
    HTTPClient http;    //Declare object of class HTTPClient
    //GET Data
    getData = "?card_uid=" + String(Card_uid) + "&device_token=" + String(device_token);
    //GET method
    Link = URL + getData;
    http.begin(Link); //initiate HTTP request
    
    int httpCode = http.GET();   //Send the request
    String payload = http.getString();    //Get the response payload

    Serial.println(httpCode);   //Print HTTP return code
    Serial.println(Card_uid);   //Print Card ID
    Serial.println(payload);    //Print request response payload

    http.end();  //Close connection
  }
}
//********************connect to the WiFi******************
void connectToWiFi(){
    WiFi.mode(WIFI_OFF);        //Prevents reconnection issue
    delay(1000);
    WiFi.mode(WIFI_STA);
    Serial.print("Connecting to ");
    Serial.println(ssid);
    
    WiFi.begin(ssid, password);
    
    while (WiFi.status() != WL_CONNECTED) {
      delay(500);
      Serial.print(".");
    }
    Serial.println("");
    Serial.println("Connected");
    Serial.print("IP address: ");
    Serial.println(WiFi.localIP());  //IP address assigned to your ESP
    delay(1000);
}