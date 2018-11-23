const init_private_mode = function () {

	const $ = jQuery

	const execute = () => {
		document.querySelector("#private_mode").addEventListener('change', function(e) {

			document.querySelectorAll(".private-mode-lang--input").forEach((itm) => {
				itm.checked = e.target.checked;
			})
		})
	}

	document.addEventListener('DOMContentLoaded', () => {
		if (document.querySelector('#private_mode').length != 0){
			execute();
		}
	})
}

export default init_private_mode;

