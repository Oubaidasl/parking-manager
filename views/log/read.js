document.addEventListener('DOMContentLoaded', () => {
    let adminactions = [];
    let renewals = [];
    let entrylogs = [];

    const isJSON = async (res) => {
        const isJson = res.headers.get('Content-Type')?.includes('application/json');
        return isJson ? await res.json() : null;
    };

    const getDatePart = (ts) => (typeof ts === 'string' ? ts.slice(0, 10) : '');

    const loadLogs = async () => {
        try {
            const res = await fetch('/log-read', {
                method: 'GET',
                headers: { Accept: 'application/json' },
            });

            if (!res.ok) {
                console.error('logs-read: HTTP error', res.status);
                return;
            }

            const data = await isJSON(res);
            if (!data || !data.ok) {
                console.error('logs-read: invalid JSON');
                return;
            }

            adminactions = Array.isArray(data.adminactions) ? data.adminactions : [];
            renewals = Array.isArray(data.renewals) ? data.renewals : [];
            entrylogs = Array.isArray(data.entrylogs) ? data.entrylogs : [];

            populateAdminSelects();
            populateNidSelects();
            clearDateFilters();      // show all data initially
            applyAllFilters();
        } catch (err) {
            console.error('logs-read: fetch failed', err);
        }
    };

    const getInputValue = (name) => {
        const el = document.querySelector(`[name="${name}"]`);
        return el ? el.value.trim() : '';
    };

    const clearDateFilters = () => {
        document.querySelectorAll('.date-input').forEach((el) => {
            el.value = ''; // empty => no date filter
        });
    };

    const populateAdminSelects = () => {
        const ids = new Set();
        adminactions.forEach((r) => ids.add(String(r.admin_id)));
        renewals.forEach((r) => ids.add(String(r.admin_id)));

        const optionsHtml = Array.from(ids)
            .filter((id) => id && id !== '0')
            .sort()
            .map((id) => `<option value="${id}">${id}</option>`)
            .join('');

        ['adminactions_admin', 'renewals_admin'].forEach((name) => {
            const sel = document.querySelector(`select[name="${name}"]`);
            if (!sel) return;
            const first = sel.querySelector('option[value=""]');
            sel.innerHTML = '';
            if (first) sel.appendChild(first);
            sel.insertAdjacentHTML('beforeend', optionsHtml);
        });
    };

    const populateNidSelects = () => {
        const adminTargets = new Set();
        const renewalClients = new Set();
        const entryClients = new Set();

        adminactions.forEach((r) => adminTargets.add(String(r.targetNid)));
        renewals.forEach((r) => renewalClients.add(String(r.clientNid)));
        entrylogs.forEach((r) => entryClients.add(String(r.clientNid)));

        const makeOptions = (set) =>
            Array.from(set)
                .filter((v) => v && v !== '0')
                .sort()
                .map((v) => `<option value="${v}">${v}</option>`)
                .join('');

        const adminTargetSel = document.querySelector('select[name="adminactions_target"]');
        if (adminTargetSel) {
            const first = adminTargetSel.querySelector('option[value=""]');
            adminTargetSel.innerHTML = '';
            if (first) adminTargetSel.appendChild(first);
            adminTargetSel.insertAdjacentHTML('beforeend', makeOptions(adminTargets));
        }

        const renewClientSel = document.querySelector('select[name="renewals_client"]');
        if (renewClientSel) {
            const first = renewClientSel.querySelector('option[value=""]');
            renewClientSel.innerHTML = '';
            if (first) renewClientSel.appendChild(first);
            renewClientSel.insertAdjacentHTML('beforeend', makeOptions(renewalClients));
        }

        const entryClientSel = document.querySelector('select[name="entrylogs_client"]');
        if (entryClientSel) {
            const first = entryClientSel.querySelector('option[value=""]');
            entryClientSel.innerHTML = '';
            if (first) entryClientSel.appendChild(first);
            entryClientSel.insertAdjacentHTML('beforeend', makeOptions(entryClients));
        }
    };

    // Filtering
    const filterAdminActions = () => {
        const date = getInputValue('adminactions_date');
        const type = getInputValue('adminactions_type');
        const adminId = getInputValue('adminactions_admin');
        const target = getInputValue('adminactions_target');

        return adminactions.filter((row) => {
            if (date && getDatePart(row.timestamp) !== date) return false;
            if (type && row.actionType !== type) return false;
            if (adminId && String(row.admin_id) !== adminId) return false;
            if (target && String(row.targetNid) !== target) return false;
            return true;
        });
    };

    const filterRenewals = () => {
        const date = getInputValue('renewals_date');
        const client = getInputValue('renewals_client');
        const adminId = getInputValue('renewals_admin');

        return renewals.filter((row) => {
            if (date && getDatePart(row.timestamp) !== date) return false;
            if (client && String(row.clientNid) !== client) return false;
            if (adminId && String(row.admin_id) !== adminId) return false;
            return true;
        });
    };

    const filterEntryLogs = () => {
        const date = getInputValue('entrylogs_date');
        const client = getInputValue('entrylogs_client');

        return entrylogs.filter((row) => {
            if (date && getDatePart(row.timestamp) !== date) return false;
            if (client && String(row.clientNid) !== client) return false;
            return true;
        });
    };

    // Empty-state helper
    const toggleEmptyMessage = (section, hasRows) => {
        const el = document.getElementById(`${section}-empty`);
        if (!el) return;

        // Show message whenever there are no rows after applying filters
        el.classList.toggle('hidden', hasRows);
    };


    // Renderers
    const renderAdminActions = (rows) => {
        const tbody = document.getElementById('adminactions-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        rows
            .sort((a, b) => (a.timestamp > b.timestamp ? -1 : 1))
            .forEach((row) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-900/40';
                tr.innerHTML = `
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.timestamp ?? ''}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.admin_id ?? ''}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-indigo-300">${row.actionType ?? ''}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.targetNid ?? ''}</td>
                `;
                tbody.appendChild(tr);
            });

        toggleEmptyMessage('adminactions', rows.length > 0);
    };

    const renderRenewals = (rows) => {
        const tbody = document.getElementById('renewals-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        rows
            .sort((a, b) => (a.timestamp > b.timestamp ? -1 : 1))
            .forEach((row) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-900/40';
                tr.innerHTML = `
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.timestamp ?? ''}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.clientNid ?? ''}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.admin_id ?? ''}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.renewedTo ?? ''}</td>
                `;
                tbody.appendChild(tr);
            });

        toggleEmptyMessage('renewals', rows.length > 0);
    };

    const renderEntryLogs = (rows) => {
        const tbody = document.getElementById('entrylogs-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        rows
            .sort((a, b) => (a.timestamp > b.timestamp ? -1 : 1))
            .forEach((row) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-900/40';
                tr.innerHTML = `
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.timestamp ?? ''}</td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-300">${row.clientNid ?? ''}</td>
                `;
                tbody.appendChild(tr);
            });

        toggleEmptyMessage('entrylogs', rows.length > 0);
    };

    const applyAllFilters = () => {
        renderAdminActions(filterAdminActions());
        renderRenewals(filterRenewals());
        renderEntryLogs(filterEntryLogs());
    };

    const bindFilter = (selector) => {
        document.querySelectorAll(selector).forEach((el) => {
            el.addEventListener('change', applyAllFilters);
            el.addEventListener('input', applyAllFilters);
        });
    };

    bindFilter(
        'input[name="adminactions_date"], select[name="adminactions_type"], select[name="adminactions_admin"], select[name="adminactions_target"]'
    );
    bindFilter('input[name="renewals_date"], select[name="renewals_client"], select[name="renewals_admin"]');
    bindFilter('input[name="entrylogs_date"], select[name="entrylogs_client"]');

    loadLogs();
});
