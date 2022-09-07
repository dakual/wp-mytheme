jQuery.noConflict();
(function( $ ) {
	  
	$('html').click(function(event) {
		var e = $(event.target);
		if ( e.parents('.dropdown-content').length < 1 && e.next('.dropdown-content').length < 1 ) {
			$('.dropdown-content').removeClass('mcart-show');
		}
	}); 
	
	$(document).on('click','.btn-mcart',function(event) {
		$( '.dropdown-content' ).toggleClass( "mcart-show" );
	});

	
	//jQuery(document).ready(function($){
		$(document).on('click','button.plus, button.minus',function () {
			//$('div.quantity').on( 'click', 'button.plus, button.minus', function() {
			// Get current quantity values
			var qty = $( this ).closest( 'div.quantity' ).find( '.qty' ); 
			var val   = parseFloat(qty.val());
			var max = parseFloat(qty.attr( 'max' ));
			var min = parseFloat(qty.attr( 'min' ));
			var step = parseFloat(qty.attr( 'step' ));

			if ( $( this ).is( '.plus' ) ) {
				if ( max && ( max <= val ) ) {
					qty.val( max ).trigger("change");
				} else {
					qty.val( val + step ).trigger("change");
				}
			} else {
			   if ( min && ( min >= val ) ) {
					qty.val( min ).trigger("change");
			   } else if ( val > 1 ) {
					qty.val( val - step ).trigger("change");
			   }
			}
		});
	//});

	$(document).on('click','.myaccount-login-tab > button',function(event) {
		var tab	= $(this).data('tab');	
		
		//$('#customer_login > div').addClass('hidden-tab');
		//$('#customer_login > .' + tab).removeClass('hidden-tab');
		$('#customer_login > div').removeClass('show-tab').addClass('hide-tab');;
		$('#customer_login > .' + tab).removeClass('hide-tab').addClass('show-tab');
		
		$('.myaccount-login-tab button').removeClass('active');
		$(this).addClass('active');
	});


})(jQuery);

