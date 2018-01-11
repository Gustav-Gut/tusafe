function indeed_search_value(v){
	var current_tab = jQuery('.indeed-addons-categories-list').attr('data-current-tag');
	
	if (v==''){
		if (current_tab==''){
			//display all
			jQuery('.indeed-item-wrapp').css('display', 'block');
		} else {
			//display only item from current tab
			jQuery('.indeed-item-wrapp').each(function(){ 
				if (current_tab && current_tab!=jQuery(this).attr('data-category')){
					return;//skip this item
				} else {
					jQuery(this).css('display', 'block');
				}		
			});
		}	
	} else {		
		v = v.toLowerCase();
		jQuery('.indeed-item-wrapp').each(function(){ //loop through all items
			jQuery(this).css('display', 'none');
			
			if (current_tab && current_tab!=jQuery(this).attr('data-category')){
				return;//skip this item
			}
			
			//selection by keyword
			var item_values = jQuery(this).attr('data-keywords').split(',');
			for (i=0; i<item_values.length; i++){
				var compare_str = item_values[i].toLowerCase();
				if (compare_str.indexOf(v)!=-1){
					jQuery(this).fadeIn(200);
					break;
				}
			}
			
			//selection by description
			var description = jQuery(".indeed-addon-short-description", this).html();
			description = description.toLowerCase();
			if (description.indexOf(v)!=-1){
				jQuery(this).fadeIn(200);
			}
		});	
	}
}

function ihc_select_items_by_cat(t, s){
	jQuery('.indeed-addons-categories-list li').each(function(){
		jQuery(this).attr('class', '');
	});
	jQuery(s).addClass('selected');
	
	jQuery('.indeed-item-wrapp').each(function(){
		var cat = jQuery(this).attr('data-category');
		if (cat==t){
			jQuery(this).fadeIn(200);
		} else {
			jQuery(this).css('display', 'none');
		}
	});
	
	//description
	jQuery('.indeed-description').css('display', 'none');
	jQuery('#indeed_'+t+'_description').css('display', 'block');
	
	//set data-current-tag
	jQuery('.indeed-addons-categories-list').attr('data-current-tag', t);
	
}
