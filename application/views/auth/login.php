	<h1>Login</h1>
	
	<div class="alert-message info">
		<p>Please login with your email and password below.</p>
	</div>
	
	<?php if(isset($message) && !empty($message)){ ?>
		<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
	<?php } ?>
	
	<?php echo form_open("auth/login", array('id'=>'login-form'));?>
	
	  <div class="clearfix">
		<label for="identity">Email:</label>
		<div class="input"><?php echo form_input($identity);?></div>
	  </div>
	  
	  <div class="clearfix">
		<label for="password">Password:</label>
		<div class="input"><?php echo form_input($password);?></div>
	  </div>
	
	  <div class="clearfix">
		  <label>Remember Me</label>  
		  <div class="input">
			  <label for="remember"><?php echo form_checkbox('remember', '1', FALSE, 'class="input"');?></label>
		 </div>
		  
	  </div>
	  
	  
	  <div class="input"><?php echo form_submit(array('class'=>'btn primary', 'value'=>'login'), 'Login');?></div>
	
	<?php echo form_close();?>
	
	<p><a href="forgot_password">Forgot your password?</a></p>