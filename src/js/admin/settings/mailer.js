(function($){
	$(document).on('click','[data-ajax-message-id]',function(e){

		e.preventDefault()

		var self = this,
			param = $.extend( {
			method:'POST',
			success:function(reponse) {
				console.log(self,reponse,reponse.html)
				$(self).replaceWith( reponse.html );
			}
		}, thebot_mailer.ajax );
		param.data = $.extend({
			message_id:$(this).data('ajax-message-id'),
		},param.data)
		console.log(param);
		$.ajax(param)
	});
})(jQuery);
