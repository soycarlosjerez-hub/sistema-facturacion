/**
 * Notification System - Activity Feed Client
 * Polls the activity feed every 10 seconds and renders:
 *  - Badge with real unread count (all pages)
 *  - Dropdown with latest activities (header, all pages)
 *  - Full activity feed page actions (mark read / delete / filters)
 */
const NotificationSystem = {
    pollInterval: null,
    pollSeconds: 10,
    lastSeenId: 0,
    unreadCount: 0,
    renderedIds: new Set(),

    init() {
        this.cacheElements();
        this.bindEvents();
        this.refresh();
        this.startPolling();
    },

    cacheElements() {
        this.badge = document.getElementById('notifBadge');
        this.bellIcon = document.getElementById('bellIcon');
        this.notifList = document.getElementById('notifList');
        this.notifCount = document.getElementById('notifCount');
        this.dropdownBtn = document.getElementById('notificationDropdownBtn');
        this.dropdownMenu = document.getElementById('notifDropdownMenu');
    },

    bindEvents() {
        if (this.dropdownMenu) {
            this.dropdownMenu.addEventListener('show.bs.dropdown', () => {
                this.refresh(true);
            });
        }

        document.addEventListener('click', (e) => {
            const markReadBtn = e.target.closest('.mark-read-btn');
            if (markReadBtn) {
                e.preventDefault();
                e.stopPropagation();
                this.markSingleRead(markReadBtn.dataset.id);
                return;
            }

            const deleteBtn = e.target.closest('.delete-notification-btn');
            if (deleteBtn) {
                e.preventDefault();
                e.stopPropagation();
                this.deleteNotification(deleteBtn.dataset.id);
                return;
            }

            const notifItem = e.target.closest('.notif-item');
            if (notifItem && this.notifList && this.notifList.contains(notifItem)) {
                const id = notifItem.dataset.id;
                if (id && !notifItem.dataset.reading) {
                    notifItem.dataset.reading = '1';
                    this.markSingleRead(id, true);
                }
            }
        });

        const markAllBtn = document.getElementById('markAllRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.markAllRead());
        }

        const cleanBtn = document.getElementById('cleanOld');
        if (cleanBtn) {
            cleanBtn.addEventListener('click', () => this.cleanOld());
        }

        const applyBtn = document.getElementById('applyFilters');
        if (applyBtn) {
            applyBtn.addEventListener('click', () => this.applyFilters());
        }
    },

    startPolling() {
        this.pollInterval = setInterval(() => this.refresh(), this.pollSeconds * 1000);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(this.pollInterval);
                this.pollInterval = null;
            } else if (!this.pollInterval) {
                this.refresh(true);
                this.pollInterval = setInterval(() => this.refresh(), this.pollSeconds * 1000);
            }
        });

        window.addEventListener('beforeunload', () => {
            if (this.pollInterval) clearInterval(this.pollInterval);
        });
    },

    async refresh(force = false) {
        try {
            const sinceId = force ? 0 : this.lastSeenId;
            const response = await fetch(`/api/notifications/feed?limit=8&since_id=${sinceId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) return;

            const data = await response.json();
            const items = data.items || [];
            const unread = data.unread_count || 0;

            const hadNew = data.has_new || this.renderedIds.size === 0;

            if (items.length > 0) {
                this.lastSeenId = Math.max(this.lastSeenId, ...items.map((i) => i.id));
                if (hadNew) {
                    this.renderedIds = new Set(items.map((i) => i.id));
                    this.renderDropdown(items);
                }
            }

            if (unread !== this.unreadCount) {
                this.unreadCount = unread;
                this.updateBadge(unread);
                if (hadNew && unread > 0 && document.visibilityState === 'visible' && !this.isDropdownOpen()) {
                    this.showBrowserNotification(unread);
                }
            }
        } catch (error) {
            console.error('Notification polling error:', error);
        }
    },

    isDropdownOpen() {
        return this.dropdownBtn?.getAttribute('aria-expanded') === 'true';
    },

    updateBadge(count) {
        if (!this.badge) return;

        if (count > 0) {
            this.badge.textContent = count > 99 ? '99+' : count;
            this.badge.classList.remove('d-none');
            if (this.bellIcon) {
                this.bellIcon.style.transition = 'transform 0.3s ease';
                this.bellIcon.style.transform = 'scale(1.2)';
                setTimeout(() => { this.bellIcon.style.transform = 'scale(1)'; }, 300);
            }
        } else {
            this.badge.classList.add('d-none');
        }

        if (this.notifCount) {
            this.notifCount.textContent = `${count} nueva${count !== 1 ? 's' : ''}`;
        }
    },

    clearBadge() {
        if (this.badge) this.badge.classList.add('d-none');
    },

    renderDropdown(items) {
        if (!this.notifList) return;

        if (!items || items.length === 0) {
            this.notifList.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-activity fs-3 d-block mb-2"></i>
                    <small>No hay actividad todavía</small>
                </div>
            `;
            return;
        }

        this.notifList.innerHTML = items.map((n) => {
            const actor = this.escapeHtml(n.actor_name || 'Sistema');
            const verb = n.action ? `<span class="text-muted"> ${this.escapeHtml(n.action)}</span>` : '';
            const title = `<span class="fw-medium"> ${this.escapeHtml(n.title)}</span>`;
            const body = n.body ? `<p class="notif-message">${this.escapeHtml(n.body)}</p>` : '';
            const href = n.action_url || '#';
            const time = this.escapeHtml(n.created_at);
            const catLabel = this.escapeHtml(n.category_label || 'Sistema');
            const catIcon = n.category_icon || 'bi-bell';

            return `
                <a href="${href}" class="notif-item ${n.read ? '' : 'unread'}" data-id="${n.id}">
                    <div class="notif-avatar" style="background:${n.color}22; color:${n.color};">
                        <i class="${n.icon || 'bi-bell'}"></i>
                    </div>
                    <div class="notif-body">
                        <p class="notif-title"><strong>${actor}</strong>${verb}${title}</p>
                        ${body}
                        <div class="notif-time">${time} · <i class="${catIcon}"></i> ${catLabel}</div>
                    </div>
                </a>
            `;
        }).join('');
    },

    async markSingleRead(id, silent = false) {
        try {
            await fetch(`/api/notifications/${id}/read`, {
                method: 'PUT',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf()
                }
            });

            const item = document.querySelector(`.notif-item[data-id="${id}"]`);
            if (item) item.classList.remove('unread');

            const pageItem = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (pageItem) {
                pageItem.classList.remove('bg-light-subtle');
                pageItem.querySelector('.mark-read-btn')?.remove();
                const badge = pageItem.querySelector('.badge');
                if (badge) badge.textContent = 'Leída';
            }

            if (!silent) {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.updateBadge(this.unreadCount);
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    },

    async markAllRead() {
        try {
            await fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf()
                }
            });

            document.querySelectorAll('.notif-item.unread').forEach((item) => item.classList.remove('unread'));
            document.querySelectorAll('.notification-item.bg-light-subtle').forEach((item) => item.classList.remove('bg-light-subtle'));
            document.querySelectorAll('.notification-item .mark-read-btn').forEach((btn) => btn.remove());

            this.unreadCount = 0;
            this.updateBadge(0);
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    },

    async deleteNotification(id) {
        if (!confirm('¿Eliminar esta notificación?')) return;

        try {
            await fetch(`/api/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf()
                }
            });

            document.querySelector(`.notif-item[data-id="${id}"]`)?.remove();
            document.querySelector(`.notification-item[data-id="${id}"]`)?.remove();

            this.unreadCount = Math.max(0, this.unreadCount - 1);
            this.updateBadge(this.unreadCount);
        } catch (error) {
            console.error('Failed to delete notification:', error);
        }
    },

    async cleanOld() {
        if (!confirm('¿Eliminar notificaciones mayores a 30 días?')) return;

        try {
            const response = await fetch('/api/notifications/clean-old', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrf()
                }
            });
            const data = await response.json();
            alert(data.message || 'Notificaciones antiguas eliminadas');
        } catch (error) {
            console.error('Failed to clean old notifications:', error);
        }
    },

    applyFilters() {
        const status = document.getElementById('statusFilter')?.value || 'all';
        const category = document.getElementById('categoryFilter')?.value || '';

        const params = new URLSearchParams();
        if (status !== 'all') params.set('status', status);
        if (category) params.set('filter', category);

        const base = window.location.pathname;
        const query = params.toString();
        window.location.href = query ? `${base}?${query}` : base;
    },

    showBrowserNotification(count) {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'granted') {
            new Notification('Nueva actividad en la instancia', {
                body: `Tienes ${count} nueva(s) actividad(es)`,
                icon: '/images/logo.png'
            });
        }
    },

    csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    NotificationSystem.init();
});