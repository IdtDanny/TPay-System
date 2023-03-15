import http.requests.*;

import processing.serial.*;
Serial arduino;

String card="LOADING...";
String response="CHECKING";
float money;
float business_pin;

PFont century;

void setup(){
  size(550,550);
  background(#3264C6);
   // Getting Money
   money = getFloat("Amount to pay:");
   print("Amount: $" + money);
   loadStrings("http://localhost/tpay/cps/reference.php?id="+money);
 
   // Getting Money
   business_pin = getFloat("Paid to:");
   print("Business pin: $" + business_pin);
   loadStrings("http://localhost/tpay/cps/business_Send.php?id="+business_pin); 
   
  arduino=new Serial(this,"COM5",115200);
}
void draw(){
 if(arduino.available()>0){
    String data=arduino.readString() ;
    if(data!=" "){
      data = data.substring(1,data.length());
      data = data.replace(" ","");
      println(data);// print data from arduino
      
      String[] send=loadStrings("http://localhost/tpay/cps/check.php?id="+data);
      
      delay(500);
      if(send.length>0){
        if(send[0].indexOf("ok")!=-1){   
            size(550,550);
            smooth();
            
            //getting the payment page
            GetRequest get = new GetRequest("http://localhost/tpay/cps/payment.php?id="+send[0]);
            get.send();
            
            //printing the message
            rect(40,200,450,200);
            
            // Loading Fonts          
              century = loadFont("CenturyGothic-22.vlw");
              textFont(century);
              //textSize(18);
            
            smooth();
            fill(#4B49C4);
            text(get.getContent(),100,230,280,400);
            println("Reponse Content: " + get.getContent());
          //link("http://localhost/cps/payment.php?id="+send[0]);
                delay(100);
            response="Known User";
            // return();            
                
        }
        else{
          //printing the message
            rect(40,200,400,200);
            smooth();
            
            // Loading Fonts           //<>//
              century = loadFont("CenturyGothic-22.vlw");
              textFont(century);
              //textSize(18);
              
            fill(#FC0A0E);
            text("You are Not Registered\n" ,100,230,280,400);
            response="Unknown User";   
        }
      card=data;
      println(send[0]);
      }
      //card=data;
      //println(send[0]);
    }
  }
  smooth();
  noStroke();
  
  rect(15,8,width-35,height-470);
  
  century = loadFont("CenturyGothic-22.vlw");
  
  textFont(century);  
  
  //textSize(20);
  
  fill(#49649B);
  text("Welcome To TAP&PAY SYSTEM",110,55);

  fill(255);
  rect(25,105,width-55,height-470);
  
  //status
  fill(#4B49C4);
  text("ID ="+card,40,130);
  fill(#FC0A0E);
  text("Result ="+response,40,175);
  fill(255);
  text("copyright reserved 2022",140,height-20);
  
  //Exiting or closing the program by pressing any key
  if(keyPressed){
        exit();
  }

  
}
