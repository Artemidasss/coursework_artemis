// assets/js/api.js  — все AJAX-запросы к бэкенду

const API = {

    async _req(endpoint, action, method, body) {
        const url  = `api/${endpoint}?action=${action}`;
        const opts = { method, credentials: 'same-origin', headers: { 'Content-Type': 'application/json' } };
        if (body && method !== 'GET') opts.body = JSON.stringify(body);
        try {
            const res  = await fetch(url, opts);
            const data = await res.json();
            return { ok: res.ok, status: res.status, data };
        } catch {
            return { ok: false, status: 0, data: { error: 'Ошибка сети. Проверьте подключение.' } };
        }
    },

    auth: {
        register: (p) => API._req('auth.php', 'register', 'POST', p),
        login:    (p) => API._req('auth.php', 'login',    'POST', p),
        logout:   ()  => API._req('auth.php', 'logout',   'POST'),
        check:    ()  => API._req('auth.php', 'check',    'GET'),
    },

    subs: {
        purchase: (type) => API._req('subscriptions.php', 'purchase', 'POST', { type }),
        my:       ()     => API._req('subscriptions.php', 'my',       'GET'),
    },
};
