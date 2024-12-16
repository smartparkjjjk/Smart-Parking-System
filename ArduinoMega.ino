#include <Servo.h> // Include the Servo library
#include <Wire.h> 
#include <LiquidCrystal_I2C.h>
LiquidCrystal_I2C lcd(0x27,20,4);

const int trigPin1 = 2;
const int trigPin2 = 7;
const int trigPin3 = 12; //
const int trigPin4 = 16; //

const int echoPin1 = 3;
const int echoPin2 = 8;
const int echoPin3 = 13; //
const int echoPin4 = 17; //

const int ledPin1 = 4;             // LED pin
const int ledPin2 = 9;
const int ledPin3 = 14;             // LED pin
const int ledPin4 = 18;

const int buzzerPin1 = 5;          // Buzzer pin
const int buzzerPin2 = 10;
const int buzzerPin3 = 15;          // Buzzer pin
const int buzzerPin4 = 19;

const int mcuPin1 = 31;
const int mcuPin2 = 28;
const int mcuPin3 = 32;
const int mcuPin4 = 30;

unsigned long triggerTime1 = 0;
unsigned long triggerTime2 = 0;
unsigned long triggerTime3 = 0;
unsigned long triggerTime4 = 0;

const unsigned long delayTime1 = 5000;  // Delay to turn on LED
const unsigned long delayTime2 = 5000;
const unsigned long delayTime3 = 5000;  // Delay to turn on LED
const unsigned long delayTime4 = 5000;

const unsigned long delayTimeA1 = 10000;  // Delay to turn on buzzer
const unsigned long delayTimeA2 = 10000;
const unsigned long delayTimeA3 = 10000;  // Delay to turn on buzzer
const unsigned long delayTimeA4 = 10000;

const int safe = 6
;                    

int IRsensorValue1 = 0;
const int IRsensorEntrance = 33;
Servo myServo1;           // Declare the servo object
const int servoPin1 = 34; // Servo control pin

int IRsensorValue2 = 0;
const int IRsensorExit =35;
Servo myServo2;           // Declare the servo object
const int servoPin2 = 36; // Servo control pin

int distanceState1 = LOW;
int distanceState2 = LOW;
int distanceState3 = LOW;
int distanceState4 = LOW;

int S1=0, S2=0, S3=0, S4=0;

int slot = 4;

void setup() {
  Serial.begin(9600);

  pinMode(trigPin1, OUTPUT);
  pinMode(echoPin1, INPUT);
  pinMode(trigPin2, OUTPUT);
  pinMode(echoPin2, INPUT);
  pinMode(trigPin3, OUTPUT);
  pinMode(echoPin3, INPUT);
  pinMode(trigPin4, OUTPUT);
  pinMode(echoPin4, INPUT);


  pinMode(ledPin1, OUTPUT);
  pinMode(ledPin2, OUTPUT);
  pinMode(ledPin3, OUTPUT);
  pinMode(ledPin4, OUTPUT);

  pinMode(buzzerPin1, OUTPUT);
  pinMode(buzzerPin2, OUTPUT);
  pinMode(buzzerPin3, OUTPUT);
  pinMode(buzzerPin4, OUTPUT);

  pinMode(mcuPin1, OUTPUT);
  pinMode(mcuPin2, OUTPUT);
  pinMode(mcuPin3, OUTPUT);
  pinMode(mcuPin4, OUTPUT);
  
  pinMode(IRsensorEntrance, INPUT);  // IR sensor input pin
  pinMode(IRsensorExit, INPUT);
  
  myServo1.attach(servoPin1); // Attach the servo to the specified pin
  myServo1.write(90);         // Start the servo at 0 degrees
  
 
  myServo2.attach(servoPin2); // Attach the servo to the specified pin
  myServo2.write(90);         // Start the servo at 0 degrees

  digitalWrite(mcuPin1, LOW);
  digitalWrite(mcuPin2, LOW);
  digitalWrite(mcuPin3, LOW);
  digitalWrite(mcuPin4, LOW);

  lcd.begin(20, 4);  
  lcd.setCursor (0,1);
  lcd.print("    Car  parking  ");
  lcd.setCursor (0,2);
  lcd.print("       System     ");
  delay (2000);
  lcd.clear();
  Read_Sensor();
  int total = S1+S2+S3+S4;
  slot = slot-total;  
}

