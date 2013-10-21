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

<h2>Users</h2>
<p>Below is a list of the users.</p>

<table cellpadding=0 cellspacing=10>
	<tr>
		<th>Last Name, First Name</th>
		<th>Email</th>
		<th>Status</th>
		<th>Profile</th>
	</tr>
	<?php foreach ($users as $user):?>
		<tr>
			<td><?php echo $user->last_name;?>, <?php echo $user->first_name;?></td>
			<td><?php echo $user->email;?></td>
			<td><?php echo ($user->active) ? anchor("deactivate/".$user->id, 'Active') : anchor("activate/". $user->id, 'Inactive');?></td>
			<td><?php echo anchor("profile/".$user->id, 'Profile') ;?></td>
		</tr>
	<?php endforeach;?>
</table>

<p><a href="<?php echo site_url('group/invite/'.$group_id);?>">Invite a New User</a></p>