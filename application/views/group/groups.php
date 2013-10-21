<h1>Safety Teams</h1>
<p>Below is a list of the Safety Teams you manage.</p>

<div id="infoMessage"><?php echo $message;?></div>

<table cellpadding=0 cellspacing=10>
	<tr>
		<th>Name</th>
		<th>Description</th>
	</tr>
	<?php foreach ($groups as $group) { ?>
		<tr>
			<td><?php echo anchor("group/".$group->id, $group->name) ;?></td>
			<td><?php echo $group->description;?></td>
		</tr>
	<?php } ?>
</table>
<?php if($is_admin) { ?>
	<p><a href="<?php echo site_url('group/create');?>">Create a New Safety Team</a></p>
<?php } ?>