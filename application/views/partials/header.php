<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
		
	<title><?php echo $title ?></title>
   <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
   <link rel="stylesheet" href="/assets/css/bootstrap-responsive.min.css" /> 
    <link rel="stylesheet" href="/assets/css/style.css" />
    
	<script type="text/javascript" src="//www.google.com/jsapi"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
  <script type="text/javascript" src="/assets/js/bootstrap.min.js" ></script>
	<script type="text/javascript" src="/assets/js/script.js" ></script>

</head>

<body id="<?php echo isset($bodyID) ? $bodyID : ''; ?>">
  <div id="main" class="container">
    <div class="navbar " style="position: static;">
        <div class="navbar-inner">
          <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/"><?php echo $title ?></a>
            <div class="nav-collapse collapse">
              <?php if($loggedIn){ ?>
              <ul class="nav pull-right">
                <li><a href="/auth/logout">Logout</a></li>
              </ul>
              <?php } ?>
            </div><!-- /.nav-collapse -->
          </div>
        </div><!-- /navbar-inner -->
      </div>