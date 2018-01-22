jQuery(document).ready(function(){
	jQuery(".filter-options-title").addClass('sign');		
	jQuery(".filter-options-title").click(function(){
		jQuery(".filter-options-title").removeClass('togle-sidenav');
		jQuery(this).toggleClass('sign');			
		jQuery(this).addClass('togle-sidenav');		
		jQuery('.togle-sidenav').next().slideToggle();
		
	});
	
});
 jQuery(document).ready(function() {
	var showChar = 200;
/* 	var ellipsestext = "..."; */
	var moretext = "More";
	var lesstext = "Less";
	jQuery('.category-description p').each(function() {
		var content = jQuery(this).html();

	       if(content.length > showChar) {
 
            var c = content.substr(0, showChar);
            var h = content.substr(showChar-0, content.length - showChar);
 
            var html = c + '<span class="moreellipses"></span><span class="morecontent"><span style="display:none;">' + h + '</span>  <a href="" class="morelink">' + moretext + '</a></span>';
 
            jQuery(this).html(html);
        }
 
    })

	jQuery(".morelink").click(function(){
		if(jQuery(this).hasClass("less")) {
			jQuery(this).removeClass("less");
			jQuery(this).html(moretext);
		} else {
			jQuery(this).addClass("less");
			jQuery(this).html(lesstext);
		}
		jQuery(this).parent().prev().toggle();
		jQuery(this).prev().toggle();
		return false;
	});
}); 