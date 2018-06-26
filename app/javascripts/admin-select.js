const init_admin_select = function(){
	const $ = jQuery

	const execute = () => {
		$(".weglot-select").selectize({
			delimiter: '|',
			persist: false,
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

	document.addEventListener('DOMContentLoaded', () => {
		execute();
	})
}

export default init_admin_select;

