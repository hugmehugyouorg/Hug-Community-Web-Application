<h1>Edit the Safety Team</h1>
<?php if(isset($message) && !empty($message)){ ?>
		<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
	<?php } ?>
<div class="tabbable">
	<ul class="nav nav-pills">
		<li class="active"><a href="#basic" data-toggle="tab">Basic Info</a></li>
		<li><a href="#add-invite" data-toggle="tab">Add/Invite</a></li>
		<li><a href="#leaders" data-toggle="tab">Leaders</a></li>
		<li><a href="#community" data-toggle="tab">Community</a></li>
	</ul>
	<div class="tab-content well">
		<div class="tab-pane active" id="basic">
			<fieldset>
				<legend>Safety Team's Basic Information</legend>
				
				<p class="muted">Edit the Safety Team's most basic information.</p>
				
				<?php echo form_open(current_url());?>
				
					  <p>
							Child's Name/Nickname: <br />
							<?php echo form_input($group_name); ?>
					  </p>
					  <p>
					  		Safety Sam's Nickname: <br />
							<?php echo form_input($companion_name); ?>
					  </p>
					  <p>
							Team Description: <br />
							<?php echo form_input($group_description);?>
					  </p>
				
					  <p><?php echo form_submit('submit', 'Update');?></p>
				
				<?php echo form_close();?>
			</fieldset>
		</div>
		<div class="tab-pane" id="add-invite">
			<fieldset>
				<legend>Add/Invite others to the Safety Team</legend>
				
				<p class="muted">Invite new members or add existing ones.</p>
				
				<p><a href="<?php echo site_url('group/invite/'.$group_id);?>">Invite a New Community Member</a></p>
				<p><a href="<?php echo site_url('group/add/'.$group_id);?>">Add an Existing <?php if($is_admin) { ?>Team Leader or <?php } ?>Community Member</a></p>
			</fieldset>
		</div>
		<div class="tab-pane" id="leaders">
			<fieldset>
				<legend>Safety Team Leaders</legend>
				<p class="muted">Below is a list of the Safety Team Leaders.</p>
				
				<table class="table table-striped table-bordered table-condensed footable toggle-arrow toggle-medium">
					<thead>
						<tr>
							<th>Last Name, First Name</th>
							<th data-hide="phone">Email</th>
							<th data-hide="phone">Phone</th>
							<?php if($is_admin) { ?>
								<th>Remove</th>
								<th>Profile</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($superUsers as $user):?>
							<tr>
								<td><?php echo $user->last_name;?>, <?php echo $user->first_name;?></td>
								<td><?php echo $user->email;?></td>
								<td><?php echo $user->phone;?></td>
								<?php if($is_admin) { ?>
									<td>
										<a href="#remove-group-user-modal-<?php echo $group_id;?>-<?php echo $user->id;?>" title="Remove User from the Safety Team" role="button" data-toggle="modal">Remove</a>
										<!-- Modal -->
										<div id="remove-group-user-modal-<?php echo $group_id;?>-<?php echo $user->id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="remove-group-user-modal-label-<?php echo $group_id;?>-<?php echo $user->id;?>" aria-hidden="true">
										  <div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
											<h3 id="remove-group-user-modal-label-<?php echo $group_id;?>-<?php echo $user->id;?>" class="text-error">Are you sure about this?</h3>
										  </div>
										  <div class="modal-body">
											<p>This will remove <strong><?php echo $user->last_name.', '.$user->first_name; ?></strong> from the Safety Team.</p>
										  </div>
										  <div class="modal-footer">
											<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
											<?php echo anchor("group/remove/".$group_id."/".$user->id, 'Yes, I\'m sure', 'class="btn btn-primary"') ;?>
										  </div>
										</div>
									</td>
									<td><?php echo anchor("profile/".$user->id, 'Profile') ;?></td>
								<?php } ?>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="tab-pane" id="community">
			<fieldset>
				<legend>Safety Team Community Members</legend>
				<p class="muted">Below is a list of the Safety Team Community Members.</p>
			
				<table class="table table-striped table-bordered table-condensed footable toggle-arrow toggle-medium">
					<thead>
						<tr>
							<th>Last Name, First Name</th>
							<th data-hide="phone">Email</th>
							<th data-hide="phone">Phone</th>
							<th>Remove</th>
							<?php if($is_admin) { ?>
								<th>Profile</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $user):?>
							<tr>
								<td><?php echo $user->last_name;?>, <?php echo $user->first_name;?></td>
								<td><?php echo $user->email;?></td>
								<td><?php echo $user->phone;?></td>
								<td>
									<a href="#remove-group-user-modal-<?php echo $group_id;?>-<?php echo $user->id;?>" title="Remove User from the Team" role="button" data-toggle="modal">Remove</a>
									<!-- Modal -->
									<div id="remove-group-user-modal-<?php echo $group_id;?>-<?php echo $user->id;?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="remove-group-user-modal-label-<?php echo $group_id;?>-<?php echo $user->id;?>" aria-hidden="true">
									  <div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
										<h3 id="remove-group-user-modal-label-<?php echo $group_id;?>-<?php echo $user->id;?>" class="text-error">Are you sure about this?</h3>
									  </div>
									  <div class="modal-body">
										<p>This will remove the user from the team.</p>
									  </div>
									  <div class="modal-footer">
										<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
										<?php echo anchor("group/remove/".$group_id."/".$user->id, 'Yes, I\'m sure', 'class="btn btn-primary"') ;?>
									  </div>
									</div>
								</td>
								<?php if($is_admin) { ?>
									<td><?php echo anchor("profile/".$user->id, 'Profile') ;?></td>
								<?php } ?>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</fieldset>
		</div>
	</div>
</div>