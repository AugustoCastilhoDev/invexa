@auth
@php $user = auth()->user(); @endphp
<li class="nav-item dropdown" id="notif-dropdown">
    <a class="nav-link position-relative px-2" href="#"
       id="notifBell" role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false"
       title="Notificações">
        <i class="bi bi-bell fs-5" style="color:rgba(226,232,240,.65);"></i>
        <span id="notif-badge"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
              style="background:#ef4444;font-size:.55rem;padding:.28rem .42rem;display:none;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0"
         style="min-width:340px;max-width:94vw;border:1px solid rgba(14,165,233,.18);background:rgba(10,18,35,.98);border-radius:.7rem;box-shadow:0 20px 40px rgba(0,0,0,.55);">

        {{-- Cabeçalho --}}
        <div class="d-flex align-items-center justify-content-between px-3 py-2"
             style="border-bottom:1px solid rgba(14,165,233,.12);">
            <span class="fw-semibold text-white" style="font-size:.85rem;">
                <i class="bi bi-bell me-1 text-info"></i>Notificações
            </span>
            <a href="#" id="notif-mark-all"
               style="font-size:.72rem;color:#38BDF8;text-decoration:none;">
                Marcar todas como lidas
            </a>
        </div>

        {{-- Lista --}}
        <div id="notif-list"
             style="max-height:380px;overflow-y:auto;">
            <div id="notif-empty" class="text-center py-4" style="color:rgba(148,163,184,.6);font-size:.82rem;display:none;">
                <i class="bi bi-check2-circle d-block fs-4 mb-1 opacity-50"></i>
                Nenhuma notificação nova
            </div>
        </div>

        {{-- Rodapé --}}
        <div class="px-3 py-2" style="border-top:1px solid rgba(14,165,233,.10);">
            <a href="{{ route('notifications.index') }}"
               style="font-size:.76rem;color:#38BDF8;text-decoration:none;">
                <i class="bi bi-list-ul me-1"></i>Ver todas as notificações
            </a>
        </div>
    </div>
</li>

<style>
#notif-list::-webkit-scrollbar { width: 4px; }
#notif-list::-webkit-scrollbar-track { background: transparent; }
#notif-list::-webkit-scrollbar-thumb { background: rgba(14,165,233,.3); border-radius: 4px; }
.notif-item {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .65rem .9rem;
    border-bottom: 1px solid rgba(148,163,184,.07);
    cursor: pointer;
    transition: background .15s ease;
    text-decoration: none;
}
.notif-item:hover { background: rgba(14,165,233,.07); }
.notif-item:last-child { border-bottom: 0; }
.notif-icon-wrap {
    width: 2.1rem; height: 2.1rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1rem;
}
.notif-icon-wrap.danger  { background: rgba(239,68,68,.18);  color: #f87171; }
.notif-icon-wrap.warning { background: rgba(234,179,8,.15);  color: #facc15; }
.notif-icon-wrap.info    { background: rgba(14,165,233,.15); color: #38BDF8; }
.notif-icon-wrap.success { background: rgba(34,197,94,.15);  color: #4ade80; }
.notif-title   { font-size: .8rem; font-weight: 600; color: #e2e8f0; line-height: 1.3; }
.notif-message { font-size: .74rem; color: rgba(148,163,184,.85); line-height: 1.4; margin-top: .1rem; }
.notif-time    { font-size: .68rem; color: rgba(148,163,184,.5); margin-top: .2rem; }
</style>

<script>
(function () {
    const POLL_INTERVAL = 60000; // 60 s
    let pollTimer;

    function fetchNotifications() {
        fetch('{{ route("notifications.unread") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const list  = document.getElementById('notif-list');
            const empty = document.getElementById('notif-empty');

            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = data.count > 0 ? 'inline' : 'none';

            // Limpa itens antigos (mantém o #notif-empty)
            list.querySelectorAll('.notif-item').forEach(el => el.remove());

            if (data.items.length === 0) {
                empty.style.display = 'block';
                return;
            }
            empty.style.display = 'none';

            data.items.forEach(n => {
                const el = document.createElement('a');
                el.className = 'notif-item';
                el.href = n.url || '#';
                el.dataset.id = n.id;
                el.innerHTML = `
                    <div class="notif-icon-wrap ${n.type}">
                        <i class="bi ${n.icon}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="notif-title">${n.title}</div>
                        <div class="notif-message">${n.message}</div>
                        <div class="notif-time">${n.time}</div>
                    </div>
                `;
                el.addEventListener('click', function (e) {
                    markRead(n.id);
                });
                list.insertBefore(el, empty);
            });
        })
        .catch(() => {});
    }

    function markRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => fetchNotifications()).catch(() => {});
    }

    document.getElementById('notif-mark-all')?.addEventListener('click', function (e) {
        e.preventDefault();
        fetch('{{ route("notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).then(() => fetchNotifications()).catch(() => {});
    });

    // Busca ao abrir o dropdown
    document.getElementById('notifBell')?.addEventListener('show.bs.dropdown', fetchNotifications);

    // Polling periódico (atualiza badge sem abrir)
    function startPolling() {
        fetchNotifications();
        pollTimer = setInterval(fetchNotifications, POLL_INTERVAL);
    }

    document.addEventListener('DOMContentLoaded', startPolling);
})();
</script>
@endauth
