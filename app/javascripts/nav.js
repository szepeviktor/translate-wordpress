jQuery(document).ready(function ($) {
	$('#update-nav-menu').bind('click', function (e) {
		if (e.target && e.target.className && -1 != e.target.className.indexOf('item-edit')) {

			$("input[value='#weglot_switcher'][type=text]").parents('.menu-item-settings').each(function () {
				const id = $(this).attr('id').substring(19);
				$(this).children('p:not( .field-move )').remove(); // remove default fields we don't need

				$(this).append($('<input>').attr({
					type: 'hidden',
					id: 'edit-menu-item-title-' + item,
					name: 'menu-item-title[' + item + ']',
					value: weglot_data.title
				}));

				$(this).append($("<input>").attr({
					type: "hidden",
					id: "edit-menu-item-url-" + item,
					name: "menu-item-url[" + item + "]",
					value: "#weglot_switcher"
				}));

			});
		}
	});
});
