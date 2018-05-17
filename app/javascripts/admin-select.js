const init_admin_select = function(){
	const $ = jQuery
	document.addEventListener('DOMContentLoaded', () => {
		$(".weglot-select").select2();

		$(".weglot-select-exclusion").select2({
			tags: true,
		});
	})
}

export default init_admin_select

