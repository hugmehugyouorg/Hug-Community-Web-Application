<html>
<head>
<title>Companion Audio</title>
</head>
<body>

<?php echo $error;?>

<?php echo form_open_multipart('audio/upload');?>

<?php echo form_label('Audio Text', 'text'); ?>
<br />
<input type="textarea" name="text" value="<?php echo set_value('text', ''); ?>" />
<br /><br />
<?php echo form_label('Is Message?', 'isMessage'); ?>
<br />
<?php echo form_checkbox('isMessage', '1', FALSE); ?>
<br /><br />
<?php echo form_label('Audio File (MP3 only)', 'userfile'); ?>
<br />
<input type="file" name="userfile" size="20" />

<br /><br />

<input type="submit" value="upload" />

</form>

</body>
</html>