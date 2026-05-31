console.log("Hello world");   // คำสั่งนี้จะทำให้ข้อความ "Hello world" ปรากฏใน console ของเว็บเบราว์เซอร์ ซึ่งในวงเล็บสามารถทำเป็นสมการคำนวณหรือใส่ค่าต่างๆได้ตามต้องการ
var name = "John";   // ประกาศตัวแปรชื่อ name และกำหนดค่าเป็น "John":ซึ่งสามารถใส่ค่าต่างๆได้ตามต้องการ
console.log(name);   // แสดงค่าของตัวแปร name ใน console
var age = 30+9+2*1-4/3;   // สามารถใช้เครื่องหมายต่างๆในการคำนวณได้เช่น + - * / % ** //และสามารถใช้วงเล็บเพื่อกำหนดลำดับการคำนวณได้
console.log(age);   // แสดงค่าของตัวแปร age ใน console 
alert("Welcome to JavaScript!");   // แสดงกล่องข้อความด้านบนที่มีข้อความ "Welcome to JavaScript!" เมื่อโหลดหน้าเว็บ

//javascript Let
{
    var x =2
    let y = 3
    console.log(y);    //คำสั่งที่อยู่ใน{}เมื่อรันคำสั่งvar x จะแสดงผลได้หมดไม่ว่าจะอยู่ใน{}หรือไม่ แต่คำสั่งlet y จะแสดงผลได้เฉพาะใน{}เท่านั้น
}
console.log(x); 
//javascript pompt>   
prompt("What is your name?");   // แสดงกล่องข้อความที่มีช่องให้ผู้ใช้กรอกข้อมูลและปุ่มยืนยัน ซึ่งสามารถใส่ข้อความบอกผู้ใช้เพิ่มเติมได้ในวงเล็บ
let userName = prompt("What is your name?");   // ประกาศตัวแปร userName และกำหนดค่าเป็นผลลัพธ์ที่ผู้ใช้กรอกในกล่องข้อความ
console.log("Hello, " + userName + "!");   // แสดงข้อความที่มีข้อความ "Hello, " ตามด้วยค่าของตัวแปร userName และเครื่องหมายตกใจ :ซึ่งจะแสดงผลเป็นstringเสมอไม่ว่าจะเป็นตัวเลขหรือข้อความก็ตาม

//javascript math random
let randomNum = prompt("Enter a number:")
console.log(randomNum);
Math.floor(Math.random()*10);   //ฟังก์ชันสุ่มตัวเลขโดยที่จะสุ่มได้แค่0-1แต่หากอยากได้ขอบเขตมากกว่านี้ให้ใส่*แล้วมันจะแสดงขอบเขตจาก0ถึง(n-1)
console.log(Math.floor(Math.random()*10));



