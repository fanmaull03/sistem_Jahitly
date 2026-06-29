/* ── Dark Mode: apply class before paint to prevent flash ── */
(function () {
	const saved = localStorage.getItem('darkMode');
	if (saved === 'true' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
		document.documentElement.classList.add('dark');
	}
})();

import './bootstrap';

/* ── Dark Mode Alpine Store ── */
document.addEventListener('alpine:init', () => {
	Alpine.store('darkMode', {
		on: document.documentElement.classList.contains('dark'),

		toggle() {
			this.on = !this.on;
			document.documentElement.classList.toggle('dark', this.on);
			localStorage.setItem('darkMode', this.on);
		},

		init() {
			// Sync across tabs
			window.addEventListener('storage', (e) => {
				if (e.key === 'darkMode') {
					this.on = e.newValue === 'true';
					document.documentElement.classList.toggle('dark', this.on);
				}
			});
		},
	});
});

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
document.addEventListener('livewire:navigated', () => {
	runInit();
	
	// Restore dark mode class as Livewire DOM morphing might wipe it from <html>
	const saved = localStorage.getItem('darkMode');
	const isDark = saved === 'true' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
	document.documentElement.classList.toggle('dark', isDark);
	
	if (window.Alpine && window.Alpine.store('darkMode')) {
		window.Alpine.store('darkMode').on = isDark;
	}
});
/* Re-init after Livewire re-renders a component */
document.addEventListener('livewire:update', () => {
	/* Small delay so the DOM has settled after morphing */
	requestAnimationFrame(() => {
		initReveal();
	});
});
