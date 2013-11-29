<?php 
	echo '<div class="widgets"><div class="pull-right">';
	
	if($hasAlerts)
	{
		echo '<div class="alert alert-error widget"><h1>Alerts</h1>';
		foreach ($companions as $companion) 
		{
			if($companion->emergency_alert)
			{
				?>
				<div class="alert alert-error">
					<?php if($leader) { ?>
					<a href="#clear-alert-modal-<?php echo $companion->id?>" title="Clear Alert" role="button" class="close" data-toggle="modal" style="font-size: 200%; top: -3px;">&times;</a>
					<?php } ?>
					<?php echo $companionToGroup[$companion->id]->name; ?> has indicated there is a serious situation
					<?php if($leader) { ?>
						<!-- Modal -->
						<div id="clear-alert-modal-<?php echo $companion->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="clear-alert-modal-label" aria-hidden="true">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h3 id="clear-alert-modal-label" class="text-error">Are you sure about this?</h3>
						  </div>
						  <div class="modal-body">
							<p>This will clear the serious situation alert for <?php echo $companionToGroup[$companion->id]->name; ?>.</p>
						  </div>
						  <div class="modal-footer">
							<button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
							<a href="/dashboard?clear_alert=<?php echo $companion->id?>" class="btn btn-primary">Yes</a>
						  </div>
						</div>
					<?php } ?>
				</div>
				<?php
			}
		}
		echo '</div>';
	}

	echo "<div class='alert alert-info widget'><h1>Child's Play</h1>";
	foreach ($companions as $companion) 
	{ 
		if(array_key_exists($companion->id, $companionToGroup))
		{
			$group = $companionToGroup[$companion->id];
			
			if(array_key_exists($companion->id, $companionToLastUpdate))
			{
				$firstUpdate = $companionToLastUpdate[$companion->id];
		
				switch($firstUpdate->emotional_state)
				{
					case 0: echo '<div class="alert"><span class="muted">'.$group->name.' has not shared with the team yet</span></div>'; break;
					case 1: echo '<div class="alert alert-info">'.$group->name.' last shared a happy moment <span class="close no-link">&#9786;</span></div>'; break;
					case 2: echo '<div class="alert alert-error">'.$group->name.' last shared an uhappy moment <span class="close no-link">&#9785;</span></div>'; break;
					case 3: echo '<div class="alert alert-error">'.$group->name.' last shared a serious moment <span class="close no-link">&#9785;</span></div>'; break;
				}
			}
			else
			{
				echo '<div class="alert"><span class="muted">'.$group->name.'  has not shared with the team yet</span></div>';
			}
		}
	}
	echo '</div>';
	
	echo "<div class='alert alert-success widget'><h1>Safety Sam</h1>";
	foreach ($companions as $companion) 
	{ 
		if(array_key_exists($companion->id, $companionToGroup))
		{
			$group = $companionToGroup[$companion->id];
			
			if(array_key_exists($companion->id, $companionToLastUpdate))
			{
				$firstUpdate = $companionToLastUpdate[$companion->id];
		
				if(!$firstUpdate->is_charging)
				{
					echo '<div class="alert"><span class="muted">'.$companion->name.' is battery powered at '.$firstUpdate->voltage.' Volts </span><span class="close no-link"><i class="text-error fa fa-flash"></i></span></div>';
				}
				else
				{
					echo '<div class="alert"><span class="muted">'.$companion->name.' is recharging at '.$firstUpdate->voltage.' Volts </span><span class="close no-link"><i class="fa fa-flash text-error"></i></span></div>';
				}
			}
		}
	}
	echo '</div>';
	
	echo '</div><div>';
	foreach ($groups as $group) 
	{ 
		if(array_key_exists($group->id, $groupToCompanion))
		{
			$companion = $groupToCompanion[$group->id];
			
			echo '<h2>Safety Team: '.$group->name.'</h2>';
			if(array_key_exists($companion->id, $companionToUpdates))
			{
				$updates = $companionToUpdates[$companion->id];
				
				$companionUpdatesFound = false;
				foreach ($updates as $update) 
				{ 
					$companionUpdatesFound = true;
					$usersTimezone = new DateTimeZone('America/Chicago');
					$l10nDate = new DateTime($update->created_at);
					$l10nDate->setTimeZone($usersTimezone);
					$date = $l10nDate->format('g:i:s A M j Y');
					?>
					<strong><?php echo $date;?></strong>
					<br/>Emotional State: <?php if($update->emotional_state == 3) echo 'SERIOUS'; else if($update->emotional_state == 2)  echo 'UNHAPPY'; if($update->emotional_state == 1) echo 'HAPPY'; if($update->emotional_state == 0)  echo 'UNKNOWN'; ?> 
					<br/><?php if($update->quiet_time) echo "It's Quiet Time"; else echo "It's Not Quiet Time"; ?>
					<br/>Battery is <?php if($update->is_charging) echo 'being charged'; else echo 'is not being charged';?>
					<br/>Estimating Battery at <?php echo $update->voltage;?> Volts
					<?php if($update->last_said_id) echo '<br/>Last Said Id: '.$update->last_said_id; ?> 
					<?php if($update->last_message_said_id) echo '<br/>Last Message Said Id: '.$update->last_message_said_id; ?> 
					<br/><br/>
		  <?php }
		  		if(!$companionUpdatesFound)
		  		{
		  			echo 'No updates.';
		  		}
		  	}
		  	else
		  	{
		  		echo 'No updates.';
		  	}
		}
	}
	echo '</div></div>';
?>	
