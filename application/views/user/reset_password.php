<h1>Change Password</h1>

<?php if(isset($message) && !empty($message)){ ?>
	<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
<?php } ?>

<?php echo form_open('reset_password/' . $code);?>

	<p>
		New Password (at least <?php echo $min_password_length;?> characters long): <br />
		<?php echo form_input($new_password);?>
	</p>

	<p>
		Confirm New Password: <br />
		<?php echo form_input($new_password_confirm);?>
	</p>

	<?php echo form_input($user_id);?>
	<?php echo form_hidden($csrf); ?>

	<p><?php echo form_submit('submit', 'Change');?></p>

<?php echo form_close();?>