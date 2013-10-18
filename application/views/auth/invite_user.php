<h1>Invite User</h1>
<p>Please enter the users information below.</p>

<?php if(isset($message) && !empty($message)){ ?>
	<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
<?php } ?>

<?php echo form_open("invite_user");?>

      <p>
            First Name: <br />
            <?php echo form_input($first_name);?>
      </p>

      <p>
            Last Name: <br />
            <?php echo form_input($last_name);?>
      </p>

      <p>
            Email: <br />
            <?php echo form_input($email);?>
      </p>

      <p>
            Phone: <br />
            <?php echo form_input($phone1);?>-<?php echo form_input($phone2);?>-<?php echo form_input($phone3);?>
      </p>

      <p><?php echo form_submit('submit', 'Invite User');?></p>

<?php echo form_close();?>