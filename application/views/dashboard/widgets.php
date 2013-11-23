<?php 

	foreach ($groups as $group) 
	{ 
		if(array_key_exists($group->id, $groupToCompanion))
		{
			$companion = $groupToCompanion[$group->id];
			
			echo '<br/><h1>GROUP: '.$group->name.'</h1><br/>';
			if(array_key_exists($companion->id, $companionToUpdates))
			{
				$companionToUpdates = $companionToUpdates[$companion->id];
				
				if($companion->emergency_alert)
					echo '<div style="border: solid 1px #ccc;text-align: center;background-color: red;color: #fff;padding: 5px;"><h2>Serious Situation Alert</h2></div><br/>';
				
				foreach ($companionToUpdates as $update) 
				{ 
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
		  	}
		}
	}
?>	
