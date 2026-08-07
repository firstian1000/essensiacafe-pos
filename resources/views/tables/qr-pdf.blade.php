<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>

        @page{
            size:A4;
            margin:1cm;
        }

        body{
            font-family:Arial, Helvetica, sans-serif;
        }

        .wrapper{
            width:5cm;
            height:5cm;

            border:1px solid #444;

            border-radius:8px;

            text-align:center;

            padding:5px;
        }

        .title{

            font-size:16px;
            font-weight:bold;

            margin-top:3px;
        }

        .table{

            font-size:20px;
            font-weight:bold;

            margin-top:4px;
            margin-bottom:5px;
        }

        img{

            width:3.2cm;
            height:3.2cm;
        }

        .footer{

            margin-top:5px;

            font-size:10px;
        }

    </style>

</head>

<body>

<div class="wrapper">

    <div class="title">

        Cafe Order

    </div>

    <div class="table">

        {{ strtoupper($table->table_number) }}

    </div>

    <img src="{{ public_path('storage/qrcodes/'.$table->qr_image) }}">

    <div class="footer">

        Scan QR untuk memesan

    </div>

</div>

</body>
</html>