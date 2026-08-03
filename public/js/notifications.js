/**
 * Notification System - Polling AJAX Client
 * Checks for new notifications every 30 seconds
 */

const NotificationSystem = {
    pollInterval: null,
    pollSeconds: 30,
    lastCount: 0,
    unreadIds: new Set(),

    init() {
        this.cacheElements();
        this.bindEvents();
        this.startPolling();
    },

    cacheElements() {
        this.badge = document.getElementById('notifBadge');
        this.bellIcon = document.getElementById('bellIcon');
        this.notifList = document.getElementById('notifList');
        this.notifCount = document.getElementById('notifCount');
        this.dropdownBtn = document.getElementById('notificationDropdownBtn');
    },

    bindEvents() {
        // Clear badge when dropdown opens
        const dropdownElement = document.getElementById('notifDropdownMenu');
        if (dropdownElement) {
            dropdownElement.addEventListener('show.bs.dropdown', () => {
                this.clearBadge();
                this.loadRecent();
            });
        }

        // Delegated events for dynamic elements
        document.addEventListener('click', (e) => {
            const markReadBtn = e.target.closest('.mark-read-btn');
            if (markReadBtn) {
                e.preventDefault();
                const id = markReadBtn.dataset.id;
                this.markSingleRead(id);
            }

            const deleteBtn = e.target.closest('.delete-notification-btn');
            if (deleteBtn) {
                e.preventDefault();
                const id = deleteBtn.dataset.id;
                this.deleteNotification(id);
            }
        });

        // Mark all read button
        const markAllBtn = document.getElementById('markAllRead');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => this.markAllRead());
        }

        // Clean old button
        const cleanBtn = document.getElementById('cleanOld');
        if (cleanBtn) {
            cleanBtn.addEventListener('click', () => this.cleanOld());
        }

        // Filter buttons
        const applyBtn = document.getElementById('applyFilters');
        if (applyBtn) {
            applyBtn.addEventListener('click', () => this.applyFilters());
        }
    },

    startPolling() {
        // Initial load
        this.checkUnreadCount();

        // Poll interval
        this.pollInterval = setInterval(() => {
            this.checkUnreadCount();
        }, this.pollSeconds * 1000);
    },

    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    },

    async checkUnreadCount() {
        try {
            const response = await fetch('/api/notifications/unread-count', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) return;
            
            const data = await response.json();
            const count = data.count;

            if (count !== this.lastCount) {
                this.updateBadge(count);
                
                // Show browser notification if new unread appeared
                if (count > this.lastCount && document.visibilityState === 'visible') {
                    // Don't notify if user has the dropdown open
                    const isOpen = this.dropdownBtn?.getAttribute('aria-expanded') === 'true';
                    if (!isOpen) {
                        this.showBrowserNotification(count - this.lastCount);
                    }
                }
                
                this.lastCount = count;
            }
        } catch (error) {
            console.error('Notification polling error:', error);
        }
    },

    updateBadge(count) {
        if (!this.badge) return;

        if (count > 0) {
            this.badge.textContent = count > 99 ? '99+' : count;
            this.badge.classList.remove('d-none');
            
            // Pulse animation on bell
            if (this.bellIcon) {
                this.bellIcon.style.transition = 'transform 0.3s ease';
                this.bellIcon.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.bellIcon.style.transform = 'scale(1)';
                }, 300);
            }
        } else {
            this.badge.classList.add('d-none');
        }

        if (this.notifCount) {
            this.notifCount.textContent = `${count} nueva${count !== 1 ? 's' : ''}`;
        }
    },

    clearBadge() {
        if (this.badge) {
            this.badge.classList.add('d-none');
        }
    },

    async loadRecent(limit = 5) {
        try {
            const response = await fetch(`/api/notifications/recent/${limit}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) return;
            
            const data = await response.json();
            this.renderDropdown(data.notifications);
        } catch (error) {
            console.error('Failed to load recent notifications:', error);
        }
    },

    renderDropdown(notifications) {
        if (!this.notifList) return;

        if (!notifications || notifications.length === 0) {
            this.notifList.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                    <small>No hay notificaciones</small>
                </div>
            `;
            return;
        }

        this.notifList.innerHTML = notifications.map(n => `
            <a href="${n.action_url || '#'}" class="notif-item ${!n.read ? 'unread' : ''}" data-notif-id="${n.id}">
                <div class="notif-icon" style="background: ${n.color}20; color: ${n.color};">
                    <i class="${n.icon}"></i>
                </div>
                <div class="notif-body">
                    <p class="notif-title">${this.escapeHtml(n.title)}</p>
                    <p class="notif-message">${this.escapeHtml(n.body)}</p>
                    <div class="notif-time">${this.escapeHtml(n.created_at)} · <i class="${n.category_icon}"></i> ${this.escapeHtml(n.category_label)}</div>
                </div>
            </a>
        `).join('');

        // Bind click to mark as read
        this.notifList.querySelectorAll('.notif-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const id = item.dataset.notifId;
                if (id) {
                    this.markSingleRead(id);
                }
            });
        });
    },

    async markSingleRead(id) {
        try {
            await fetch(`/api/notifications/${id}/read`, {
                method: 'PUT',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            
            // Update UI
            const item = document.querySelector(`.notif-item[data-notif-id="${id}"]`);
            if (item) {
                item.classList.remove('unread');
            }

            // Remove from DOM if on notifications page
            const pageItem = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (pageItem) {
                pageItem.classList.remove('bg-light-subtle');
                const btn = pageItem.querySelector('.mark-read-btn');
                if (btn) btn.remove();
            }

            this.lastCount = Math.max(0, this.lastCount - 1);
            this.updateBadge(this.lastCount);
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            // Update all items
            document.querySelectorAll('.notif-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
            document.querySelectorAll('.notification-item.bg-light-subtle').forEach(item => {
                item.classList.remove('bg-light-subtle');
            });

            this.lastCount = 0;
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            // Remove from DOM
            const item = document.querySelector(`.notif-item[data-notif-id="${id}"]`);
            if (item) item.remove();

            const pageItem = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (pageItem) pageItem.remove();

            this.lastCount = Math.max(0, this.lastCount - 1);
            this.updateBadge(this.lastCount);
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();
            alert(data.message);
        } catch (error) {
            console.error('Failed to clean old notifications:', error);
        }
    },

    applyFilters() {
        const status = document.getElementById('statusFilter')?.value || 'all';
        const category = document.getElementById('categoryFilter')?.value || '';
        
        // Reload notifications page with filters via redirect
        const params = new URLSearchParams();
        if (status !== 'all') params.set('status', status);
        if (category) params.set('filter', category);
        
        window.location.href = `{{ route('notifications.index') }}?${params.toString()}`;
    },

    showBrowserNotification(count) {
        if (!('Notification' in window)) return;
        if (Notification.permission === 'granted') {
            new Notification('Nueva(s) notificación(ones)', {
                body: `Tienes ${count} nueva(s) notificación(ones) sin leer`,
                icon: '/images/logo.png'
            });
        }
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    NotificationSystem.init();
});
