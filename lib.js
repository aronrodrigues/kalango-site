function load() {
	setTimeout("hideElement('message')", 3000);
}
function hideElement(id) {
	var elem = document.getElementById(id);
	if (!elem)
		return;
	elem.style.display = "none";
}
