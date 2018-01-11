<?php
if (empty($total_items)){
	die("Direct access not permitted");
}
$dir_path = plugin_dir_path (__FILE__);
$style="<style>".file_get_contents( $dir_path.'style.css')."
</style>";
$list_item_template = '
<div class="team-member">
<div class="member-img">
 <a #POST_LINK#>
 <img title="IHC_FIRST_NAME IHC_LAST_NAME" src="IHC_AVATAR" alt=""/>
 </a>
</div>
<div class="member-content">
<div class="member-name">
<a #POST_LINK#>
IHC_FIRST_NAME IHC_LAST_NAME
</a> 
</div>	
<div style="text-align:center;">
<div class="member-username">
IHC_USERNAME
 </div>
 </div>
<div class="member-social">
	IHC_SOCIAL_MEDIA
  </div>
<div class="member-email">
IHC_EMAIL
</div>
<div class="member-extra-fields">
IHC_EXTRA_FIELDS
</div>
</div>
';

$socials_arr = array(
		'ihc_fb' => '<a href="FB" target="_blank" class="facebook"><i class="fa-ihc-sm fa-ihc-fb"></i></a>',
		'ihc_tw' => '<a href="TW" target="_blank" class="twitter"><i class="fa-ihc-sm fa-ihc-tw"></i></a>',
		'ihc_in' => '<a href="LIN" target="_blank" class="linkedin"><i class="fa-ihc-sm fa-ihc-in"></i></a>',
		'ihc_goo' => '<a href="GP" target="_blank" class="gplus"><i class="fa-ihc-sm fa-ihc-goo"></i></a>',
		'ihc_ig' => '<a href="INS" target="_blank" class="instagramm"><i class="fa-ihc-sm fa-ihc-ig"></i></a>',
		'ihc_tbr' => '<a href="TBR" target="_blank" class="Tumblr"><i class="fa-ihc-sm fa-ihc-tbr"></i></a>',
		'ihc_vk' => '<a href="VK" target="_blank" class="Vk"><i class="fa-ihc-sm fa-ihc-vk"></i></a>',
);