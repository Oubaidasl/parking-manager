document.addEventListener('DOMContentLoaded', async (e) => {

    const buttonPanel = document.querySelector('.client-selection');
    const infoPanel = document.querySelector('.client-info');

    const nidIN = document.getElementById('nid');
    const rfidBadgeIN = document.getElementById('rfid-badge');
    const fullNameIn = document.getElementById('full-name');
    const matriculaIn = document.getElementById('matricula');
    const endDateDiv = document.querySelector('.end-date-label');

    const editBtn = document.getElementById('edit-btn');
    const renewBtn = document.getElementById('renew-btn');
    const deleteBtn = document.getElementById('delete-btn');
    const submitBtn = document.getElementById('client-create-submit-btn');
    const cancelBtn = document.getElementById('client-create-cancel-btn');

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

    const setInvalid = (input, invalid) => { 
        if (invalid) {
            input.classList.add(
                'ring-2','ring-red-500/60','focus:ring-red-500',
                'border','border-red-500/60','focus:border-red-500',
                'bg-red-500/10','placeholder:text-red-300'
            );
            input.setAttribute('aria-invalid', 'true');
        } else {
            input.classList.remove(
                'ring-2','ring-red-500/60','focus:ring-red-500',
                'border','border-red-500/60','focus:border-red-500',
                'bg-red-500/10','placeholder:text-red-300'
            );
            input.setAttribute('aria-invalid', 'false');
        } 
    };

    const isJSON = async (res) => {
        const isJson = res.headers.get('Content-Type').includes('application/json');
        return isJson ? await res.json() : null;
    };

    document.addEventListener('click', (e) => {
        if (e.target.id == 'client-create-button') {

            buttonPanel.classList.add('hidden');
            infoPanel.classList.remove('hidden');
            endDateDiv.classList.add('hidden');

            nidIN.value = null;
            rfidBadgeIN.value = null;
            fullNameIn.value = null;
            matriculaIn.value = null;

            nidIN.disabled = false;
            rfidBadgeIN.disabled = false;
            fullNameIn.disabled = false;
            matriculaIn.disabled = false;
            
            submitBtn.classList.remove('hidden');
            cancelBtn.classList.remove('hidden');
            deleteBtn.classList.add('hidden');
            editBtn.classList.add('hidden');
            renewBtn.classList.add('hidden');

            submitBtn.addEventListener('click', async () => {
                const NID = nidIN.value.trim();
                const RFID = rfidBadgeIN.value.trim();
                const fullName = fullNameIn.value.trim();
                const matricula = matriculaIn.value.trim();

                setSubmitting(true);

                if (!NID || !RFID || !fullName || !matricula) {
                    setInvalid(nidIN, !NID);
                    setInvalid(rfidBadgeIN, !RFID);
                    setInvalid(fullNameIn, !fullName);
                    setInvalid(matriculaIn, !matricula);
                    setSubmitting(false);
                    return;
                }

                const payload = { NID, RFID, fullName, matricula };

                try {
                    const res = await fetch('/client-create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await isJSON(res);

                    if (!data.ok) throw new Error;

                    window.location.assign(data.redirect);

                } catch {
                    throw new Error;
                }
            });

            cancelBtn.addEventListener('click', () => {
                location.reload();
            });

        }
    });

});
