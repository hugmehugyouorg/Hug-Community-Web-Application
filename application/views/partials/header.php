<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	
	<!-- fix for IE9 Google Font Issue -->
	<meta http-equiv="X-UA-Compatible" content="IE=8" />

	<!-- responsive design so don't let scaling occur -->
	<meta name="viewport" content="width = device-width, initial-scale = 1, minimum-scale = 1, maximum-scale = 1, user-scalable = no" />
	 
	<!-- favicon
	<link rel="icon" href="//www.hugmehugyou.org/img/favicon.gif" type="image/gif"/>
	 -->
	 
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
          	<?php if($signInPage == 'false') { ?>
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				  <span class="icon-bar"></span>
				  <span class="icon-bar"></span>
				  <span class="icon-bar"></span>
				</a>
			<?php } ?>
			<?php if($user && $homeLink == 'dashboard'){ ?>
				<a class="brand" href="/dashboard">
					<!--<img src="//www.hugmehugyou.org/img/logo.png">-->
					Dashboard
				</a>
            <?php } else { ?>
            	<a class="brand" href="/">
            	<!--<img src="//www.hugmehugyou.org/img/logo.png">-->
            		Welcome
            	</a>
            <?php }  ?>
            <?php if($signInPage == 'false') { ?>
				<div class="nav-collapse collapse">
				  <ul class="nav pull-right">
				  	<?php if($user){ ?>
				  		<?php if($homeLink == 'dashboard'){ ?>
							<li><a href="/">Welcome</a></li>
						<?php } else { ?>
							<li><a href="/dashboard">Dashboard</a></li>
						<?php } ?>
						<li><a href="/profile/<?php echo $user->id ?>">Profile</a></li>
						<li><a href="/sign_out">Sign Out</a></li>
					<?php } else { ?>
						<li><a href="/sign_in">Sign In</a></li>
					<?php }  ?>
				  </ul>
				</div><!-- /.nav-collapse -->
			<?php }  ?>
          </div>
        </div><!-- /navbar-inner -->
      </div>