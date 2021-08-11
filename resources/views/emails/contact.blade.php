<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EGC-Cuba</title>
    <style>
        body{
            background-color: rgba(116, 216, 216, 0.904);
        }
        .btn{
            padding: 8px;
            background-color: rgb(102, 102, 102);
            border-radius: 4px;
            color: aliceblue;
            transition: .6s;
            margin: 5px;
            text-decoration: none;
        }

        .btn:hover{
            background-color: rgb(75, 75, 75);
            cursor: pointer;
        }
        table{
            font-size: 1.5rem;
        }
    </style>
</head>
<body style="background-color: #718096;">
    <center>
    <div style="background-color: #ffffff; padding: 20px; border-radius: 5px;">
    <h1>Private|Wire</h1>
    <br>
    <table border="0" cellspacing="10">
        <tr>
            <td>Name:</td>
            <td><b>{{$name}}</b></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><a href="mailto:{{$email}}">{{$email}}</a></td>
        </tr>
        <tr>
            <td colspan="2">Commect:</td>
        </tr>
        <tr>
            <td colspan="2">{{$commentc}}</td>
        </tr>
    </table>
    </div>
</center>
</body>
</html>
