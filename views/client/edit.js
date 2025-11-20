document.addEventListener('DOMContentLoaded', async () => {

    const mainBtnsPanel = document.getElementById('main-buttons');
    const editPanel    = document.getElementById('edit-panel');

    const editInfoBtn = document.getElementById('edit-btn');
    const submitBtn    = document.getElementById('edit-submit');
    const cancelBtn    = document.getElementById('edit-cancel');
    const nidIn = document.getElementById('nid');
    const rfidBadgeIn = document.getElementById('rfid-badge');
    const fullNameIn = document.getElementById('full-name');
    const phoneNumberIn = document.getElementById('phone-number');
    const emailIn = document.getElementById('e-mail');
    const matriculaIn = document.getElementById('matricula');

    let rfidBadgeInit;
    let fullNameInit; 
    let phoneNumberInit;
    let emailInit;
    let matriculaInit;

    const initInputs =  () => {
        rfidBadgeInit = rfidBadgeIn.value;
        fullNameInit = fullNameIn.value; 
        phoneNumberInit = phoneNumberIn.value;
        emailInit = emailIn.value;
        matriculaInit = matriculaIn.value;
    }

    const restoreInputs = () => {
        rfidBadgeIn.value = rfidBadgeInit;
        fullNameIn.value = fullNameInit;
        phoneNumberIn.value = phoneNumberInit;
        emailIn.value = emailInit;
        matriculaIn.value = matriculaInit;

        setInvalid(nidIn, false);
        setInvalid(rfidBadgeIn, false);
        setInvalid(fullNameIn, false);
        setInvalid(phoneNumberIn, false);
        setInvalid(emailIn, false);
        setInvalid(matriculaIn, false);
    }


    const toggle = () => {
        mainBtnsPanel.classList.toggle('hidden');
        editPanel.classList.toggle('hidden');
    };

    const disableElements = (bool) => {
        toggle();
        rfidBadgeIn.disabled = bool;
        fullNameIn.disabled = bool;
        phoneNumberIn.disabled = bool;
        emailIn.disabled = bool;
        matriculaIn.disabled = bool;
    }

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


    editInfoBtn.addEventListener('click', () => {
        disableElements(false);
        initInputs();
    });
    cancelBtn.addEventListener('click', () => {
        disableElements(true);
        restoreInputs();
    });

    submitBtn.addEventListener('click', async () => {
        const NID = nidIn.value.trim();
        const RFID = rfidBadgeIn.value.trim();
        const fullName = fullNameIn.value.trim();
        const phoneNumber = phoneNumberIn.value.trim();
        const email = emailIn.value.trim();
        const matricula = matriculaIn.value.trim();

        setSubmitting(true);

        if (!NID || !RFID || !fullName || !phoneNumber) {
            setInvalid(nidIn, !NID);
            setInvalid(rfidBadgeIn, !RFID);
            setInvalid(fullNameIn, !fullName);
            setInvalid(phoneNumberIn, !phoneNumber);
            setSubmitting(false);
            return;
        }
        if (confirm(`You are about to edit client info, Proceed ?`)) {

            const payload = { NID, RFID, fullName, phoneNumber, email, matricula };

            try {
                const res = await fetch('/client-update', {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    console.error('Bad JSON or not ok:');
                    setSubmitting(false);
                    return;
                }

                const data = await isJSON(res);

                if (!data.ok) { 
                    setInvalid(nidIn, data.error.nid ?? false);
                    setInvalid(rfidBadgeIn, data.error.rfid ?? false);
                    setInvalid(fullNameIn, data.error.fullName ?? false);
                    setInvalid(phoneNumberIn, data.error.phoneNumber ?? false);
                    setInvalid(emailIn, data.error.email ?? false);
                    setInvalid(matriculaIn, data.error.matricula ?? false);
                    setSubmitting(false);
                    return;
                }

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
