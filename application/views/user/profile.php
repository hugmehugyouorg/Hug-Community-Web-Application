<h1>Profile</h1>
<p>Please update the information below.</p>

<?php if(isset($message) && !empty($message)){ ?>
	<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
<?php } ?>

<?php echo form_open(uri_string());?>

      <p>
            First Name: <br />
            <?php echo form_input($first_name);?>
      </p>

      <p>
            Last Name: <br />
            <?php echo form_input($last_name);?>
      </p>
	  
       <div class="control-group">
		<div class="controls">
            Phone: <br />
            <?php echo form_input($phone1);?>-<?php echo form_input($phone2);?>-<?php echo form_input($phone3);?>
            <div class="row-fluid">
				<div class="span5">
					<label class="checkbox" for="phone_alerts">
						<input id="mobile_alerts" name="mobile_alerts" type="checkbox" value="1" <?php echo set_checkbox('mobile_alerts', '1', $user->mobile_alerts ? TRUE : FALSE); ?>>
						Would you like to receive alerts on your phone?
						<span class="help-block"><span class="text-info"><small>If you would like to receive alerts on your phone, please verify your number is for a mobile device.&nbsp;&nbsp;Alerts are sent as SMS messages and only mobile phones are able to receive such alerts.</small></span></span>
					</label>
				</div>
			</div>
	  	</div>
	  </div>
	<div class="clearfix"></div>
      <p>
            Password: (if changing password)<br />
            <?php echo form_input($password);?>
      </p>

      <p>
            Confirm Password: (if changing password)<br />
            <?php echo form_input($password_confirm);?>
      </p>
      
	<?php if( count($currentGroups) != 0 && FALSE ) { ?>
	
		 <h3>Member of Safety Teams</h3>
		<?php foreach ($groups as $group):?>
		<label class="checkbox">
		<?php
			$gID=$group['id'];
			$checked = null;
			$item = null;
			foreach($currentGroups as $grp) {
				if ($gID == $grp->id) {
					$checked= ' checked="checked"';
				break;
				}
			}
		?>
	
		<input type="checkbox" name="groups[]" value="<?php echo $group['id'];?>"<?php echo $checked;?>>
		<?php echo $group['name'];?>
		</label>
		<?php endforeach?>
		
	<?php } ?>
	
      <?php echo form_hidden('id', $user->id);?>
      <?php echo form_hidden($csrf); ?>

      <p><?php echo form_submit('submit', 'Update Profile');?></p>

<?php echo form_close();?>
