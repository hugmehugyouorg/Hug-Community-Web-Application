<style>
	.childs-play 
	{
		
	}
	.alert
	{
		margin-left:20px;
		padding-right: 14px;
		min-width: 250px;
	}
	.alert h1
	{
		text-align:center;
	}
	.alert ul
	{
		margin-right: 8px;
	}
	@media (min-width: 768px)
	{
		.alert
		{
			background-color: transparent;
			border: none;
		}
	}
	@media (max-width: 767px)
	{
		.alert
		{
			margin-left:0px;
		}
		.pull-right, .pull-left {
			
			float:none;
		}
	}
</style>
<?php 
	echo '<div class="pull-right">';
	
	if($hasAlerts)
	{
		echo '<div class="alert alert-error"><h1>Alerts</h1><ul>';
		foreach ($companions as $companion) 
		{
			if($companion->emergency_alert)
			{
				echo '<li>'.$companionToGroup[$companion->id]->name.' reported a Serious Situation <span style="font-size:125%;">&#9785;</span></li>';
			}
		}
		echo '</ul></div>';
	}
	else
	{
		echo '<div class="alert alert-success"><h1>No Alerts</h1></div>';
	}

	echo "<div class='childs-play alert alert-info pull-right'><h1>Childs Play</h1><ul>";
	foreach ($companions as $companion) 
	{ 
		if(array_key_exists($companion->id, $companionToGroup))
		{
			$group = $companionToGroup[$companion->id];
			
			if(array_key_exists($companion->id, $companionToFirstUpdate))
			{
				$firstUpdate = $companionToFirstUpdate[$companion->id];
		
				if($firstUpdate)
				{
					switch($firstUpdate->emotional_state)
					{
						case 0: echo '<li class="muted">'.$group->name.' has not shared with the team yet</li>'; break;
						case 1: echo '<li class="text-success">'.$group->name.' last shared a happy moment <span style="font-size:125%;">&#9786;</span></li>'; break;
						case 2: echo '<li class="text-warning">'.$group->name.' last shared an uhappy moment <span style="font-size:125%;">&#9785;</span></li>'; break;
						case 3: echo '<li class="text-error"><strong>'.$group->name.' last shared a serious moment <span style="font-size:125%;">&#9785;</span></li>'; break;
					}
				}
			}
			else
			{
				echo '<li class="muted">'.$group->name.'  has not shared with the team yet</li>';
			}
		}
	}
	
	echo '</ul></div></div><div style="pull-left;">';
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
	echo '</div>';
?>	
