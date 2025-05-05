function showPassword() {
	var passwordBox = document.getElementById('the_password');
	if (passwordBox.type === 'text') {
		passwordBox.type = 'password';
	} else {
		passwordBox.type = 'text';
	}
}

	function changeInterface() {
		var dropdown = document.getElementById("the_selection");
		var selectedValue = dropdown.value;
		var contentDiv = document.getElementById("dbinput");

		if (selectedValue === "dbuser") {
			contentDiv.style.display = "table-row";
		} else if (selectedValue === "regular") {
			contentDiv.style.display = "none";
		}
	}