import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const sidebarAccordion = document.getElementById('sidebarAccordion');
    if (sidebarAccordion) {
        sidebarAccordion.querySelectorAll('.accordion-button').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-bs-target');
                if (!targetId) return;
                const target = document.querySelector(targetId);
                if (!target) return;
                const isOpen = target.classList.contains('show');
                sidebarAccordion.querySelectorAll('.accordion-collapse.show').forEach(function (el) {
                    const instance = bootstrap.Collapse.getInstance(el);
                    if (instance) instance.hide();
                });
                if (!isOpen) {
                    const instance = bootstrap.Collapse.getOrCreateInstance(target);
                    instance.show();
                }
            });
        });
    }

    const successToast = document.getElementById('successToast');
    if (successToast) {
        const toast = new bootstrap.Toast(successToast, { delay: 4000 });
        toast.show();
    }

    const errorToast = document.getElementById('errorToast');
    if (errorToast) {
        const toast = new bootstrap.Toast(errorToast, { delay: 6000 });
        toast.show();
    }
});
