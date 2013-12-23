<h1>Add New Members to the Safety Team</h1>

<?php if(isset($message) && !empty($message)){ ?>
		<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
	<?php } ?>

<?php echo form_open("group/add/".$id);?>

	  <?php if($is_admin) { ?>
		  <h2>Add New Safety Team Leaders</h2>
		  <p>Below is a list of Administrators and Social Workers.&nbsp;&nbsp;Please choose one or more to add.</p>
		  
		  <select name="leaders[]" multiple data-selected-text-format="count">
			  <?php foreach($groupLeaders as $row) { ?>
				<option value="<?=$row->id?>" <?php  
					foreach($newLeaders as $leader) {
						if ($row->id == $leader) {
							echo 'selected';
							break;
						}
					}
				?>>
					<?=$row->last_name?>, <?=$row->first_name?>
				</option>
			  <?php } ?>
		  </select>
	  <?php } ?>
	  
	  <h2>Add New Safety Team Members</h2>
	  <p>Below is a list of Community Members.&nbsp;&nbsp;Please choose one or more to add.</p>
	  
	  <select name="members[]" multiple data-selected-text-format="count">
		  <?php foreach($groupMembers as $row) { ?>
		  	<option value="<?=$row->id?>" <?php  
		  		foreach($newMembers as $member) {
					if ($row->id == $member) {
						echo 'selected';
						break;
					}
				}
		  	?>>
		  		<?=$row->last_name?>, <?=$row->first_name?>
		  	</option>
		  <?php } ?>
	  </select>
	  
      <p><?php echo form_submit('submit', 'Add');?></p>
      <p><a href="<?php echo site_url('group/'.$id.'#add-invite');?>">Cancel</a></p>

<?php echo form_close();?>