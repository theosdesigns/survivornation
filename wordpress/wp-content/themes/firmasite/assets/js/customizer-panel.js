jQuery(document).ready(function() {
	jQuery('input[value=content-sidebar]').after('<span class="content-sidebar"></span>');
	jQuery('input[value=sidebar-content]').after('<span class="sidebar-content"></span>');
	jQuery('input[value=only-content]').after('<span class="only-content"></span>');
	jQuery('input[value=only-content-long]').after('<span class="only-content-long"></span>');

	jQuery('input[value=loop-list]').after('<span class="loop-list"></span>');
	jQuery('input[value=loop-tile]').after('<span class="loop-tile"></span>');
	jQuery('input[value=loop-excerpt]').after('<span class="loop-excerpt"></span>');
	
	
	
		// Image Options
	jQuery('.of-radio-img-img').click(firmasite_change_style);
	jQuery('.of-radio-img-label').click(firmasite_change_style);	

	function firmasite_change_style() {
		jQuery(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		jQuery(this).parent().parent().find('.of-radio-img-radio').removeAttr('checked');
		jQuery(this).parent().parent().find('.of-radio-img-label').removeClass('selected');
		jQuery(this).parent().parent().find('.of-radio-img-label i').remove();
		jQuery(this).parent().find('.of-radio-img-img').addClass('of-radio-img-selected');		
		jQuery(this).parent().find('input.of-radio-img-radio').click();
		jQuery(this).parent().find('.of-radio-img-label').addClass('selected');
		jQuery(this).parent().find('.of-radio-img-label').prepend('<i class="icon-ok"></i> ');

	}
	jQuery('.of-radio-img-label').show();
	jQuery('.of-radio-img-img').show();
	jQuery('.of-radio-img-radio').hide();

});


