const init_admin = function(){
	const $ = jQuery

	const init_select2 = () => {
		$(".weglot-select").select2();

		$(".weglot-select-exclusion").select2({
			tags: true,
		});
	}

	const init_exclude_url = () => {
		const template_add_exclude_url = document.querySelector("#tpl-exclusion-url");
		const template_add_exclude_block = document.querySelector("#tpl-exclusion-block");
		const parent_exclude_url_append = document.querySelector("#container-exclude_urls");
		const parent_exclude_block_append = document.querySelector("#container-exclude_blocks");

		function removeLineUrl(e) {
			e.preventDefault()
			this.parentNode.remove()
		}

		document
			.querySelector("#js-add-exclude-url")
			.addEventListener("click", (e) => {
				e.preventDefault()
				parent_exclude_url_append.insertAdjacentHTML("beforeend", template_add_exclude_url.innerHTML);
				document
					.querySelector(
						"#container-exclude_url .item-exclude:last-child .js-btn-remove-exclude"
					)
					.addEventListener("click", removeLineUrl);
			});

		document
			.querySelector("#js-add-exclude-block")
			.addEventListener("click", (e) => {
				e.preventDefault()
				parent_exclude_block_append.insertAdjacentHTML("beforeend", template_add_exclude_block.innerHTML);
				document
					.querySelector(
						"##container-exclude_block .item-exclude:last-child .js-btn-remove-exclude"
					)
					.addEventListener("click", removeLineUrl);
			});

		const remove_urls = document
			.querySelectorAll(".js-btn-remove-exclude")

		remove_urls.forEach((el) => {
			el.addEventListener("click", removeLineUrl);
		})

	}

	document.addEventListener('DOMContentLoaded', () => {
		init_select2();
		init_exclude_url();
	})
}

export default init_admin;

