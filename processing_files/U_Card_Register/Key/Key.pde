
void setup()
{
   size(300,300);
   background(#3264C6);
   String name = getString("Client Name:");
   float money = getFloat("Amount to pay:");
   print("Hello " + name);
   print("Amount: $" + money);
   
    //status
  textSize(18);
  fill(#FFFFFF);
  text("Client: "+name,40,75);
  fill(#FFFFFF);
  text("Amount: $ "+money,40,135);
} 
