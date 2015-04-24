<html>
<head>
    <title>Laravel</title>

    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>

    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            color: #B0BEC5;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
            margin-bottom: 40px;
        }

        .quote {
            font-size: 24px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title"><a id="auth" href="{{$authUrl}}">Sign in with VK</a></div>
        <form id="handleForm" method="post" style="display: none">
            <input type="text" name="code" placeholder="Input code here">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <button type="submit">Submit</button>
        </form>
    </div>
</div>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script>
    $('#auth').click(function(e) {
        e.preventDefault();

        var timer = setInterval(checkChild, 500);
        var child = window.open($(this).attr('href'),'','toolbar=0,status=0,width=650,height=500');
        function checkChild() {
            if (child.closed) {
                $('#handleForm').show();
                clearInterval(timer);
            }
        }


    });
</script>
</body>
</html>
