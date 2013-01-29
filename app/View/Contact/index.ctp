<h2>Contact</h2>
<?php 
echo $this->Form->create('Contact', array('type'=>'file'));
echo $this->Form->input('name');
echo $this->Form->input('email');
echo $this->Form->input('message');
?>

<?php if (!empty($this->data['Contact']['filepath'])): ?>
	<div class="input">
		<label>Uploaded File</label>
		<?php
		echo $this->Form->input('filepath', array('type'=>'hidden'));
		echo $this->Html->link(basename($this->data['Contact']['filepath']), $this->data['Contact']['filepath']);
		?>
	</div>
<?php else: ?>
	<?php echo $this->Form->input('filename',array(
		'type' => 'file'
	)); ?>
<?php endif; ?>

<?php
echo $this->Form->end('Submit');
?>