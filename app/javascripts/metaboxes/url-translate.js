
const init_url_translate = () => {
	const $ = jQuery;

	const execute = () => {

		const edit_weglot_post_name = function(e) {
			const code = $(this).data('lang')

			$(`#text-edit-${code}`).text( $(`#lang-${code}`).val() );

			$(`#lang-${code}`).hide();
			$(this).hide()
			$(`.button-weglot-lang[data-lang=${code}]`).show()
		}

		$(".button-weglot-lang").each((key, itm) => {
			$(itm).on('click', function (e) {
				e.preventDefault()

				const code = $(this).data('lang')
				const text = $(`#text-edit-${code}`).text();
				$(`#text-edit-${code}`).text(' ');
				$(`#lang-${code}`).val(text).show();
				$(this).hide()
				$(`.button-weglot-lang-submit[data-lang=${code}]`)
					.show()
					.on("click", edit_weglot_post_name);
			})
		})
	};

	document.addEventListener("DOMContentLoaded", () => {
		execute();
	});

}

export default init_url_translate
