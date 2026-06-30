import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
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
