<h1>Add New Members to the Safety Team</h1>

<?php if(isset($message) && !empty($message)){ ?>
		<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
	<?php } ?>

<?php echo form_open("group/add/".$id);?>
<div class="tabbable">
	<ul class="nav nav-pills">
		<?php if($is_admin) { ?><li class="active"><a href="#leaders" data-toggle="tab">Leaders</a></li><?php } ?>
		<li class="<?php if(!$is_admin) { ?>active<?php } ?>"><a href="#community" data-toggle="tab">Community</a></li>
	</ul>
	<div class="tab-content well">
		<?php if($is_admin) { ?>
			<div class="tab-pane active" id="leaders">
				<fieldset>
					<legend>Add New Safety Team Leaders</legend>
					<p class="muted">Below is a list of Administrators and Social Workers.&nbsp;&nbsp;Please choose one or more to add.</p>
					<select data-dropup-auto="false" name="leaders[]" multiple data-selected-text-format="count">
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
				</fieldset>
			</div>
		<?php } ?>
		<div class="tab-pane <?php if(!$is_admin) { ?>active<?php } ?>" id="community">
			<fieldset>
				<legend>Add New Safety Team Community Members</legend>
				<p class="muted">Below is a list of Community Members.&nbsp;&nbsp;Please choose one or more to add.</p>
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
			</fieldset>
		</div>
	</div>
</div>
<p><?php echo form_submit('submit', 'Add');?></p>
<p><a href="<?php echo site_url('group/'.$id.'#add-invite');?>">Cancel</a></p>
<?php echo form_close();?>