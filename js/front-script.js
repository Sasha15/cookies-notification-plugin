(function ($) {
	function showHideBanner() {
		if (localStorage.getItem("popupWasShown") == null) {
			$('#br-cookies-notification-wrapper').css('display','block');
		}else if (localStorage.getItem("popupWasShown") == 1) {
			$('#br-cookies-notification-wrapper').css('display','none');
		}
	}
	showHideBanner();
	console.log(localStorage.getItem("popupWasShown"));
	$('.br-button-container a').click(function(){
		$('#br-cookies-notification-wrapper').hide();
		if($(this).hasClass('br-accept-button')){
			localStorage.setItem("popupWasShown", 1);
		}
	})
})(jQuery);