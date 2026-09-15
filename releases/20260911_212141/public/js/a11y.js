/**
 * a11y.js - Utilidades de accesibilidad
 *
 * Funcionalidades:
 * - Anuncios ARIA live region para lectores de pantalla
 * - Focus management para modales
 * - Navegación por teclado mejorada
 * - Skip links
 */

(function() {
    'use strict';

    // ==== Live Region (anuncios para screen readers) ====
    const A11y = {
        liveRegion: null,

        init() {
            this.createLiveRegion();
            this.setupFocusTrap();
            this.setupSkipLinks();
            this.setupKeyboardNav();
            console.log('[a11y] Inicializado');
        },

        createLiveRegion() {
            if (document.getElementById('a11y-live-region')) return;

            const region = document.createElement('div');
            region.id = 'a11y-live-region';
            region.setAttribute('role', 'status');
            region.setAttribute('aria-live', 'polite');
            region.setAttribute('aria-atomic', 'true');
            region.className = 'visually-hidden';
            region.style.cssText = 'position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;';
            document.body.appendChild(region);
            this.liveRegion = region;
        },

        /**
         * Anunciar mensaje a screen readers
         * @param {string} message
         * @param {string} priority - 'polite' o 'assertive'
         */
        announce(message, priority = 'polite') {
            if (!this.liveRegion) this.createLiveRegion();
            this.liveRegion.setAttribute('aria-live', priority);
            // Limpiar primero para que se re-anuncie
            this.liveRegion.textContent = '';
            setTimeout(() => {
                this.liveRegion.textContent = message;
            }, 100);
        },

        /**
         * Alert (assertive) - interrumpe al usuario
         */
        alert(message) {
            this.announce(message, 'assertive');
        },

        // ==== Focus Management ====
        setupFocusTrap() {
            // Aplicar a cualquier modal que se abra
            document.addEventListener('shown.bs.modal', (e) => {
                this.trapFocus(e.target);
                this.announce('Diálogo abierto');
            });

            document.addEventListener('hidden.bs.modal', (e) => {
                this.releaseFocusTrap();
            });
        },

        trapFocus(modalElement) {
            if (!modalElement) return;
            const focusable = modalElement.querySelectorAll(
                'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
            );
            if (focusable.length === 0) return;

            const firstFocusable = focusable[0];
            const lastFocusable = focusable[focusable.length - 1];

            // Guardar elemento que tenía focus antes
            this._previousFocus = document.activeElement;

            // Focus al primer elemento focuseable
            setTimeout(() => firstFocusable.focus(), 50);

            // Trap con Tab
            const handler = (e) => {
                if (e.key !== 'Tab') return;

                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {
                        lastFocusable.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        firstFocusable.focus();
                        e.preventDefault();
                    }
                }
            };

            modalElement.addEventListener('keydown', handler);
            this._focusTrapHandler = handler;
            this._trappedModal = modalElement;
        },

        releaseFocusTrap() {
            if (this._trappedModal && this._focusTrapHandler) {
                this._trappedModal.removeEventListener('keydown', this._focusTrapHandler);
            }
            if (this._previousFocus) {
                this._previousFocus.focus();
                this._previousFocus = null;
            }
        },

        // ==== Skip Links ====
        setupSkipLinks() {
            const skipLink = document.querySelector('.skip-to-main');
            if (skipLink) {
                skipLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    const target = document.querySelector(skipLink.getAttribute('href'));
                    if (target) {
                        target.setAttribute('tabindex', '-1');
                        target.focus();
                        target.scrollIntoView();
                    }
                });
            }
        },

        // ==== Navegación por Teclado ====
        setupKeyboardNav() {
            document.addEventListener('keydown', (e) => {
                // Escape para cerrar modales/dropdowns abiertos
                if (e.key === 'Escape') {
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        const closeBtn = openModal.querySelector('[data-bs-dismiss="modal"]');
                        if (closeBtn) closeBtn.click();
                    }
                }

                // Ctrl+Enter para submit forms
                if (e.ctrlKey && e.key === 'Enter') {
                    const form = e.target.closest('form');
                    if (form) {
                        const submitBtn = form.querySelector('[type="submit"]:not([disabled])');
                        if (submitBtn) {
                            e.preventDefault();
                            submitBtn.click();
                        }
                    }
                }
            });
        },

        /**
         * Anunciar cambio de estado de un botón (loading/success)
         */
        announceButtonState(button, newLabel) {
            if (!button) return;
            const originalLabel = button.textContent.trim();
            button.setAttribute('aria-label', newLabel);
            button.setAttribute('aria-busy', 'true');
            this.announce(newLabel);

            return {
                restore: () => {
                    button.removeAttribute('aria-label');
                    button.removeAttribute('aria-busy');
                    this.announce(originalLabel);
                }
            };
        },

        /**
         * Confirmar acción destructiva con anuncio
         */
        confirmDestructive(message) {
            this.alert('Atención: ' + message);
            return confirm(message);
        }
    };

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => A11y.init());
    } else {
        A11y.init();
    }

    // Exponer globalmente
    window.A11y = A11y;
})();
