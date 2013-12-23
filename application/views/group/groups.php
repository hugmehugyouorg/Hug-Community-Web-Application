<h1>Safety Teams</h1>
<p>Below is a list of the Safety Teams you manage.</p>

<?php if(isset($message) && !empty($message)){ ?>
		<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
	<?php } ?>
	
<table class="table table-striped table-bordered table-condensed footable toggle-circle-filled toggle-medium">
	<thead>
		<tr>
			<th>Name</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($groups as $group) { ?>
			<tr>
				<td><?php echo anchor("group/".$group->id, $group->name) ;?></td>
				<td><?php echo $group->description;?></td>
			</tr>
		<?php } ?>
	<tbody>
</table>

<?php if($is_admin) { ?>
	<p><a href="<?php echo site_url('group/create');?>">Create a New Safety Team</a></p>
<?php } ?>
