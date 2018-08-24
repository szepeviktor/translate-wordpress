const init_admin_select = function(){

    const $ = jQuery
    const generate_destination_language = () => {
        return weglot_languages.available.filter(itm => {
            return itm.code !== weglot_languages.original
        });
    }

    let destination_selectize

    const load_destination_selectize = () => {
        destination_selectize = $(".weglot-select-destination").selectize(
            {
                delimiter: "|",
                persist: false,
                valueField: "code",
                labelField: "local",
                searchField: ["code", "english", "local"],
                sortField: [
                    { field: "code", direction: "asc" },
                    { field: "english", direction: "asc" }
                ],
                maxItems: weglot_languages.limit,
                plugins: ["remove_button"],
                options: generate_destination_language(),
                render: {
                    option: function (item, escape) {
                        return (
                            '<div class="weglot__choice__language">' +
                            '<span class="weglot__choice__language--local">' +
                            escape(item.local) +
                            "</span>" +
                            '<span class="weglot__choice__language--english">' +
                            escape(item.english) +
                            " [" +
                            escape(item.code) +
                            "]</span>" +
                            "</div>"
                        );
                    }
                }
            }
        );
    }

    const execute = () => {

        load_destination_selectize();

        window.addEventListener("weglotCheckApi", (data) => {

            let limit = 1000
            const plan = data.detail.answer.plan

            if (
                plan <= 0 ||
                weglot_languages.plans.starter_free.ids.indexOf(plan) >= 0
            ) {
                limit = weglot_languages.plans.starter_free.limit_language;
            } else if( weglot_languages.plans.business.ids.indexOf(plan) >= 0 ) {
                limit = weglot_languages.plans.business.limit_language;
            }

            destination_selectize[0].selectize.settings.maxItems = limit
        });

    }

    document.addEventListener('DOMContentLoaded', () => {
        execute();
    })
}

export default init_admin_select;