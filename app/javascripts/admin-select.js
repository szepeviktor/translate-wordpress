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
		const parent_append = document.querySelector("#container-exclude_url");

		function removeLineUrl(e) {
			e.preventDefault();
			this.parentNode.remove()
		}

		document
			.querySelector("#js-add-exclude-url")
			.addEventListener("click", (e) => {
				e.preventDefault()
				parent_append.insertAdjacentHTML("beforeend", template_add_exclude_url.innerHTML);
				document
					.querySelector(
						".item-exclude-url:last-child .js-btn-remove-exclude-url"
					)
					.addEventListener("click", removeLineUrl);
			});

		const remove_urls = document
			.querySelectorAll(".js-btn-remove-exclude-url")

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

