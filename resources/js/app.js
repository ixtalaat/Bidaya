import Toastify from 'toastify-js';
import Swal from 'sweetalert2';

window.Swal = Swal;

const isRtl = document.documentElement.dir === 'rtl';

function showToast(message, type = 'success') {
    Toastify({
        text: message,
        duration: 4000,
        close: true,
        gravity: 'top',
        position: isRtl ? 'left' : 'right',
        stopOnFocus: true,
        style: {
            background: type === 'success' ? '#16a34a' : '#dc2626',
            fontFamily: 'inherit',
        },
    }).showToast();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dropdown-toggle]').forEach((button) => {
        const menu = document.getElementById(button.dataset.dropdownToggle);

        if (!menu || button.dataset.dropdownBound) {
            return;
        }

        button.dataset.dropdownBound = 'true';

        const close = () => {
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        };

        button.addEventListener('click', (event) => {
            event.stopPropagation();

            const willOpen = menu.classList.contains('hidden');

            document.querySelectorAll('[data-dropdown-menu]').forEach((other) => other.classList.add('hidden'));
            document.querySelectorAll('[data-dropdown-toggle]').forEach((other) => other.setAttribute('aria-expanded', 'false'));

            if (willOpen) {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
            }
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target)) {
                close();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });
    });

    document.querySelectorAll('[data-mobile-toggle]').forEach((button) => {
        const panel = document.getElementById(button.dataset.mobileToggle);

        if (!panel || button.dataset.mobileBound) {
            return;
        }

        button.dataset.mobileBound = 'true';

        button.addEventListener('click', () => {
            const willOpen = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            button.setAttribute('aria-expanded', String(willOpen));
        });
    });
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealTargets = document.querySelectorAll('.reveal');

    if (reduceMotion || revealTargets.length === 0 || !('IntersectionObserver' in window)) {
        revealTargets.forEach((element) => element.classList.add('is-visible'));
    } else {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationDelay = `${Math.min(entry.target.dataset.stagger || index * 60, 480)}ms`;
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.15 }
        );

        revealTargets.forEach((element) => observer.observe(element));
    }

    const flash = document.getElementById('flash-data');

    if (flash) {
        if (flash.dataset.success) {
            showToast(flash.dataset.success, 'success');
        }

        try {
            JSON.parse(flash.dataset.errors || '[]').forEach((message) => showToast(message, 'error'));
        } catch (error) {
            // Ignore malformed flash payloads.
        }

        document.querySelectorAll('form[data-confirm]').forEach((form) => {
            if (form.dataset.confirmBound) {
                return;
            }

            form.dataset.confirmBound = 'true';

            form.addEventListener('submit', (event) => {
                event.preventDefault();

                Swal.fire({
                    text: form.dataset.confirm,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: flash.dataset.confirmButton || 'Confirm',
                    cancelButtonText: flash.dataset.cancelButton || 'Cancel',
                    confirmButtonColor: '#b91c1c',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }
});
