// ids setup
const FORM_ID = 'global-form'
const USERNAME_ID = 'username'
const USERNAME_ERROR_ID = 'username-error'
const PASSWORD_ID = 'password'
const PASSWORD_ERROR_ID = 'password-error'
const BUTTON_ID = 'submit-btn'



// global event listener
document.addEventListener('DOMContentLoaded', async function (e) {

    // assign each element on a const
    const form = document.getElementById(FORM_ID);
    if (!form) return;

    const username = document.getElementById(USERNAME_ID);
    const password = document.getElementById(PASSWORD_ID);
    const submitButton = document.getElementById(BUTTON_ID);
    const usernameError = document.getElementById(USERNAME_ERROR_ID);
    const passwordError = document.getElementById(PASSWORD_ERROR_ID);
    if (!username || !password || !submitButton || !usernameError || !passwordError) return;

    const labelDefault = submitButton.querySelector('[data-id="btn-label-default"]');
    const labelBusy = submitButton.querySelector('[data-id="btn-label-busy"]');

    // form event listener
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        alert('ighvkjhlj');
        return;
    })
        // prevent default

        // validate

        // send post request

        // wait for results

})


    






