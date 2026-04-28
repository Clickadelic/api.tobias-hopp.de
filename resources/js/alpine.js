import Alpine from 'alpinejs';

// 👉 Collapse Plugin (für x-collapse Animationen im Accordion)
import collapse from '@alpinejs/collapse';

// 👉 Plugins registrieren (WICHTIG: vor Alpine.start!)
Alpine.plugin(collapse);

// 👉 Optional: Alpine global verfügbar machen (Debugging / DevTools)
window.Alpine = Alpine;

// 👉 Eigene Alpine-Komponenten registrieren
// Wird ausgeführt, sobald Alpine initialisiert wird
document.addEventListener('alpine:init', () => {
	/**
	 * Accordion State
	 * @param {boolean} single - erlaubt nur ein geöffnetes Item gleichzeitig
	 */
	Alpine.data('accordion', (single = false) => ({
		// 👉 Array der aktuell geöffneten Items (über Index gesteuert)
		openItems: [],

		/**
		 * Toggle eines Accordion-Items
		 * @param {number|string} index
		 */
		toggle(index) {
			// 👉 Single Mode: nur ein Item darf offen sein
			if (single) {
				this.openItems = this.openItems[0] === index ? [] : [index];
				return;
			}

			// 👉 Multi Mode: mehrere Items gleichzeitig erlaubt
			if (this.openItems.includes(index)) {
				// schließen
				this.openItems = this.openItems.filter((i) => i !== index);
			} else {
				// öffnen
				this.openItems.push(index);
			}
		},

		/**
		 * Prüft, ob ein Item geöffnet ist
		 * @param {number|string} index
		 * @returns {boolean}
		 */
		isOpen(index) {
			return this.openItems.includes(index);
		},
	}));
});

// 👉 Alpine starten (IMMER zuletzt!)
Alpine.start();