void loop() {
  long distance1 = getDistance(trigPin1, echoPin1);
  long distance2 = getDistance(trigPin2, echoPin2);
  long distance3 = getDistance(trigPin3, echoPin3);
  long distance4 = getDistance(trigPin4, echoPin4);
  
   // Read the IR sensor value (digital sensor)
  IRsensorValue1 = digitalRead(IRsensorEntrance);  // Read the digital value from the IR sensor
  // Read the IR sensor value (digital sensor)
  IRsensorValue2 = digitalRead(IRsensorExit);  // Read the digital value from the IR sensor

  if (distance1 < safe && distance1 > 0) {
    distanceState1 = HIGH;
    digitalWrite(mcuPin1, HIGH);
} else {
    distanceState1 = LOW;
    digitalWrite(mcuPin1, LOW);
}

if (distance2 < safe && distance2 > 0) {
    distanceState2 = HIGH;
    digitalWrite(mcuPin2, HIGH);
} else {
    distanceState2 = LOW;
    digitalWrite(mcuPin2, LOW);
}

if (distance3 < safe && distance3 > 0) {
    distanceState3 = HIGH;
    digitalWrite(mcuPin3, HIGH);
} else {
    distanceState3 = LOW;
    digitalWrite(mcuPin3, LOW);
}

if (distance4 < safe && distance4 > 0) {
    distanceState4 = HIGH;
    digitalWrite(mcuPin4, HIGH);
} else {
    distanceState4 = LOW;
    digitalWrite(mcuPin4, LOW);
}


  if (distanceState1 == HIGH) {
    if (triggerTime1 == 0) {
      triggerTime1 = millis();
    }

    if (millis() - triggerTime1 >= delayTime1) {
      digitalWrite(ledPin1, HIGH);
      delay(50);
      digitalWrite(ledPin1, LOW);
      delay(50);
    }

    if (millis() - triggerTime1 >= delayTimeA1) {
      digitalWrite(buzzerPin1, HIGH);  // Turn on buzzer after delayTime2
    }
  } else {
    // Reset if the signal goes LOW
    triggerTime1 = 0;
    digitalWrite(ledPin1, LOW);
    digitalWrite(buzzerPin1, LOW);
  }
  
  
  //SENSOR2 (LIGHT BLUE)
  
  
  if (distanceState2 == HIGH) {
    if (triggerTime2 == 0) {
      triggerTime2 = millis();
    }

    if (millis() - triggerTime2 >= delayTime2) {
      digitalWrite(ledPin2, HIGH);
      delay(50);
      digitalWrite(ledPin2, LOW);
      delay(50);
    }

    if (millis() - triggerTime2 >= delayTimeA2) {
      digitalWrite(buzzerPin2, HIGH);  // Turn on buzzer after delayTime2
    }
  } else {
    // Reset if the signal goes LOW
    triggerTime2 = 0;
    digitalWrite(ledPin2, LOW);
    digitalWrite(buzzerPin2, LOW);
  }

   //SENSOR3 (ORANGE)
  
  if (distanceState3 == HIGH) {
    if (triggerTime3 == 0) {
      triggerTime3 = millis();
    }

    if (millis() - triggerTime3 >= delayTime3) {
      digitalWrite(ledPin3, HIGH);
      delay(50);
      digitalWrite(ledPin3, LOW);
      delay(50);
    }

    if (millis() - triggerTime3 >= delayTimeA3) {
      digitalWrite(buzzerPin3, HIGH);  // Turn on buzzer after delayTime2
    }
  } else {
    // Reset if the signal goes LOW
    triggerTime3 = 0;
    digitalWrite(ledPin3, LOW);
    digitalWrite(buzzerPin3, LOW);
  }


  //SENSOR4 (GREEN)
  
  if (distanceState4 == HIGH) {
    if (triggerTime4 == 0) {
      triggerTime4 = millis();
    }

    if (millis() - triggerTime4 >= delayTime4) {
      digitalWrite(ledPin4, HIGH);
      delay(50);
      digitalWrite(ledPin4, LOW);
      delay(50);
    }

    if (millis() - triggerTime4 >= delayTimeA4) {
      digitalWrite(buzzerPin4, HIGH);  // Turn on buzzer after delayTime2
    }
  } else {
    // Reset if the signal goes LOW
    triggerTime4 = 0;
    digitalWrite(ledPin4, LOW);
    digitalWrite(buzzerPin4, LOW);
  }
  
  delay(10);
  

  Read_Sensor();
  lcd.setCursor (0,0);
  lcd.print("   Have Slot: "); 
  lcd.print(slot);
  lcd.print("    ");  
  lcd.setCursor (0,2);
  if(S1==1){lcd.print("S1:Fill ");}
     else{lcd.print("S1:Empty");}
  lcd.setCursor (10,2);
  if(S2==1){lcd.print("S2:Fill ");}
     else{lcd.print("S2:Empty");}
  lcd.setCursor (0,3);
  if(S3==1){lcd.print("S3:Fill ");}
     else{lcd.print("S3:Empty");}
  lcd.setCursor (10,3);
  if(S4==1){lcd.print("S4:Fill ");}
     else{lcd.print("S4:Empty");}


if (IRsensorValue1 == LOW) {
  if (slot > 0 && slot <= 4) { // Check if there are available slots to decrement
    myServo1.write(180); // Move servo to 90 degrees
    Serial.print("MOVING");
    delay(2000);
    myServo1.write(90); // Move servo back to 180 degrees
    slot = slot - 1; // Decrease slot count
    delay(500);
  } else {
    lcd.setCursor(0, 0);
    lcd.print(" Sorry Parking Full ");
    delay(1500);
  }
}

if (IRsensorValue2 == LOW) { // Increment slot if IRsensorValue2 is LOW
  if (slot >= 0 && slot < 4) { // Check if there is room for increment
    myServo2.write(0); // Move servo to 90 degrees
    Serial.print("MOVING");
    delay(2000);
    myServo2.write(90); // Move servo back to 0 degrees
    slot = slot + 1; // Increase slot count
    delay(500);
  }
}

}
  



long getDistance(int trigPin, int echoPin) {
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

 
  long duration = pulseIn(echoPin, HIGH, 30000); 
  
  long distance = duration * 0.034 / 2;

  return distance;
}

void Read_Sensor(){
S1=0, S2=0, S3=0, S4=0;
if(distanceState1 == HIGH){S1=1;}
if(distanceState2 == HIGH){S2=1;}
if(distanceState3 == HIGH){S3=1;}
if(distanceState4 == HIGH){S4=1;}
}

