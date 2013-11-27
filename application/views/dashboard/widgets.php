<style>
	.alert
	{
		margin-left:20px;
		padding-right: 14px;
	}
	.alert h1
	{
		text-align:center;
	}
	.alert ul
	{
		margin-right: 8px;
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
	$alertFound = false;
	foreach ($companions as $companion) 
	{
		if($companion->emergency_alert)
		{
			if(!$alertFound)
			{
				$alertFound = true;
				echo '<div class="alert alert-error pull-right"><h1>Alerts</h1><ul>';
			}
			echo '<li>'.$companionToGroup[$companion->id]->name.' (Serious Situation)</li>';
		}
	}
	
	if($alertFound)
	{
		echo '</ul></div>';
	}
	else
	{
		echo '<div class="alert alert-success pull-right">No Alerts</div>';
	}

	echo '<div style="pull-left;">';
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
