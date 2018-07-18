
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>{$faucetname}</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-default">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">{$faucetname}</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            {$navBar}

          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <div class="container">
      <div id="containertop">
        <div class="row">
          <div class="col-md-6 col-md-offset-3">{$spacetop}</div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="col-md-3">
        <div id="advertising">
          {$spaceleft}
        </div>
      </div>
      <div class="col-md-6">
        <div class="well content">
          {$content}
<iframe data-aa='692409' src='//ad.a-ads.com/692409?size=468x60' scrolling='no' style='width:468px; height:60px; border:0px; padding:0;overflow:hidden' allowtransparency='true'></iframe>
        </div>
      </div>
      <div class="col-md-3">
        <div id="advertising">
          {$spaceright}
        </div>
      </div>
    </div>



    <footer>
      <div class="container">
<center><script data-cfasync="false" type="text/javascript" src="//www.bitcoadz.io/display/items.php?2388&163&468&60&4"></script>
</center>
        <div class="row">
          <div class="col-md-12">
            <hr />
            <span style="color: grey;">&copy; 2018 {$faucetname}</span>
          </div>
        </div>
      </div>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://authedmine.com/lib/simple-ui.min.js" async></script>
  </body>
</html>
