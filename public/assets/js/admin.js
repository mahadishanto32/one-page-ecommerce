/* ==========================================================================
   ADMIN PANEL — SHARED JS
   Custom modal (confirm / delete / apply), sidebar behavior, table + editor
   bootstrapping. Everything is namespaced under `AdminModal` / `AdminToast`
   so it can be reused from any Blade view.
   ========================================================================== */

(function (window, document) {
    'use strict';

    /* ----------------------------------------------------------------------
       AdminModal — replaces window.confirm() with a themed modal.
       Usage:
           AdminModal.confirm({
               type: 'delete',              // 'confirm' | 'delete' | 'apply'
               title: 'Delete this item?',
               message: 'This action cannot be undone.',
               confirmText: 'Delete',
               cancelText: 'Cancel',
               onConfirm: function () { ... }
           });
    ---------------------------------------------------------------------- */

    var AdminModal = (function () {
        var overlay = null;

        var ICONS = {
            confirm: 'fa-solid fa-circle-question',
            delete: 'fa-solid fa-trash-can',
            apply: 'fa-solid fa-circle-check'
        };

        var DEFAULT_TITLES = {
            confirm: 'Are you sure?',
            delete: 'Delete this item?',
            apply: 'Apply these changes?'
        };

        var DEFAULT_MESSAGES = {
            confirm: 'Please confirm you want to proceed with this action.',
            delete: 'This action cannot be undone. The item will be permanently removed.',
            apply: 'This will apply your changes immediately.'
        };

        var DEFAULT_CONFIRM_CLASS = {
            confirm: 'btn-admin-primary',
            delete: 'btn-admin-danger',
            apply: 'btn-admin-primary'
        };

        function build() {
            if (overlay) return overlay;

            overlay = document.createElement('div');
            overlay.className = 'admin-modal-overlay';
            overlay.innerHTML =
                '<div class="admin-modal" role="dialog" aria-modal="true">' +
                    '<div class="admin-modal-icon"><i></i></div>' +
                    '<h4 class="admin-modal-title"></h4>' +
                    '<p class="admin-modal-message"></p>' +
                    '<div class="admin-modal-actions">' +
                        '<button type="button" class="btn-admin btn-admin-outline admin-modal-cancel"></button>' +
                        '<button type="button" class="btn-admin admin-modal-confirm"></button>' +
                    '</div>' +
                '</div>';

            document.body.appendChild(overlay);

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) close();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && overlay.classList.contains('show')) close();
            });

            return overlay;
        }

        function close() {
            if (!overlay) return;
            overlay.classList.remove('show');
        }

        function open(options) {
            options = options || {};
            var type = options.type || 'confirm';

            build();

            var iconWrap = overlay.querySelector('.admin-modal-icon');
            var iconEl = overlay.querySelector('.admin-modal-icon i');
            var titleEl = overlay.querySelector('.admin-modal-title');
            var messageEl = overlay.querySelector('.admin-modal-message');
            var cancelBtn = overlay.querySelector('.admin-modal-cancel');
            var confirmBtn = overlay.querySelector('.admin-modal-confirm');

            iconWrap.className = 'admin-modal-icon type-' + type;
            iconEl.className = options.icon || ICONS[type] || ICONS.confirm;
            titleEl.textContent = options.title || DEFAULT_TITLES[type];
            messageEl.textContent = options.message || DEFAULT_MESSAGES[type];

            cancelBtn.textContent = options.cancelText || 'Cancel';
            confirmBtn.textContent = options.confirmText ||
                (type === 'delete' ? 'Delete' : type === 'apply' ? 'Apply' : 'Confirm');

            confirmBtn.className = 'btn-admin admin-modal-confirm ' +
                (options.confirmClass || DEFAULT_CONFIRM_CLASS[type] || 'btn-admin-primary');

            // Replace buttons to clear old listeners
            var newCancel = cancelBtn.cloneNode(true);
            var newConfirm = confirmBtn.cloneNode(true);
            cancelBtn.parentNode.replaceChild(newCancel, cancelBtn);
            confirmBtn.parentNode.replaceChild(newConfirm, confirmBtn);

            newCancel.addEventListener('click', function () {
                close();
                if (typeof options.onCancel === 'function') options.onCancel();
            });

            newConfirm.addEventListener('click', function () {
                close();
                if (typeof options.onConfirm === 'function') options.onConfirm();
            });

            requestAnimationFrame(function () {
                overlay.classList.add('show');
            });
        }

        return {
            confirm: function (opts) { open(Object.assign({ type: 'confirm' }, opts)); },
            delete: function (opts) { open(Object.assign({ type: 'delete' }, opts)); },
            apply: function (opts) { open(Object.assign({ type: 'apply' }, opts)); },
            close: close
        };
    })();

    /* ----------------------------------------------------------------------
       AdminToast — small notification popups
    ---------------------------------------------------------------------- */

    var AdminToast = (function () {
        var container = null;

        function getContainer() {
            if (!container) {
                container = document.createElement('div');
                container.className = 'admin-toast-container';
                document.body.appendChild(container);
            }
            return container;
        }

        function show(message, type, duration) {
            type = type || 'info';
            duration = duration || 3500;

            var icons = {
                success: 'fa-solid fa-circle-check',
                error: 'fa-solid fa-circle-xmark',
                info: 'fa-solid fa-circle-info'
            };

            var toast = document.createElement('div');
            toast.className = 'admin-toast ' + type;
            toast.innerHTML = '<i class="' + (icons[type] || icons.info) + '"></i><span>' + message + '</span>';

            getContainer().appendChild(toast);
            requestAnimationFrame(function () { toast.classList.add('show'); });

            setTimeout(function () {
                toast.classList.remove('show');
                setTimeout(function () { toast.remove(); }, 250);
            }, duration);
        }

        return {
            success: function (msg, d) { show(msg, 'success', d); },
            error: function (msg, d) { show(msg, 'error', d); },
            info: function (msg, d) { show(msg, 'info', d); }
        };
    })();

    window.AdminModal = AdminModal;
    window.AdminToast = AdminToast;

    /* ----------------------------------------------------------------------
       DOM READY — sidebar, delete forms, datatables, ckeditor
    ---------------------------------------------------------------------- */

    document.addEventListener('DOMContentLoaded', function () {

        /* --- Sidebar submenu toggle (for .has-submenu / .dropdown-toggle) --- */
        document.querySelectorAll('.sidebar-menu .dropdown-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                var parent = toggle.closest('.has-submenu');
                if (!parent) return;
                parent.classList.toggle('open');
            });
        });

        /* --- Mobile sidebar toggle, if a [data-sidebar-toggle] button exists --- */
        var sidebarToggle = document.querySelector('[data-sidebar-toggle]');
        var sidebarEl = document.querySelector('.sidebar');
        if (sidebarToggle && sidebarEl) {
            sidebarToggle.addEventListener('click', function () {
                sidebarEl.classList.toggle('mobile-open');
            });
        }

        /* --- Generic delete forms: <form data-confirm-delete> --- */
        document.querySelectorAll('form[data-confirm-delete]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                AdminModal.delete({
                    title: form.getAttribute('data-title') || undefined,
                    message: form.getAttribute('data-message') || undefined,
                    onConfirm: function () { form.submit(); }
                });
            });
        });

        /* --- Generic confirm buttons/links: [data-confirm] --- */
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var href = el.getAttribute('href');
                var targetForm = el.closest('form');

                AdminModal.confirm({
                    type: el.getAttribute('data-confirm-type') || 'confirm',
                    title: el.getAttribute('data-title') || undefined,
                    message: el.getAttribute('data-message') || undefined,
                    confirmText: el.getAttribute('data-confirm-text') || undefined,
                    onConfirm: function () {
                        if (targetForm) {
                            targetForm.submit();
                        } else if (href) {
                            window.location.href = href;
                        }
                    }
                });
            });
        });

        /* --- DataTables init: any <table class="data-table"> --- */
        if (window.jQuery && jQuery.fn.DataTable) {
            jQuery('.data-table').each(function () {
                jQuery(this).DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50, 100],
                    language: {
                        search: '',
                        searchPlaceholder: 'Search...'
                    }
                });
            });
        }

        /* --- CKEditor 5 init: any <textarea class="ckeditor"> --- */
        if (window.ClassicEditor) {
            document.querySelectorAll('textarea.ckeditor').forEach(function (el) {
                ClassicEditor.create(el).catch(function (err) {
                    console.error('CKEditor failed to initialize:', err);
                });
            });
        }

        /* --- Auto-dismiss flash / alert banners with [data-auto-dismiss] --- */
        document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.3s ease';
                el.style.opacity = '0';
                setTimeout(function () { el.remove(); }, 300);
            }, 4000);
        });
    });

})(window, document);