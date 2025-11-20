document.addEventListener('DOMContentLoaded', async () => {

    const mainBtnsPanel = document.getElementById('main-buttons');
    const renewPanel    = document.getElementById('renew-panel');

    const renewPlanBtn = document.getElementById('renew-btn');
    const submitBtn    = document.getElementById('renew-submit');
    const cancelBtn    = document.getElementById('renew-cancel');
    const planSelect   = document.getElementById('renew-plan');
    const nidIN        = document.getElementById('nid');

    const toggle = () => {
        mainBtnsPanel.classList.toggle('hidden');
        renewPanel.classList.toggle('hidden');
    };

    const isJSON = async (res) => {
        const isJson = res.headers.get('Content-Type').includes('application/json');
        return isJson ? await res.json() : null;
    };

    const labelDefault = submitBtn?.querySelector('[data-id="btn-label-default"]');
    const labelBusy = submitBtn?.querySelector('[data-id="btn-label-busy"]');

    const setSubmitting = (submitting) => {
        submitBtn.disabled = submitting;
        submitBtn.setAttribute('aria-busy', submitting ? 'true' : 'false');
        if (labelDefault && labelBusy) {
            if (submitting) {
                labelDefault.classList.add('hidden'); 
                labelBusy.classList.remove('hidden'); 
            } else { 
                labelBusy.classList.add('hidden'); 
                labelDefault.classList.remove('hidden'); 
            }
        }
    };


    renewPlanBtn.addEventListener('click', toggle);
    cancelBtn.addEventListener('click', toggle);

    submitBtn.addEventListener('click', async () => {
        if(!planSelect.value) return;
        setSubmitting(true);
        if (confirm(`You are about to extend client subscription for ${planSelect.value} months, ensure you have been payed before proceeding`)) {
                try {
                    const res = await fetch('/client-update', {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ NID: nidIN.value,
                            plan: Number(planSelect.value) || 1
                        })
                    });

                    if (!res.ok) {
                        console.error('Bad JSON or not ok:');
                        setSubmitting(false);
                        return;
                    }

                    const data = await isJSON(res);

                    if (!data.ok) { setSubmitting(false); return; }

                    window.location.assign(data.redirect);

                } catch(error) {
                    setSubmitting(false);
                    console.log(error);
                    throw new Error;
                }
            }
            setSubmitting(false);
        return;
        
    });
});
