<?php
if (!empty($_POST['url'])){
	ihc_add_new_redirect_link($_POST);
} else if (isset($_POST['delete_redirect_link'])){
	ihc_delete_redirect_link($_POST['delete_redirect_link']);
}
?>			
<form method="post" action="" id="redirect_links_form">
	<input type="hidden" value="" name="delete_redirect_link" id="delete_redirect_link" />
	<div class="ihc-stuffbox">
		<h3><?php _e('Redirect Links', 'ihc');?></h3>
		<div class="inside">
		<h2><?php _e('Redirect Links', 'ihc');?></h2>
		<p style="margin-top:0px;"><?php _e('Add custom Links from inside or outside of your Website that can be used for Redirects inside the Membership system', 'ihc');?></p>
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 15px 0;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Name:', 'ihc');?></span>
						<input type="text" class="form-control" name="name"value="" />
					</div>
				</div>
				</div>		
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:0px 0 15px 0;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Custom Link:', 'ihc');?></span>
						<input type="text" class="form-control" name="url"value="" />
					</div>
				</div>
				</div>	
						
			<div style="margin-top: 15px;">
				<input type="submit" value="<?php _e('Add New', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>	
		</div>
	</div>
</form>
<?php 
$data = get_option('ihc_custom_redirect_links_array');
if ($data && count($data)){
	?>
	<div class="ihc-dashboard-form-wrap">
		<table class="wp-list-table widefat fixed tags" style="margin-bottom: 20px;">
			<thead>
				<tr>
					<th class="manage-column"><?php _e('Name', 'ihc');?></th>
					<th class="manage-column"><?php _e('Link', 'ihc');?></th>
					<th class="manage-column" style="width:80px;  text-align: center;"><?php _e('Delete', 'ihc');?></th>
				</tr>
			</thead>
			<?php 
				$i = 1;
				foreach ($data as $key=>$url){
				?>
				<tr class="<?php if ($i%2==0) echo 'alternate';?>">
					<td><?php echo $key;?></td>
					<td><?php echo $url;?></td>
					<td align="center">
						<i class="fa-ihc ihc-icon-remove-e" style="cursor:pointer;" onClick="jQuery('#delete_redirect_link').val('<?php echo $key;?>');jQuery('#redirect_links_form').submit();"></i>
					</td>
				</tr>
				<?php 
				}
				?>
		</table>
	</div>
<?php 
}
