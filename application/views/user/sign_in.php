	<h1>Sign In</h1>
	
	<p>Please sign in with your email and password below.</p>
	
	<?php if(isset($message) && !empty($message)){ ?>
		<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
	<?php } ?>
	
	<?php echo form_open("sign_in", array('id'=>'login-form'));?>
	
	  <div class="clearfix">
		<label for="identity">Email:</label>
		<div class="input"><?php echo form_input($identity);?></div>
	  </div>
	  
	  <div class="clearfix">
		<label for="password">Password:</label>
		<div class="input"><?php echo form_input($password);?></div>
	  </div>
	  
	  <div class="input"><?php echo form_submit(array('class'=>'btn primary', 'value'=>'Sign In'), 'Sign In');?></div>
	
	<?php echo form_close();?>
	
	<p><a href="forgot_password">Forgot your password?</a></p>