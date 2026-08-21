/**
 * ASR FORM — Core JavaScript
 * Sidebar, Toast, Modal, Dropdown, AJAX helpers
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initDropdowns();
    initToasts();
    initModals();
    initDeleteForms();
});

/* ═══════════════════════════════════════════
   SIDEBAR
   ═══════════════════════════════════════════ */

function initSidebar() {
    const toggle = document.getElementById('sidebar-toggle');
    const overlay = document.getElementById('sidebar-overlay');
    const body = document.body;

    if (toggle) {
        toggle.addEventListener('click', () => {
            const isMobile = window.innerWidth <= 1024;

            if (isMobile) {
                body.classList.toggle('sidebar-open');
            } else {
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebar_collapsed', body.classList.contains('sidebar-collapsed'));
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            body.classList.remove('sidebar-open');
        });
    }

    // Restore sidebar state on desktop
    if (window.innerWidth > 1024) {
        const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (collapsed) {
            body.classList.add('sidebar-collapsed');
        }
    }

    // Handle resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (window.innerWidth > 1024) {
                body.classList.remove('sidebar-open');
            }
        }, 150);
    });
}

/* ═══════════════════════════════════════════
   DROPDOWNS
   ═══════════════════════════════════════════ */

function initDropdowns() {
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const trigger = dropdown.querySelector('[data-dropdown-toggle]');
        
        if (trigger) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                
                // Close other dropdowns
                document.querySelectorAll('.dropdown.open').forEach(d => {
                    if (d !== dropdown) d.classList.remove('open');
                });
                
                dropdown.classList.toggle('open');
            });
        }
    });

    // Close dropdowns on click outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown.open').forEach(d => {
            d.classList.remove('open');
        });
    });
}

/* ═══════════════════════════════════════════
   TOAST NOTIFICATIONS
   ═══════════════════════════════════════════ */

function initToasts() {
    // Check for server-side flash messages
    const flashToast = document.getElementById('flash-toast');
    if (flashToast) {
        const type = flashToast.dataset.type;
        const message = flashToast.dataset.message;
        if (message) {
            showToast(type, message);
        }
    }
}

function showToast(type, message, duration = 4000) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const icons = {
        success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.info}</span>
        <span class="toast-message">${message}</span>
        <button class="toast-close" onclick="dismissToast(this)">&times;</button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        dismissToast(toast.querySelector('.toast-close'));
    }, duration);
}

function dismissToast(btn) {
    const toast = btn.closest('.toast');
    if (toast) {
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }
}

/* ═══════════════════════════════════════════
   MODALS
   ═══════════════════════════════════════════ */

function initModals() {
    // Close modal on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', () => {
            closeAllModals();
        });
    });

    // Close modal on close button
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', () => {
            closeAllModals();
        });
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
}

function openModal(id) {
    const modal = document.getElementById(id);
    const backdrop = document.getElementById(id + '-backdrop');
    
    if (modal) modal.classList.add('active');
    if (backdrop) backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const backdrop = document.getElementById(id + '-backdrop');
    
    if (modal) modal.classList.remove('active');
    if (backdrop) backdrop.classList.remove('active');
    document.body.style.overflow = '';
}

function closeAllModals() {
    document.querySelectorAll('.modal.active').forEach(m => m.classList.remove('active'));
    document.querySelectorAll('.modal-backdrop.active').forEach(b => b.classList.remove('active'));
    document.body.style.overflow = '';
}

/* ═══════════════════════════════════════════
   DELETE CONFIRMATIONS
   ═══════════════════════════════════════════ */

function initDeleteForms() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            const message = el.dataset.confirm || 'Apakah Anda yakin?';
            
            // Set confirm modal content
            const modal = document.getElementById('confirm-modal');
            if (modal) {
                modal.querySelector('.confirm-message').textContent = message;
                const confirmBtn = modal.querySelector('.confirm-action');
                confirmBtn.onclick = () => {
                    closeModal('confirm-modal');
                    
                    // If it's a link, navigate
                    if (el.tagName === 'A') {
                        window.location.href = el.href;
                    }
                    // If it's inside a form, submit
                    const form = el.closest('form') || document.getElementById(el.dataset.form);
                    if (form) {
                        form.submit();
                    }
                };
                openModal('confirm-modal');
            } else {
                if (confirm(message)) {
                    const form = el.closest('form') || document.getElementById(el.dataset.form);
                    if (form) form.submit();
                    else if (el.tagName === 'A') window.location.href = el.href;
                }
            }
        });
    });
}

/* ═══════════════════════════════════════════
   FETCH WRAPPER (CSRF-safe)
   ═══════════════════════════════════════════ */

async function fetchAPI(url, options = {}) {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : '';

    const defaults = {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    };

    if (!(options.body instanceof FormData)) {
        defaults.headers['Content-Type'] = 'application/json';
    }

    const config = { ...defaults, ...options };
    config.headers = { ...defaults.headers, ...options.headers };

    try {
        const response = await fetch(url, config);
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            throw new Error('Server mengembalikan respons non-JSON: ' + text.substring(0, 120));
        }
        
        if (!response.ok) {
            throw { status: response.status, ...(data || {}) };
        }
        
        return data;
    } catch (error) {
        if (error.message) {
            showToast('error', error.message);
        }
        throw error;
    }
}
