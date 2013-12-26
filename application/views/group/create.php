<h1>Create a Safety Team <p><small>*All the fields below are required.</small></p></h1>

<?php if(isset($message) && !empty($message)){ ?>
		<div class="alert alert-message error" id="infoMessage"><?php echo $message;?></div>
	<?php } ?>

<?php echo form_open(current_url());?>
<div class="tabbable">
	<ul class="nav nav-pills">
		<li class="active"><a href="#basic" data-toggle="tab">Basic Info</a></li>
		<li><a href="#companion" data-toggle="tab">Therapeutic Companion</a></li>
		<li><a href="#leaders" data-toggle="tab">Leaders</a></li>
	</ul>
	<div class="tab-content well">
		<div class="tab-pane active" id="basic">
			<fieldset>
				<legend>Safety Team's Basic Information</legend>
				<p class="muted">Enter the Safety Team's most basic information.</p>
				<p>
					Child's Name/Nickname: <br />
					<?php echo form_input($group_name); ?>
				</p>
				<p>
					Team Description: <br />
					<?php echo form_input($description);?>
				</p>
			</fieldset>
		</div>
		<div class="tab-pane" id="companion">
			<fieldset>
				<legend>Assign a Safety Sam Therapeutic Companion</legend>
				<p class="muted">Below is a list of the Unassigned Safety Sam Therapeutic Companions.&nbsp;&nbsp;Please choose one and give it a nickname.</p>
				<select data-dropup-auto="false" name="companion">
				  <?php foreach($companions as $row) { ?>
					<option value="<?=$row->id?>" <?php if($companion_id == $row->id) echo 'selected'; ?>><?=$row->name?></option>
				  <?php } ?>
				</select>
				<p>
					Safety Sam's Nickname: <br />
					<?php echo form_input($companion_name); ?>
				</p>
			</fieldset>
		</div>
		<div class="tab-pane" id="leaders">
			<fieldset>
				<legend>Assign Safety Team Leaders</legend>
				<p class="muted">Below is a list of all Administrators and Social Workers.&nbsp;&nbsp;Please choose one or more.</p>
				<select data-dropup-auto="false" name="leaders[]" multiple data-selected-text-format="count">
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
			</fieldset>
		</div>
	</div>	
</div>
<p><?php echo form_submit('submit', 'Create');?></p>
<?php echo form_close();?>