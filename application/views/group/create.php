<h1>Create a Safety Team</h1>
<p>Please enter the Safety Team information below.</p>

<div id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("group/create");?>

      <p>
            Safety Team Name: <br />
            <?php echo form_input($group_name);?>
      </p>

      <p>
            Safety Team Description: <br />
            <?php echo form_input($description);?>
      </p>

      <p><?php echo form_submit('submit', 'Create');?></p>

<?php echo form_close();?>