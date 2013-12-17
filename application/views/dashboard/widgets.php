<link type="text/css" href="/assets/css/jplayer.blue.monday.css" rel="stylesheet" />
<link type="text/css" href="/assets/css/style.jplayer.override.css" rel="stylesheet" />
<script type="text/javascript" src="/assets/js/highcharts/highcharts.js"></script>
<script type="text/javascript" src="/assets/js/jplayer/jquery.jplayer.min.js"></script>
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
					<a href="#clear-alert-modal-<?php echo $companion->id?>" title="Clear Alert" role="button" class="close" data-toggle="modal"><i class="fa fa-times fa-2x"></i></a>
					<?php } ?>
					<?php echo $companion->name; ?> reported a Serious Situation with <?php echo $companionToGroup[$companion->id]->name; if($companionToLastEmergencyUpdate[$companion->id]) { ?>&nbsp;&nbsp;<code><?php echo $companionToLastEmergencyUpdate[$companion->id]['timeElapsed']; ?> ago</code><?php } ?>
					<?php if($leader) { ?>
						<!-- Modal -->
						<div id="clear-alert-modal-<?php echo $companion->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="clear-alert-modal-label" aria-hidden="true">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
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
				<div class="alert"><span class="close no-link"><i class="fa fa-dashboard text-error fa-2x"></i></span><?php echo $companion->name; ?> has a low battery.&nbsp;&nbsp;<code><?php echo $timeElapsed; ?> ago</code></div>
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
					case 0: echo '<div class="alert"><span class="close no-link"><i class="fa fa-meh-o fa-2x"></i></span>'.$group->name.' has not shared with the team yet.</div>'; break;
					case 1: echo '<div class="alert alert-info"><span class="close no-link"><i class="fa fa-smile-o fa-2x"></i></span>'.$group->name.' shared a happy moment.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>'; break;
					case 2: echo '<div class="alert alert-error"><span class="close no-link"><i class="fa fa-frown-o fa-2x"></i></span>'.$group->name.' shared an unhappy moment.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>'; break;
					case 3: echo '<div class="alert alert-error"><span class="close no-link"><i class="fa fa-frown-o fa-2x"></i></span>'.$group->name.' shared a serious moment.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>'; break;
				}
			}
			else
			{
				echo '<div class="alert"><span class="close no-link"><i class="fa fa-meh-o fa-2x"></i></span>'.$group->name.'  has not shared with the team yet.</div>';
			}
			
			if(array_key_exists($companion->id, $companionToLastQuietTimeOnUserUpdate))
			{
				$firstUpdate = $companionToLastQuietTimeOnUserUpdate[$companion->id]['update'];
				$timeElapsed = $companionToLastQuietTimeOnUserUpdate[$companion->id]['timeElapsed'];
				
				echo '<div class="alert"><span class="close no-link"><i class="fa fa-microphone-slash fa-2x"></i></span>'.$group->name.' wants some quiet time, shh!&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>';
			}
		}
	}
	echo '</div>';
	
	$replyModals = array();
	
	echo "<div class='alert alert-success widget'><h1>Safety Sam</h1>";
	foreach ($companions as $companion) 
	{ 
		if(array_key_exists($companion->id, $companionToGroup))
		{
			$group = $companionToGroup[$companion->id];
			
			if(array_key_exists($companion->id, $companionToLastSaid))
			{
				$firstUpdate = $companionToLastSaid[$companion->id]['update'];
				$timeElapsed = $companionToLastSaid[$companion->id]['timeElapsed'];
				$text = $companionToLastSaid[$companion->id]['text'];
				$player = $companionToLastSaid[$companion->id]['player'];
				?>
				<div class="alert" style="min-height:80px;">
					<span class="close no-link" style="right: -26px;">
						<?php echo $player; ?>
						<a href="#send-a-message-modal-<?php echo $companion->id; ?>" title="Reply" role="button" class="close-mimic" data-toggle="modal">
							<i class="fa fa-cloud-upload fa-2x"></i>
						</a>
						<?php $replyModal = '<!-- Modal -->
						<div id="send-a-message-modal-'.$companion->id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="send-a-message-modal-label" aria-hidden="true">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
							<h3 id="send-a-message-modal-label">Reply</h3>
						  </div>
						  <div class="modal-body" >
						  	<div class="row-fluid">
								<p class="muted"><em>"'.$text.'"</em></p>
								<p></p>
								<div class="row-fluid">
									<select class="dont-auto-render span11 companion-messages-select" id="companion-messages-'.$companion->id.'"></select>
								</div>
							</div>
							<hr/>
						  	<div style="text-align: right; width:100%;font-size:1.1em;">
						  		<div class="pull-left" style="width:30%;padding:5px; border-right: 1px solid #ccc; padding-right:2%;"><p class="text-warning" style="font-size:1.15em;">Step 1 <i class="fa fa-reply"></i></p><p>You can reply to '.$group->name.' by selecting a message to send.</p></div>
						  		<div class="pull-left" style="width:30%;padding:5px; border-right: 1px solid #ccc; padding-right:2%;"><p class="text-warning" style="font-size:1.15em;">Step 2 <i class="fa fa-cloud"></i></p><p>We\'ll have our CloudCarriers&#153; deliver it to '.$companion->name.'.</p></div>
						  		<div class="pull-left" style="width:30%;padding:5px; "><p class="text-warning" style="font-size:1.15em;">Step 3 <i class="fa fa-volume-up"></i></p><p>'.$companion->name.' will play it back for '.$group->name.', it\'s as simple as that.</p></div>
						  		<div class="clearfix"></div>
						  	</div>
							<div class="row-fluid">
								<hr/>
								<span class="text-info pull-right">That\'s the power of the Hug Community.</span>
								<p>&nbsp;</p>
							</div>
						  </div>
						  <div class="modal-footer">
							<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
							<button class="btn btn-primary btn-send-reply" data-companion-id="'.$companion->id.'">Send</button>
						  </div>
						</div>'; array_push($replyModals, $replyModal); ?>
					</span>
					<?php echo $companion->name;?> had this to say, <em>"<?php echo $text; ?>"</em>
					&nbsp;&nbsp;
					<code><?php echo $timeElapsed; ?>ago</code>
				</div>
				<?php
			}
			
			if(array_key_exists($companion->id, $companionToLastChargingUpdate))
			{
				$firstUpdate = $companionToLastChargingUpdate[$companion->id]['update'];
				$timeElapsed = $companionToLastChargingUpdate[$companion->id]['timeElapsed'];
		
				if(!$firstUpdate->is_charging)
				{
					echo '<div class="alert"><span class="close no-link"><i class="fa fa-dashboard fa-2x"></i></span>'.$companion->name.' is battery powered at '.$firstUpdate->voltage.' Volts.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>';
				}
				else
				{
					echo '<div class="alert clearfix"><span class="close no-link" style="right: -14px;"><i class="fa fa-flash fa-2x"></i></span>'.$companion->name.' is recharging at '.$firstUpdate->voltage.' Volts.&nbsp;&nbsp;<code>'.$timeElapsed.' ago</code></div>';
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
										text: '<?php echo $group->name; ?> emotional health <i class="fa fa-stethoscope"></i>',
										useHTML: true
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
	echo '</div>';
	?>
		<div id="success-modal" class="modal hide fade" tabindex="-1" style="text-align:center;">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
			<h3>Success!</h3>
		  </div>
		  <div class="modal-body">
			<p><i class="fa fa-heart fa-5x fa-lg"></i></p>
		  </div>
		</div>
		
		<div id="error-modal" class="modal hide fade" tabindex="-1" style="text-align:center;">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
			<h3>A Problem Was Encountered</h3>
		  </div>
		  <div class="modal-body">
			<p><i class="fa fa-bug fa-5x fa-lg"></i></p>
			<p>Please try again.</p>
		  </div>
		</div>
	<?php
	foreach( $replyModals as $replyModal)
	{
		echo $replyModal;
	}
	?>
	<script type="text/javascript">
			$(function () {
				$.ajax({
					url: "/dashboard/getMessages",
					type: "GET",
					cache: false,
					success: function (r) {
						var json = $.parseJSON(r);
						
						if(json)
						{
							var options = "";
							$.each(json, function(index, item){
								options += "<option value='" + item.id + "'>" + item.text + "</option>";
							});
							$(".companion-messages-select").html(options).selectpicker();
							
							$('.companion-messages-select').on('change', function(e, isTriggered) {
								if(!this.value)
									return;
									
								var selfie = $(this);
								var ancestor = selfie.parent();
								ancestor.find('#companion-message-audio-player').remove();
								
								$.ajax({
									url: "/dashboard/getAudioPlayer",
									type: "GET",
									data: { id : this.value },
									cache: false,
									success: function (r) {
										ancestor.append("<div id='companion-message-audio-player' class='span1 pull-right' style='margin-top:-5px;'>"+r+"</div>");
										var player = $('#companion-message-audio-player').find('.jp-jplayer');
										
										if(!isTriggered)
										{
											player.bind($.jPlayer.event.ready, function(event) {
												player.jPlayer("play");
											});
										}
										
										$('.btn-send-reply').unbind('click');
										$('.btn-send-reply').bind('click', function(e) {
											var companionId = $(this).data('companionId');
											
											if(player)
												player.jPlayer("stop");
														
											$.ajax({
												url: "/dashboard/sendAudioMessage",
												type: "GET",
												cache: false,
												data: { audioId : selfie.val(), companionId: companionId},
												success: function (r) {
													if(player)
														player.jPlayer("stop");
													$('#send-a-message-modal-'+companionId).modal('hide');
													$('#success-modal').modal('show');
												},
												error: function( jqXhr ) {
													if( jqXhr.status == 401 )
														window.location = '/sign_in';
												}
											});
										});
									},
								
									error: function( jqXhr ) {
										if( jqXhr.status == 401 )
											window.location = '/sign_in';
									}
								});
							});
							$('.companion-messages-select').trigger('change', [true]);
						}
					},
				
					error: function( jqXhr ) {
						if( jqXhr.status == 401 )
							window.location = '/sign_in';
					}
				});
			});
		</script>
	<?php
	echo '</div>';
?>