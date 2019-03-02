// Includes the Servo library
#include <Servo.h>
#include <Ethernet.h>
#include <ArduinoJson.h>
#include <SPI.h>
#include <pt.h>
static struct pt pt1, pt2; // each protothread n

byte mac[] = { 0x00, 0xAA, 0xBB, 0xCC, 0xDE, 0x02 };
IPAddress ip(192,168,137,22);
char server[] = "192.168.137.1"; 
EthernetClient cliente;

StaticJsonBuffer<1060> jsonBuffer;
JsonObject& root1 = jsonBuffer.createObject(); 
JsonObject& root2 = jsonBuffer.createObject(); 

const byte trigPin1 = 2;
const byte echoPin1 = 3;
const byte trigPin2 = 5;
const byte echoPin2 = 6;

byte posisi1 = 10;
byte posisi2 = 10;
byte x1=4;
byte x2=4;

Servo myServo1, myServo2; // Creates a servo object for controlling the servo motor

void setup() {
  pinMode(trigPin1, OUTPUT); // Sets the trigPin as an Output
  pinMode(echoPin1, INPUT); // Sets the echoPin as an Input
  pinMode(trigPin2, OUTPUT); // Sets the trigPin as an Output
  pinMode(echoPin2, INPUT); // Sets the echoPin as an Input
  Serial.begin(9600);
  myServo1.attach(4); // Defines on which pin is the servo motor attached
  myServo2.attach(7); // Defines on which pin is the servo motor attached
  Ethernet.begin(mac, ip);
  PT_INIT(&pt1);  // initialise the two
  PT_INIT(&pt2);  // protothread variables    
}
void loop() {
  protothread1(&pt1);
  protothread2(&pt2);
}
// Function for calculating the distance measured by the Ultrasonic sensor
int calculateDistance1(){ 
  digitalWrite(trigPin1, LOW); 
  delayMicroseconds(2);
  digitalWrite(trigPin1, HIGH); 
  delayMicroseconds(10);
  digitalWrite(trigPin1, LOW);
  return ((pulseIn(echoPin1, HIGH))*0.034/2);
}
int calculateDistance2(){   
  digitalWrite(trigPin2, LOW); 
  delayMicroseconds(2);
  digitalWrite(trigPin2, HIGH); 
  delayMicroseconds(10);
  digitalWrite(trigPin2, LOW);
  return ((pulseIn(echoPin2, HIGH))*0.034/2);
}

void printIPAddress(){
  Serial.print("My IP address: ");
  for (byte thisByte = 0; thisByte < 4; thisByte++) {
    Serial.print(Ethernet.localIP()[thisByte], DEC);
    Serial.print(".");
  }
  Serial.println();
}
static int protothread1(struct pt *pt) {
  static unsigned long timestamp = 0;
  PT_BEGIN(pt);
  while(1) {
    posisi1 += x1;
    myServo1.write(posisi1);
    timestamp = millis();
    PT_WAIT_UNTIL(pt, millis() - timestamp > 200); 
    root1[String(posisi1)] = calculateDistance1();
    if(posisi1 >= 166){
      posisi1 = 10;
      myServo1.write(posisi1);
      if (cliente.connect(server, 80)) {
        root1.printTo(Serial);
        Serial.println();
        cliente.print("GET /latihan/coba1.php?radar=1&value="); //Connecting and Sending values to database
        root1.printTo(cliente);
        cliente.println();
        cliente.stop();
      }else{
        Serial.println("connection failed");
      }
   }
  }
  PT_END(pt);
}
static int protothread2(struct pt *pt) {
  static unsigned long timestamp = 0;
  PT_BEGIN(pt);
  while(1) {
    timestamp = millis();
    posisi2 += x2;
    myServo2.write(posisi2);
    PT_WAIT_UNTIL(pt, millis() - timestamp > 250);
    root2[String(posisi2)] = calculateDistance2();
    if(posisi2 >= 166){
      posisi2 = 10;
      myServo2.write(posisi2);
      if (cliente.connect(server, 80)) {
        cliente.print("GET /latihan/coba1.php?radar=2&value="); //Connecting and Sending values to database
        root2.printTo(cliente);
        cliente.println();
        cliente.stop();
      }else{
        Serial.println("connection failed");
      }
    }
  }
  PT_END(pt);
}

