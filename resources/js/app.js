import './bootstrap';

/* 
 * AlpineJS is automatically injected and initialized by Livewire 3. 
 * Do NOT manually import or start Alpine here, as it will cause conflicts 
 * (breaking wire:click, wire:model, etc. especially on wire:navigate).
 */


/* ── Reveal-on-scroll logic (IntersectionObserver) ── */
let revealObserver = null;

function initReveal() {
	// Create observer once, reuse it
	if (!revealObserver) {
		revealObserver = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						revealObserver.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.05, rootMargin: '0px 0px 50px 0px' }
		);
	}

	// Observe all un-revealed elements
	document.querySelectorAll('[data-reveal]:not(.is-visible)').forEach((el) => {
		revealObserver.observe(el);
	});
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
