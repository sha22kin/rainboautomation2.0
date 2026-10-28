<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="{{asset(general()->favicon())}}">
<title>Your Verify OPT Code Form {{general()->title}} </title>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">

<style>

body{
margin:0;
background:#f1f1f1;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  font-size:14px;
  padding: 8px;
}



	@media only screen and (max-width: 600px) {


		}

</style>

</head>
<body>
<div style="margin:25px auto;width:80%;min-width:600px;overflow:auto;padding:15px;background:#fff;">

<center>
<img src="{{URL::asset(general()->logo())}}" style="max-width:200px;">
<p style="margin:0;">Welcome To {{general()->title}} !</p>
</center>

<div style="background:#f1f1f1;padding:10px;margin: 10px 0;">
  <p style="margin: 0;border-bottom: 1px solid #c6c6c6;padding: 5px 0;">VERIFY CODE </p>
  <br>
  <center>
  <span>Verify Code: {{$datas['verifycode']}}</span>
  </center>
  <br>
  <p style="margin:0;">Your Verify Code is secret. Can not Share other one.</p>
  <br>
</div>

<div style="background:#f1f1f1;padding:10px;margin: 10px 0;">
    <p style="margin: 0;border-bottom: 1px solid #c6c6c6;padding: 5px 0;">CONTACT US</p>
  <p>{!!general()->address_one!!} <br><strong>Email: </strong>{{general()->email}}<br> <strong>Mob: </strong>{{general()->mobile}} <br><a href="{{general()->website}}" target="_blank">{{general()->website}}</a> </p>
</div>

</div>
</body>
</html>

