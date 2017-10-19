;
if (typeof(jQueryIWD)=="undefined"){	
	jQueryIWD = jQuery.noConflict(); 
	$ji = jQuery.noConflict(); 
};

$ji(document).ready(function(){
	$ji('#load-map-data').click(function(){
		var loader = new varienLoader(true);
		$ji('#load-map-data').addClass('disabled').attr('disabled', true);
		Element.show('loading-mask');
		var urlDomain = window.location.href;
		var arr = urlDomain.split("/");
		$ji.post(pathJson,$ji('#edit_form').serialize(), function(response){
			Element.hide('loading-mask');
			$ji('#load-map-data').removeClass('disabled').attr('disabled', false);
			
			$ji('#page_latitude').val(response.lat);
			$ji('#page_longitude').val(response.long)
		
		},'json');
		
		return false;
	});
});