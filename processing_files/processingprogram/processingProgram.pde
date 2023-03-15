import http.requests.*;

import processing.serial.*;
Serial arduino;

String card="LOADING...";
String response="CHECKING";
float money;

void setup(){
  size(550,550);
  background(#3264C6);
   // Getting Money
   money = getFloat("Amount to pay:");
   print("Amount: $" + money);
   
  arduino=new Serial(this,"COM4",115200);
    
}
void draw(){
 if(arduino.available()>0){
    String data=arduino.readString() ;
    if(data!=" "){
      data = data.substring(1,data.length());
      data = data.replace(" ","");
      println(data);// print data from arduino
      
      String[] send=loadStrings("http://localhost/tpay/cps/check.php?id="+data);
      loadStrings("http://localhost/tpay/cps/reference.php?id="+money);
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
            smooth();
            fill(#4B49C4);
            text("You are welcome: \n" + get.getContent(),100,220,280,400);
            println("Reponse Content: " + get.getContent());
          //link("http://localhost/cps/payment.php?id="+send[0]);
                delay(100);
            response="Known User";
            // return();              
                
        }
        else{
          //printing the message
            rect(40,200,500,200);
            smooth();
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
  textSize(20);
  fill(255);
  text("Welcome To TAP&PAY SYSTEM",25,25);

  stroke(255);
  rect(25,35,width-55,height-450);
  
  //status
  fill(#4B49C4);
  text("ID ="+card,40,65);
  fill(#FC0A0E);
  text("Result ="+response,40,115);
  fill(255);
  text("copyright reserved 2022",40,height-10);
  
  //Exiting or closing the program by pressing any key
  if(keyPressed){
        exit();
  }

  
}
