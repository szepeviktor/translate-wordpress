const init_admin_select = function(){
	const $ = jQuery
	document.addEventListener('DOMContentLoaded', () => {
		$(".weglot-select").selectize({
			plugins: ['remove_button', 'drag_drop']
		})
	})
}

export default init_admin_select

