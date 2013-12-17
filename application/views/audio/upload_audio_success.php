<html>
<head>
<title>Companion Audio</title>
<link type="text/css" href="/assets/css/jplayer.blue.monday.css" rel="stylesheet" />
<link type="text/css" href="/assets/css/style.jplayer.override.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/jplayer/jquery.jplayer.min.js"></script>
</head>
<body>

<h3>The MP3 was successfully saved!  ID = <?php echo $audioNum; ?></h3>

<ul>
<?php foreach ($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<?php echo $player; ?>

<p><?php echo anchor('audio/upload', 'Upload Another File!'); ?></p>

</body>
</html>