<h1>Safety Teams</h1>
<p>Below is a list of the Safety Teams you manage.</p>

<div id="infoMessage"><?php echo $message;?></div>
<div id="team-list-panel" class="panel panel-default">
	<div class="panel-body">
		<?php 
			/*
			* TODO
			* Encapsulate table/cell-rendering
			* in partials and helpers
			*/
		 ?>
		<table class="table" cellpadding=0 cellspacing=10>
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
	</div>
</div>
<?php if($is_admin) { ?>
	<p><a href="<?php echo site_url('group/create');?>">Create a New Safety Team</a></p>
<?php } ?>
