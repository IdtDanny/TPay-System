import http.requests.*;

import processing.serial.*;
Serial arduino;

String card="LOADING...";
String response="CHECKING";
float money;
float agent_pin;

PFont century;

void setup(){
  size(550,550);
  background(#49649B);
   // Getting Money
   
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
            
            // Getting the amount from the user to top up
            money = getFloat("Amount to Top_Up:");
            print("Amount: " + money);
            loadStrings("http://localhost/tpay/topup/topup.php?id="+money);
            
            // Getting the current agent identity
            agent_pin = getFloat("Enter your Pin:");
            print("Agent Pin: " + agent_pin);
            loadStrings("http://localhost/tpay/topup/agent_pin.php?id="+agent_pin);
            
            
            //getting the payment page
            GetRequest get = new GetRequest("http://localhost/tpay/topup/topup_return.php?id="+send[0]);
            get.send();
            
            //printing the message
            rect(40,200,450,210);
            
            // Loading Fonts          
              century = loadFont("CenturyGothic-22.vlw");
              textFont(century);
              //textSize(18);
            
            smooth();
            fill(#499B69);
            text(" \n" + get.getContent(),100,230,280,250);
            fill(#4B49C4);
            println("Reponse Content: " + get.getContent());
          //link("http://localhost/cps/payment.php?id="+send[0]);
                delay(100);
            response="Known User";
            // return();              
                
        }
        else{
          //printing the message
            rect(40,200,450,200);
            smooth();
              
              // Loading Fonts          
              century = loadFont("CenturyGothic-22.vlw");
              textFont(century);
            
            fill(#FC0A0E);
            text("You are Not Registered\n" ,100,250,300,400);
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
  text("Welcome To TAP&PAY SYSTEM",110,40);
  fill(#49649B);
  text("Tap your card to Recharge",125,70);

  fill(255);
  rect(25,105,width-55,height-470);
  
  //status
  fill(#4B49C4);
  text("ID ="+card,40,130);
  fill(#FC0A0E);
  text("Result ="+response,40,175);
  fill(255);
  text("Copyright reserved 2022",140,height-20);
  
  //Exiting or closing the program by pressing any key
  if(keyPressed){
        exit();
  }

  
}
