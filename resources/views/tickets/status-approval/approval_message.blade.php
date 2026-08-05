<!DOCTYPE html>
<html>
<head>
    <title>Approval Status</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f6f6f6;
            padding:40px;
        }
        .box{
            max-width:500px;
            margin:auto;
            background:white;
            padding:30px;
            border-radius:8px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
            text-align:center;
        }
        .success{ color:#1e7e34; }
        .error{ color:#c82333; }
        .warning{ color:#e0a800; }

        .btn{
            display:inline-block;
            margin-top:20px;
            padding:10px 18px;
            background:#007bff;
            color:white;
            text-decoration:none;
            border-radius:5px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2 class="{{ $type ?? 'success' }}">
        {{ $msg ?? 'Action completed' }}
    </h2>

    <a href="{{ url('/') }}" class="btn">Go Home</a>
</div>

</body>
</html>