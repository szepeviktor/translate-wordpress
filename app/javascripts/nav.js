jQuery(document).ready(function ($) {
	$('#update-nav-menu').bind('click', function (e) {
		if (e.target && e.target.className && -1 != e.target.className.indexOf('item-edit')) {

			$("input[value='#weglot_switcher'][type=text]").parents('.menu-item-settings').each(function () {
				const id = $(this).attr('id').substring(19);
				$(this).children('p:not( .field-move )').remove(); // remove default fields we don't need

				$(this).append($('<input>').attr({
					type: 'hidden',
					id: 'edit-menu-item-title-' + id,
					name: 'menu-item-title[' + id + ']',
					value: weglot_data.title
				}));

				$(this).append($("<input>").attr({
					type: "hidden",
					id: "edit-menu-item-url-" + id,
					name: "menu-item-url[" + id + "]",
					value: "#weglot_switcher"
				}));

				$(this).append($('<input>').attr({
					type: 'hidden',
					id: 'edit-menu-item-pll-detect-' + id,
					name: 'menu-item-pll-detect[' + id + ']',
					value: 1
				}));


				for (var i = 0; i < weglot_data.list_options.length; i++) {
					const paragraph = $("<p>").attr("class", "description");
					const label = $("<label>")
						.attr("for", `edit-menu-item-${weglot_data.list_options[i].key}-${id}`)
						.text(` ${weglot_data.list_options[i].title}`);

					$(this).prepend(paragraph);
					paragraph.append(label);

					const checkbox = $("<input>").attr({
						type: "checkbox",
						id: `edit-menu-item-${
							weglot_data.list_options[i].key
						}-${id}`,
						name: `menu-item-${
							weglot_data.list_options[i].key
						}[${id}]`,
						value: 1
					});

					label.prepend(checkbox);
				}
			});

			// disallow unchecking both show names and show flags
			$('.menu-item-data-object-id').each(function () {
				// var id = $(this).val();
				// var options = ['names-', 'flags-'];
				// $.each(options, function (i, v) {
				// 	$('#edit-menu-item-show_' + v + id).change(function () {
				// 		if ('checked' != $(this).attr('checked')) {
				// 			$('#edit-menu-item-show_' + options[1 - i] + id).prop('checked', true);
				// 		}
				// 	});
				// });
			});
		}
	});
});
