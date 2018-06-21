const init_admin = function(){
	const $ = jQuery

	const init_select = () => {
		$(".weglot-select").selectize({
			delimiter: '|',
			persist: false,
			maxItems: null,
			valueField: 'code',
			labelField: 'local',
			searchField: ['code', 'english', 'local'],
			sortField: [
				{ field: 'code', direction: 'asc' },
				{ field: 'english', direction: 'asc' }
			],
			plugins: ['remove_button'],
			options: weglot_languages.available,
			render: {
				option: function (item, escape) {
					return '<div class="weglot__choice__language">' + '<span class="weglot__choice__language--local">' + escape(item.local) + "</span>" + '<span class="weglot__choice__language--english">' + escape(item.english) + " [" + escape(item.code) + "]</span>" + "</div>";
				}
			}
		});
	}

	const init_exclude_url = () => {
		const template_add_exclude_url = document.querySelector("#tpl-exclusion-url");
		const template_add_exclude_block = document.querySelector("#tpl-exclusion-block");
		const parent_exclude_url_append = document.querySelector("#container-exclude_urls");
		const parent_exclude_block_append = document.querySelector("#container-exclude_blocks");

		function removeLine(e) {
			e.preventDefault()
			this.parentNode.remove()
		}

		if (document.querySelector("#js-add-exclude-url")){

			document
				.querySelector("#js-add-exclude-url")
				.addEventListener("click", (e) => {
					e.preventDefault()
					parent_exclude_url_append.insertAdjacentHTML("beforeend", template_add_exclude_url.innerHTML);
					document
						.querySelector(
							"#container-exclude_urls .item-exclude:last-child .js-btn-remove"
						)
						.addEventListener("click", removeLine);
				});

		}


		if (document.querySelector("#js-add-exclude-block")) {
			document
			.querySelector("#js-add-exclude-block")
			.addEventListener("click", (e) => {
				e.preventDefault()
				parent_exclude_block_append.insertAdjacentHTML("beforeend", template_add_exclude_block.innerHTML);
				document
					.querySelector(
						"#container-exclude_blocks .item-exclude:last-child .js-btn-remove-exclude"
					)
					.addEventListener("click", removeLine);
			});
		}

		const remove_urls = document
			.querySelectorAll(".js-btn-remove")

		remove_urls.forEach((el) => {
			el.addEventListener("click", removeLine);
		})


	}

	document.addEventListener('DOMContentLoaded', () => {
		init_select();
		init_exclude_url();
	})
}

export default init_admin;

