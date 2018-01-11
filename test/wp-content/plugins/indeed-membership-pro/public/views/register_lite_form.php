<div class="iump-register-form  <?php echo @$data['template'];?>">
	<?php do_action('ihc_print_content_before_register_lite_form');?>
	<style><?php echo @$data['css'];?></style>
	<form action="" method="post" name="createuser" id="createuser" class="ihc-form-create-edit" enctype="multipart/form-data" >
		
		<?php if ($data['template']=='ihc-register-6'):?>
			<div class="ihc-register-col">
		<?php endif;?>
		
		<?php if ($data['email_fields']):?>
			<?php echo $data['email_fields'];?>
		<?php endif;?>				
		
		<div class="impu-temp7-row">
			<div class="iump-submit-form">
				<?php echo $data['submit_button'];?>
			</div>			
		</div>
		
		<?php foreach ($data['hidden_fields'] as $hidden_field):?>
			<?php echo $hidden_field;?>
		<?php endforeach;?>

		<?php if ($data['template']==''):?>
			</div>
		<?php endif;?>
		
	</form>
	<?php do_action('ihc_print_content_after_register_lite_form');?>	
</div>
	
<?php if (!empty($data['js'])): ?>
<script><?php echo $data['js'];?></script>
<?php endif;?>