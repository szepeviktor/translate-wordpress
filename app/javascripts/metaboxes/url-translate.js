
const init_url_translate = () => {
	const $ = jQuery;

	const execute = () => {
		$(".button-weglot-lang").each((key, itm) => {
			console.log("itm", itm)
			$(itm).on('click', function (e) {
				e.preventDefault()

				console.log($(this).data('lang'))
			})
		})
	};

	document.addEventListener("DOMContentLoaded", () => {
		execute();
	});

}

export default init_url_translate
