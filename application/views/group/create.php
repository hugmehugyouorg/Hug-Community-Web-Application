<h1>Create a Safety Team</h1>
<p>Please enter the Safety Team information below.</p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("group/create");?>

      <p>
            Safety Team Name: <br />
            <?php echo form_input($group_name);?>
      </p>

      <p>
            Safety Team Description: <br />
            <?php echo form_input($description);?>
      </p>

	  <h2>Assign a Therapeutic Companion</h2>
	  <p>Below is a list of the Unassigned Therapeutic Companions.&nbsp;&nbsp;Please choose one.</p>
	  
	  <select name="companion">
		  <?php foreach($companions as $row) { ?>
		  	<option value="<?=$row->id?>" <?php if($companion_id == $row->id) echo 'selected'; ?>><?=$row->name?></option>
		  <?php } ?>
	  </select>
	  
	  <h2>Assign Safety Team Leaders</h2>
	  <p>Below is a list of all Administrators and Social Workers.&nbsp;&nbsp;Please choose one or more.</p>
	  
	  <select name="leaders[]" multiple data-selected-text-format="count">
		  <?php foreach($groupLeaders as $row) { ?>
		  	<option value="<?=$row->id?>" <?php  
		  		foreach($currentLeaders as $leader) {
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
      <p><?php echo form_submit('submit', 'Create');?></p>

<?php echo form_close();?>