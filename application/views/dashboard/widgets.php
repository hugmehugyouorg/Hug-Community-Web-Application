<script type="text/javascript" src="/assets/js/highcharts/highcharts.js"></script>
<?php 
	echo '<div class="widgets row"><div class="pull-right span4">';
	
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
					<a href="#clear-alert-modal-<?php echo $companion->id?>" title="Clear Alert" role="button" class="close" data-toggle="modal"><i class="fa fa-times"></i></a>
					<?php } ?>
					<?php echo $companion->name; ?> reported a Serious Situation with <?php echo $companionToGroup[$companion->id]->name; if($companionToLastEmergencyUpdate[$companion->id]) { ?>&nbsp;&nbsp;<code><?php echo $companionToLastEmergencyUpdate[$companion->id]['timeElapsed']; ?> ago</code><?php } ?>
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
			
			if(array_key_exists($companion->id, $companionToLowBattery))
			{
				$timeElapsed = $companionToLowBattery[$companion->id]['timeElapsed'];
				?>
				<div class="alert"><span class="close no-link"><i class="fa fa-dashboard text-error"></i></span><?php echo $companion->name; ?> has a low battery.&nbsp;&nbsp;<code><?php echo $timeElapsed; ?> ago</code></div>
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
			
			if(array_key_exists($companion->id, $companionToLastUpdateWithEmotion))
			{
				$firstUpdate = $companionToLastUpdateWithEmotion[$companion->id]['update'];
				$timeElapsed = $companionToLastUpdateWithEmotion[$companion->id]['timeElapsed'];
		
				switch($firstUpdate->emotional_state)
				{
					case 0: echo '<div class="alert"><span class="close no-link"><i class="fa fa-meh-o"></i></span>'.$group->name.' has not shared with the team yet.</div>'; break;
					case 1: echo '<div class="alert alert-info"><span class="close no-link"><i class="fa fa-smile-o"></i></span>'.$group->name.' last shared a happy moment.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>'; break;
					case 2: echo '<div class="alert alert-error"><span class="close no-link"><i class="fa fa-frown-o"></i></span>'.$group->name.' last shared an unhappy moment.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>'; break;
					case 3: echo '<div class="alert alert-error"><span class="close no-link"><i class="fa fa-frown-o"></i></span>'.$group->name.' last shared a serious moment.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>'; break;
				}
			}
			else
			{
				echo '<div class="alert"><span class="close no-link"><i class="fa fa-meh-o"></i></span>'.$group->name.'  has not shared with the team yet.</div>';
			}
			
			if(array_key_exists($companion->id, $companionToLastQuietTimeOnUserUpdate))
			{
				$firstUpdate = $companionToLastQuietTimeOnUserUpdate[$companion->id]['update'];
				$timeElapsed = $companionToLastQuietTimeOnUserUpdate[$companion->id]['timeElapsed'];
				
				echo '<div class="alert"><span class="close no-link" style="margin-right:5px;"><i class="fa fa-volume-off"></i></span>'.$group->name.' wants some quiet time, shh!&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>';
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
			
			if(array_key_exists($companion->id, $companionToLastChargingUpdate))
			{
				$firstUpdate = $companionToLastChargingUpdate[$companion->id]['update'];
				$timeElapsed = $companionToLastChargingUpdate[$companion->id]['timeElapsed'];
		
				if(!$firstUpdate->is_charging)
				{
					echo '<div class="alert"><span class="close no-link" style="margin-right:5px;"><i class="fa fa-dashboard"></i></span>'.$companion->name.' is battery powered at '.$firstUpdate->voltage.' Volts.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>';
				}
				else
				{
					echo '<div class="alert clearfix"><span class="close no-link" style="margin-right:5px;"><i class="fa fa-flash"></i></span>'.$companion->name.' is recharging at '.$firstUpdate->voltage.' Volts.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>';
				}
			}
		}
	}
	?>
	</div></div><div class="span8">
	
	<?php
	foreach ($groups as $group) 
	{ 
		if(array_key_exists($group->id, $groupToCompanion))
		{
			$companion = $groupToCompanion[$group->id];
			
			echo '<h2>'.$group->name.'</h2>';
			if(array_key_exists($companion->id, $companionToEmotionUpdates))
			{
				$updates = $companionToEmotionUpdates[$companion->id];
				
				$companionUpdatesFound = false;
				foreach ($updates as $update) 
				{ 
					if(!$companionUpdatesFound)
					{
						$companionUpdatesFound = true;
						?>
						
						<div id="chart-<?php echo $companion->id; ?>"></div>
						<script type="text/javascript" >
							$(function () {
								$('#chart-<?php echo $companion->id; ?>').highcharts({
									chart: {
										type: 'areaspline',
										zoomType: 'x',
										spacingRight: 20
									},
									title: {
										text: '<?php echo $group->name; ?> emotional health'
									},
									subtitle: {
										text: document.ontouchstart === undefined ?
											'Click and drag in the plot area to zoom in' :
											'Pinch the chart to zoom in'
									},
									xAxis: {
										type: 'datetime',
										dateTimeLabelFormats: {
											millisecond: '%I:%M:%S.%L %P',
											second: '%I:%M:%S %P',
											minute: '%I:%M %P',
											hour: '%I:%M %P'
										},
										title: {
											text: 'Time'
										}
									},
									yAxis: {
										title: {
											text: null
										},
										categories: ['Serious', 'Unhappy', 'Happy'],
										gridLineColor: '#FFFFFF'
									},
									tooltip: {
										formatter: function() {
												return '<b>'+ Highcharts.dateFormat('%a, %d %b %Y %I:%M %P', this.x) +'</b><br/>'+this.key;
										}
									},
									legend: {
										enabled: false
									},
									credits: {
										  enabled: false
									},
									series: [{
										name: 'Emotion',
										data: [<?php
			   		}
			   		$usersTimezone = new DateTimeZone('America/Chicago');
					$l10nDate = new DateTime($update->created_at);
					$l10nDate->setTimeZone($usersTimezone);
					$timestamp = $l10nDate->format('Y-m-d H:i:s');
			   		?>
			   									{x: new Date(<?php echo strtotime($timestamp)*1000;?>), <?php
													switch($update->emotional_state)
													{
														case 3: echo 'name: "Serious", y: 0'; break;
														case 2: echo 'name: "Unhappy", y: 1'; break;
														case 1: echo 'name: "Happy", y: 2'; break;
													}
												?>},<?php
		  		}
		  		if(!$companionUpdatesFound)
		  		{
		  			echo 'No updates.';
		  		}
		  		else
		  		{
		  			?>
										]
									}]
								});
							});
						</script>
					<?php
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