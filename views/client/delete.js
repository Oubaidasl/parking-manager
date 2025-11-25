document.addEventListener('DOMContentLoaded', async () => {

    deleteBtn = document.getElementById('delete-btn');
    nidEl = document.getElementById('nid');

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

    deleteBtn.addEventListener('click', async () => {

        deleteBtn.disabled = true;

        NID = nidEl.value;

        if (!NID) {setInvalid(nidEl, true); return;}

        try {
            const res = await fetch('/client-delete', {
                method: 'DELETE',
                headers : {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({NID})
            });

            console.log(NID);
            console.log('ok');

            if (!res.ok) {deleteBtn.disabled = false; throw new Error;}

            const data = await isJSON(res);

            if (!data.ok) {setInvalid(nidEl, true); deleteBtn.disabled = false; return;}

            window.location.assign(data.redirect);


        } catch (error) {
            deleteBtn.disabled = false;
            console.error(error);
            throw new Error;
        }


    })

});