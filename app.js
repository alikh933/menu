(() => {
	const navButtons = Array.from(document.querySelectorAll('.nav-item'));
	const panels = Array.from(document.querySelectorAll('[role="tabpanel"]'));

	function setActive(targetButton) {
		navButtons.forEach(btn => {
			btn.classList.remove('is-active');
			btn.classList.remove('bg-amber-400/10', 'text-amber-400');
			btn.classList.add('bg-transparent', 'text-slate-400');
		});
		panels.forEach(p => {
			p.hidden = true;
			p.classList.remove('is-active');
		});

		targetButton.classList.add('is-active');
		targetButton.classList.remove('bg-transparent', 'text-slate-400');
		targetButton.classList.add('bg-amber-400/10', 'text-amber-400');
		const targetId = targetButton.getAttribute('aria-controls');
		const panel = document.getElementById(targetId);
		if (panel) {
			panel.hidden = false;
			panel.classList.add('is-active');
		}

		navButtons.forEach(btn => btn.setAttribute('aria-selected', String(btn === targetButton)));
	}

	// Wire events
	navButtons.forEach(btn => {
		btn.addEventListener('click', () => setActive(btn));
		btn.addEventListener('keydown', (ev) => {
			const currentIndex = navButtons.indexOf(btn);
			if (ev.key === 'ArrowLeft') {
				ev.preventDefault();
				const prev = navButtons[(currentIndex - 1 + navButtons.length) % navButtons.length];
				prev.focus();
				setActive(prev);
			}
			if (ev.key === 'ArrowRight') {
				ev.preventDefault();
				const next = navButtons[(currentIndex + 1) % navButtons.length];
				next.focus();
				setActive(next);
			}
		});
	});

	// Ensure first tab visible on load
	const initial = document.querySelector('.nav-item.is-active');
	if (initial) setActive(initial);
})();

