document.addEventListener('DOMContentLoaded', async () => {

    const panel = document.querySelector('.client-info');

    const clientSelector = document.getElementById('client-select');
    const nidIN = document.getElementById('nid');
    const rfidBadgeIN = document.getElementById('rfid-badge');
    const fullNameIn = document.getElementById('full-name');
    const phoneNumberIn = document.getElementById('phone-number');
    const emailIn = document.getElementById('e-mail');
    const matriculaIn = document.getElementById('matricula');
    const endDateIN = document.getElementById('end-date');

    const isJSON = async (res) => {
        const isJson = res.headers.get('Content-Type').includes('application/json');
        return isJson ? await res.json() : null;
    };

    let data;
    try {
        const res = await fetch('/client-read', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        data = await isJSON(res);

    } catch {
        throw new Error();
    }

    if (!data.ok) {
        throw new Error();
    }

    // data properties
    //  data.ok
    //  data.clients
    //      data.clients[clientID].NID
    //      data.clients[clientID].RFID
    //      data.clients[clientID].fullName
    //      data.clients[clientID].matricula
    //      data.clients[clientID].endDate

    for (const clientID in data.clients) {
        clientSelector.appendChild(addElement('option', clientID));
    }

    clientSelector.addEventListener('change', (e) => {
        e.preventDefault();

        const clientID = clientSelector.value;

        nidIN.value = data.clients[clientID].NID;
        rfidBadgeIN.value = data.clients[clientID].RFID;
        fullNameIn.value = data.clients[clientID].fullName;
        phoneNumberIn.value = data.clients[clientID].phoneNumber;
        emailIn.value = data.clients[clientID].email;
        matriculaIn.value = data.clients[clientID].matricula;
        endDateIN.value = data.clients[clientID].endDate;

        panel.classList.remove('hidden');
    });

});

function addElement(element, text) {
    const newOption = document.createElement(element);
    newOption.value = text;
    newOption.textContent = text;
    return newOption;
}
