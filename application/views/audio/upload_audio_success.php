<html>
<head>
<title>Companion Audio</title>
<link type="text/css" href="/assets/css/jplayer.blue.monday.css" rel="stylesheet" />
<style>
div.jp-audio,
div.jp-video {
  /* Edit the font-size to counteract inherited font sizing.
   * Eg. 1.25em = 1 / 0.8em
   */
  font-size:1.25em;
}
* {
	outline: none;
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/jplayer/jquery.jplayer.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
      $("#jquery_jplayer_1").jPlayer({
        ready: function () {
          $(this).jPlayer("setMedia", {
            mp3: "<?php echo $audioURL; ?>"
          });
        },
        swfPath: "assets/js/jplayer",
        supplied: "mp3"
      });
    });
  </script>
</head>
<body>

<h3>The MP3 was successfully saved!  ID = <?php echo $audioNum; ?></h3>

<ul>
<?php foreach ($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<div id="jquery_jplayer_1" class="jp-jplayer"></div>
  <div id="jp_container_1" class="jp-audio">
    <div class="jp-type-single">
      <div class="jp-gui jp-interface">
        <ul class="jp-controls">
          <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
          <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
          <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
          <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
          <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
          <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
        </ul>
        <div class="jp-progress">
          <div class="jp-seek-bar">
            <div class="jp-play-bar"></div>
          </div>
        </div>
        <div class="jp-volume-bar">
          <div class="jp-volume-bar-value"></div>
        </div>
        <div class="jp-time-holder">
          <div class="jp-current-time"></div>
          <div class="jp-duration"></div>
        </div>
      </div>
      <div class="jp-title">
        <ul>
          <li><?php echo $audioText; ?></li>
        </ul>
      </div>
      <div class="jp-no-solution">
        <span>Update Required</span>
        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
      </div>
    </div>
  </div>

<p><?php echo anchor('audio/upload', 'Upload Another File!'); ?></p>

</body>
</html>