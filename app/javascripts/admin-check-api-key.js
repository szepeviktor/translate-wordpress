const init_admin_button_preview = function () {
	const $ = jQuery

	const execute = () => {

		$("#api_key").blur(function() {
			var key = $(this).val();
			$.getJSON(
				"https://weglot.com/api/user-info?api_key=" + key,
				function(data) {
					$(".weglot-keyres").remove();
					$("#api_key").after(
						'<span class="weglot-keyres weglot-okkey"></span>'
					);
					$(".weglot-widget-option-form input[type=submit]").prop(
						"disabled",
						false
					);
				}
			).fail(function() {
				$(".weglot-keyres").remove();
				$("#api_key").after('<span class="weglot-keyres weglot-nokkey"></span>');
				$("#wrap-weglot #submit").prop("disabled", true);
			});
		});
	}

	document.addEventListener('DOMContentLoaded', () => {
		execute();
	})
}

export default init_admin_button_preview;

