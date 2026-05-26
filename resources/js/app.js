import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

try {
	if (!window.alpineInitialized) {
		window.alpineInitialized = true;
		Alpine.start();
	}
} catch (e) {
	console.warn('Alpine start failed:', e);
}

/* ── Reveal-on-scroll logic ── */
function initReveal() {
	const targets = document.querySelectorAll('[data-reveal]:not(.is-visible)');
	if (!targets.length) return;

	const checkReveal = () => {
		// Trigger when the element's top is within 95% of the viewport height
		const triggerBottom = window.innerHeight * 0.95; 
		
		targets.forEach((el) => {
			if (el.classList.contains('is-visible')) return;
			
			const rect = el.getBoundingClientRect();
			if (rect.top < triggerBottom) {
				el.classList.add('is-visible');
			}
		});
	};

	// Check immediately
	checkReveal();

	// Check on scroll and resize
	window.addEventListener('scroll', checkReveal, { passive: true });
	window.addEventListener('resize', checkReveal, { passive: true });

	// Fallbacks for layout shifts
	setTimeout(checkReveal, 150);
	setTimeout(checkReveal, 500);
}

/* ── Table row stagger animation ── */
function initRowStagger() {
	document.querySelectorAll('tbody.divide-y').forEach((tbody) => {
		const rows = tbody.querySelectorAll('tr');
		rows.forEach((row, i) => {
			row.classList.add('row-enter');
			row.style.animationDelay = `${i * 40}ms`;
		});
	});
}

const runInit = () => {
	// Defer slightly to ensure browser has completed initial layout/paint
	setTimeout(() => {
		initReveal();
		initRowStagger();
	}, 50);
};

/* Kick off on first load */
if (document.readyState === 'loading') {
	window.addEventListener('DOMContentLoaded', runInit);
} else {
	runInit();
}

/* Re-init after Livewire SPA navigation (wire:navigate) */
document.addEventListener('livewire:navigated', runInit);

/* Re-init after Livewire re-renders a component */
document.addEventListener('livewire:update', () => {
	/* Small delay so the DOM has settled after morphing */
	requestAnimationFrame(() => {
		initReveal();
	});
});
