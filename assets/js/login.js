// --- Constants ---
const COOKIE_CONSENT_NAME = 'userCookieConsent';
const COOKIE_EXPIRY_DAYS = 30;
const SAVE_LOGIN_CHECKBOX_ID = 'rememberMe'; // ID of your checkbox
const SAVE_LOGIN_WRAPPER_ID = 'rememberMeWrapper'; // ID of the new wrapper span
const COOKIE_BANNER_ID = 'cookie'; // ID of your cookie banner/card

// --- Cookie Helper Functions ---
// (setCookie, getCookie - keep as they are)
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (encodeURIComponent(value) || "") + expires + "; path=/; Secure; SameSite=Lax";
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) {
            try {
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
            } catch (e) {
                console.error(`Error decoding cookie "${name}":`, e);
                return null;
            }
        }
    }
    return null;
}


// --- Function to Update Save Login Checkbox State ---
// (updateSaveLoginCheckboxState - keep as it is)
function updateSaveLoginCheckboxState() {
    const saveLoginCheckbox = document.getElementById(SAVE_LOGIN_CHECKBOX_ID);
    const consent = getCookie(COOKIE_CONSENT_NAME);

    if (!saveLoginCheckbox) {
        return;
    }

    if (consent === "true") {
        saveLoginCheckbox.disabled = false;
        console.log("Cookie consent accepted. 'Remember me' checkbox enabled.");
    } else {
        saveLoginCheckbox.disabled = true;
        saveLoginCheckbox.checked = false;
        console.log("Cookie consent not accepted or rejected. 'Remember me' checkbox disabled and unchecked.");
    }
}

// --- NEW: Function to explicitly show the cookie banner ---
function showCookieBanner() {
    const cookiesCard = document.getElementById(COOKIE_BANNER_ID);
    if (cookiesCard) {
        // Only show if consent hasn't already been given as 'true'
        // (Prevents showing it again if user clicks wrapper after accepting)
        const consent = getCookie(COOKIE_CONSENT_NAME);
        if (consent !== "true") {
             cookiesCard.style.display = "block";
             console.log('Cookie banner shown on wrapper click.');
        } else {
            console.log('Consent already true, banner not shown on wrapper click.')
        }
    } else {
        console.warn(`Cookie banner element (#${COOKIE_BANNER_ID}) not found.`);
    }
}


// --- Initialization Functions ---
// (showPageContent - keep as it is)
function showPageContent() {
    document.body.classList.add('content-ready');
    console.log("Page content displayed, loading indicator hidden.");
}

// (handleCookieConsent - keep as it is, it updates the checkbox state)
function handleCookieConsent() {
    const cookiesCard = document.getElementById(COOKIE_BANNER_ID); // Use constant
    const acceptButton = document.getElementById("acceptCookies");
    const rejectButton = document.getElementById("rejectCookies");

    if (!cookiesCard) {
        console.warn(`Cookie consent card element (#${COOKIE_BANNER_ID}) not found.`);
        updateSaveLoginCheckboxState();
        return;
    }

    const consent = getCookie(COOKIE_CONSENT_NAME);

    if (consent !== "true" && consent !== "false") {
        // Show banner initially ONLY if no decision has been made
         cookiesCard.style.display = "block";

        acceptButton?.addEventListener("click", () => {
            setCookie(COOKIE_CONSENT_NAME, "true", COOKIE_EXPIRY_DAYS);
            cookiesCard.style.display = "none";
            console.log("User accepted cookies");
            updateSaveLoginCheckboxState();
        });

        rejectButton?.addEventListener("click", () => {
            setCookie(COOKIE_CONSENT_NAME, "false", COOKIE_EXPIRY_DAYS);
            cookiesCard.style.display = "none";
            console.log("User rejected cookies");
            updateSaveLoginCheckboxState();
        });

    } else {
        // If consent is already set, ensure banner is hidden and update checkbox state
        cookiesCard.style.display = "none";
        console.log(`Cookie consent already set: ${consent}`);
        updateSaveLoginCheckboxState();
    }
}


// --- Bootstrap Popover Initialization ---
// (Keep your popover init code if you still use it)
// Example:
// const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
// const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))


// --- Main Execution ---
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed");
    showPageContent();
    handleCookieConsent(); // Check initial consent, update checkbox, show banner if needed

    // *** NEW: Add event listener to the WRAPPER ***
    const rememberMeWrapper = document.getElementById(SAVE_LOGIN_WRAPPER_ID);
    const saveLoginCheckbox = document.getElementById(SAVE_LOGIN_CHECKBOX_ID);

    if (rememberMeWrapper && saveLoginCheckbox) {
        rememberMeWrapper.addEventListener('click', (event) => {
            // Check if the checkbox *inside* the wrapper is disabled at the time of click
            if (saveLoginCheckbox.disabled) {
                console.log("Disabled checkbox wrapper clicked.");
                // Prevent the click from trying to toggle the disabled checkbox (though it usually won't anyway)
                event.preventDefault();
                // Show the cookie consent banner
                showCookieBanner();
            }
        });
    } else {
         if (!rememberMeWrapper) console.error(`Remember me wrapper (#${SAVE_LOGIN_WRAPPER_ID}) not found.`);
         if (!saveLoginCheckbox) console.error(`Remember me checkbox (#${SAVE_LOGIN_CHECKBOX_ID}) not found.`);
    }

    // Initialize popovers if you use them
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

});