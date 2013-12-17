<table class="table" cellpadding=0 cellspacing=10>
	<thead>
	<tr>
		<?php foreach($fields as $key => $value) { ?>
		<th><?php echo $key; ?></th>
		<?php } ?>
	</tr>
	</thead>
		<tbody>
		<?php foreach ($data as $row) { ?>
			<tr>
				<?php foreach ($fields as $key => $value) { ?>
				<td><?php echo $row->$value; ?></td>
				<?php } ?>
			</tr>
		<?php } ?>
	<tbody>
</table>