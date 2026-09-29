// Mobile bottom-sheet filter toggle (mirrors mockup-v2/app.js .sheet/#shade behavior).
(function () {
	var sheet = document.querySelector('.sheet');
	var openBtn = document.getElementById('openFilters');
	var closeBtn = document.getElementById('closeFilters');
	if (!sheet || !openBtn) return;

	var shade = document.getElementById('shade');
	if (!shade) {
		shade = document.createElement('div');
		shade.id = 'shade';
		shade.hidden = true;
		document.body.appendChild(shade);
	}

	function openSheet(on) {
		sheet.classList.toggle('open', on);
		shade.hidden = !on;
		document.body.style.overflow = on ? 'hidden' : '';
	}

	openBtn.addEventListener('click', function () { openSheet(true); });
	if (closeBtn) closeBtn.addEventListener('click', function () { openSheet(false); });
	shade.addEventListener('click', function () { openSheet(false); });
})();
