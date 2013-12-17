<!-- BEGIN AUDIO TEMPLATE -->
<div id="jquery_jplayer_<?php echo $audioNum; ?>" class="jp-jplayer"></div>
<div id="jp_container_<?php echo $audioNum; ?>" class="jp-audio">
	<div class="jp-type-single">
	  <div class="jp-gui jp-interface">
		<ul class="jp-controls">
		  <li><a href="javascript:;" class="jp-play" tabindex="1" title="<?php echo $audioText; ?>">play</a></li>
		  <li><a href="javascript:;" class="jp-pause" tabindex="1" title="<?php echo $audioText; ?>">pause</a></li>
		</ul>
	  </div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  $("#jquery_jplayer_<?php echo $audioNum; ?>").jPlayer({
	ready: function () {
	  $(this).jPlayer("setMedia", {
		mp3: "<?php echo $audioURL; ?>"
	  });
	},
	swfPath: "assets/js/jplayer",
	supplied: "mp3",
	play: function() { // To avoid multiple jPlayers playing together.
		$(this).jPlayer("pauseOthers");
	},
	cssSelectorAncestor: "#jp_container_<?php echo $audioNum; ?>",
  });
});
</script>
<!-- END AUDIO TEMPLATE -->