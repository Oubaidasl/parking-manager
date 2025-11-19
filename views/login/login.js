document.addEventListener('DOMContentLoaded', async function () {
    const form = document.getElementById('global-form');
    if (!form) return;

    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const submitButton = document.getElementById('submit-btn');
    const usernameError = document.getElementById('username-error');
    const passwordError = document.getElementById('password-error');

    const labelDefault = submitButton?.querySelector('[data-id="btn-label-default"]');
    const labelBusy = submitButton?.querySelector('[data-id="btn-label-busy"]');

    const setError = (el, msg) => { if (el) el.textContent = msg || ''; };
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

    const setSubmitting = (submitting) => {
        submitButton.disabled = submitting;
        submitButton.setAttribute('aria-busy', submitting ? 'true' : 'false');
        if (labelDefault && labelBusy) {
        if (submitting) { labelDefault.classList.add('hidden'); labelBusy.classList.remove('hidden'); }
        else { labelBusy.classList.add('hidden'); labelDefault.classList.remove('hidden'); }
        }
    };

    const validate = () => {
        let ok = true;
        setError(usernameError, '');
        setError(passwordError, '');
        setInvalid(username, false);
        setInvalid(password, false);

        if (!username.value.trim()) {
            setError(usernameError, 'Username is required.');
            setInvalid(username, true);
            ok = false;
        }

        if (!password.value.trim()) {
            setError(passwordError, 'Password is required.');
            setInvalid(password, true);
            ok = false;
        }

        return ok;
    };

    const isJSON = async (res) => {
        const isJson = res.headers.get('Content-Type').includes('application/json');
        return isJson ? await res.json() : null;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (!validate()) return;

        setSubmitting(true);

        // ... your fetch here ...
        payload = {
            username : username.value.trim(),
            password : password.value.trim()
        }

        try {
            const res = await fetch('/session-create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });

            const data = await isJSON(res);

            if (!data) { setSubmitting(false); return; }

            if (!data.ok) {
                setError(usernameError, data.error.username);
                setInvalid(username, true);

                setError(passwordError, data.error.password);
                setInvalid(password, true);

                setSubmitting(false);
                return;
            }

            window.location.assign(data.redirect);

        } catch {
            setSubmitting(false);
            throw new Error
        }

    }); // end submit handler
}); // end DOMContentLoaded
