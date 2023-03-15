#include <Wire.h> 
#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN         9          // Configurable, see typical pin layout above
#define SS_PIN          10         // Configurable, see typical pin layout above

MFRC522 mfrc522(SS_PIN, RST_PIN);   // Create MFRC522 instance.
String lastId="";
int lastTime;
void setup() {
  Serial.begin(115200);   // Initiate a serial communication
  SPI.begin();      // Initiate  SPI bus
  mfrc522.PCD_Init();   // Initiate MFRC522
  
}
void loop(){
  if ( ! mfrc522.PICC_IsNewCardPresent()) 
  {
    return;
  }
  // Select one of the cards
  if ( ! mfrc522.PICC_ReadCardSerial()) 
  {
    return;
  }
  String content= "";
  for (byte i = 0; i < mfrc522.uid.size; i++) 
  {
     //Serial.print(mfrc522.uid.uidByte[i] < 0x10 ? " 0" : " ");
     //Serial.print(mfrc522.uid.uidByte[i], HEX);
      content.concat(String(mfrc522.uid.uidByte[i] < 0x10 ? " 0" : " "));
      content.concat(String(mfrc522.uid.uidByte[i], HEX));
     
     
  }
  content.toUpperCase();
  if(lastId==content){
    int leftTime=millis()-lastTime;
    if(leftTime>5000){
      lastId="";
      //Serial.println("");
      // Serial.println("Now you can put that card again ");
    }else{
      //Serial.print(". ");
    }
  }else{
    //Serial.println("");
    Serial.println(content);
    lastId=content;
    lastTime=millis();
  }

} 
