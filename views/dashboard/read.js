document.addEventListener('DOMContentLoaded', async () => {

    let data;

    const isJSON = async (res) => {
        const isJson = res.headers.get('Content-Type').includes('application/json');
        return isJson ? await res.json() : null;
    };

    try {
        const res = await fetch('/dashboard-read', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            console.error('no response');
            return;
        }

        data = await isJSON(res);


    } catch (error) {
        console.error(error);
        throw new Error;
    }

    document.getElementById('total-spots').querySelector('[data-id="total-spots-value"]').innerHTML = data.totalSpots;
    document.getElementById('empty-spots').querySelector('[data-id="empty-spots-value"]').innerHTML = data.emptySpots;
    document.getElementById('total-users').querySelector('[data-id="total-users-value"]').innerHTML = data.totalUsers;
    document.getElementById('expired-subs').querySelector('[data-id="expired-subs-value"]').innerHTML = data.expiredSubs;
    document.getElementById('permitted').querySelector('[data-id="permitted-value"]').innerHTML = data.permitted;
    document.getElementById('expiring-this-week').querySelector('[data-id="expiring-this-week-value"]').innerHTML = data.expiringThisWeek;
    document.getElementById('new-this-month').querySelector('[data-id="new-this-month-value"]').innerHTML = data.newThisMonth;

});