const init_admin_button_preview = function () {
	const $ = jQuery

	const execute = () => {
		// Init old type flags
		let old_type_flags = $("#type_flags").val()

		let destination_languages = []
		destination_languages.push($(".weglot-preview label").data("code-language"));
		$(".weglot-preview li").each((key, itm) => {
			destination_languages.push($(itm).data("code-language"));
		})

		const weglot_desination_languages = weglot_languages.available.filter(itm => {
			return destination_languages.indexOf(itm.code) >= 0;
		})

		$("#weglot-css-inline").text(weglot_css.inline);

		// Change dropdown
		$("#is_dropdown").on("change", function(){
			$(".weglot-preview").toggleClass("weglot-inline");
		})

		// Change with flags
		$("#with_flags").on("change", function() {
			$(".weglot-preview label, .weglot-preview li").toggleClass("weglot-flags");
		});

		// Change type flags
		$("#type_flags").on("change", function(e) {
			$(".weglot-preview label, .weglot-preview li").removeClass(`flag-${old_type_flags}`);
			const new_type_flags = e.target.value;
			$(".weglot-preview label, .weglot-preview li").addClass(`flag-${new_type_flags}`);
			old_type_flags = new_type_flags;
		});

		const set_fullname_language = () => {
			const label_language = weglot_desination_languages.find(
				(itm) => itm.code === $(".weglot-preview label").data("code-language")
			);

			$(".weglot-preview label").text(label_language.local);
				$(".weglot-preview li").each((key, itm) => {
				const language = weglot_desination_languages.find(
					(lang) => lang.code === $(itm).data("code-language")
				);

				$(itm).text(language.local);
			})
		}

		// Change with name
		$("#with_name").on("change", function(e) {
			if(e.target.checked){
				set_fullname_language()
			}
			else{
				$(".weglot-preview label").text("");
				$(".weglot-preview li").each((key, itm) => {
					$(itm).text("");
				});
			}
		});



		$("#is_fullname").on("change", function(e){
			if (e.target.checked) {
				set_fullname_language();

			}
			else {
				const label_language = weglot_desination_languages.find(itm => itm.code === $(".weglot-preview label").data("code-language"));

				$(".weglot-preview label").text(label_language.code.toUpperCase());
				$(".weglot-preview li").each((key, itm) => {
					const language = weglot_desination_languages.find(lang => lang.code === $(itm).data("code-language"));

					$(itm).text(language.code.toUpperCase());
				});
			}
		});

		$("#override_css").on("keyup", function(e) {
			$("#weglot-css-inline").text(e.target.value);
		})
	}

	document.addEventListener('DOMContentLoaded', () => {
		execute();
	})
}

export default init_admin_button_preview;

