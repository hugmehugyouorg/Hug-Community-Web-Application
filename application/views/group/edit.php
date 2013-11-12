<h1>Edit the Safety Team</h1>
<p>Please enter the Safety Team information below.</p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open(current_url());?>

	  <p>
			Safety Team Name: <br />
			<?php echo form_input($group_name); ?>
	  </p>
      <p>
            Safety Team Description: <br />
            <?php echo form_input($group_description);?>
      </p>

      <p><?php echo form_submit('submit', 'Update');?></p>

<?php echo form_close();?>

<h2>Safety Team Leaders</h2>
<p>Below is a list of the Safety Team Leaders.</p>

<table cellpadding=0 cellspacing=10>
	<tr>
		<th>Last Name, First Name</th>
		<th>Email</th>
		<th>Phone</th>
		<?php if($is_admin) { ?>
			<th>Remove</th>
			<th>Profile</th>
		<?php } ?>
	</tr>
	<?php foreach ($superUsers as $user):?>
		<tr>
			<td><?php echo $user->last_name;?>, <?php echo $user->first_name;?></td>
			<td><?php echo $user->email;?></td>
			<td><?php echo $user->phone;?></td>
			<?php if($is_admin) { ?>
				<td><?php echo anchor("group/remove/".$group_id."/".$user->id, 'Remove') ;?></td>
				<td><?php echo anchor("profile/".$user->id, 'Profile') ;?></td>
			<?php } ?>
		</tr>
	<?php endforeach;?>
</table>

<h2>Safety Team Community Members</h2>
<p>Below is a list of the Safety Team Community Members.</p>

<table cellpadding=0 cellspacing=10>
	<tr>
		<th>Last Name, First Name</th>
		<th>Email</th>
		<th>Phone</th>
		<th>Remove</th>
		<?php if($is_admin) { ?>
			<th>Profile</th>
		<?php } ?>
	</tr>
	<?php foreach ($users as $user):?>
		<tr>
			<td><?php echo $user->last_name;?>, <?php echo $user->first_name;?></td>
			<td><?php echo $user->email;?></td>
			<td><?php echo $user->phone;?></td>
			<td><?php echo anchor("group/remove/".$group_id."/".$user->id, 'Remove') ;?></td>
			<?php if($is_admin) { ?>
				<td><?php echo anchor("profile/".$user->id, 'Profile') ;?></td>
			<?php } ?>
		</tr>
	<?php endforeach;?>
</table>

<p><a href="<?php echo site_url('group/invite/'.$group_id);?>">Invite a New Safety Team Community Member</a></p>
<p><a href="<?php echo site_url('group/add/'.$group_id);?>">Add a New Safety Team <?php if($is_admin) { ?>Team Leader or <?php } ?>Community Member</a></p>