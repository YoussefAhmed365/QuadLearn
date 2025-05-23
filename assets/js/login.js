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

// // --- Constants ---
// const COOKIE_CONSENT_NAME = 'userCookieConsent';
// const COOKIE_EXPIRY_DAYS = 30;
// const SAVE_LOGIN_CHECKBOX_ID = 'rememberMe'; // ID of your checkbox

// // --- Cookie Helper Functions ---
// function setCookie(name, value, days) {
//     let expires = "";
//     if (days) {
//         const date = new Date();
//         date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
//         expires = "; expires=" + date.toUTCString();
//     }
//     // Ensure cookie value is properly encoded
//     // **REMOVED HttpOnly** for the consent cookie so getCookie can read it.
//     // Keep Secure and SameSite.
//     document.cookie = name + "=" + (encodeURIComponent(value) || "") + expires + "; path=/; Secure; SameSite=Lax";
// }

// function getCookie(name) {
//     const nameEQ = name + "=";
//     const ca = document.cookie.split(';');
//     for (let i = 0; i < ca.length; i++) {
//         let c = ca[i];
//         while (c.charAt(0) === ' ') c = c.substring(1, c.length); // Trim leading space
//         if (c.indexOf(nameEQ) === 0) {
//             try {
//                 // Decode the value
//                 return decodeURIComponent(c.substring(nameEQ.length, c.length));
//             } catch (e) {
//                 console.error(`Error decoding cookie "${name}":`, e);
//                 return null; // Return null if decoding fails
//             }
//         }
//     }
//     return null; // Cookie not found
// }


// // --- NEW: Function to Update Save Login Checkbox State ---
// function updateSaveLoginCheckboxState() {
//     const saveLoginCheckbox = document.getElementById(SAVE_LOGIN_CHECKBOX_ID);
//     const consent = getCookie(COOKIE_CONSENT_NAME);

//     if (!saveLoginCheckbox) {
//         // console.warn(`Checkbox with ID "${SAVE_LOGIN_CHECKBOX_ID}" not found.`);
//         return; // Exit if checkbox doesn't exist
//     }

//     // Check if consent is explicitly 'true' (accepted)
//     if (consent === "true") {
//         saveLoginCheckbox.disabled = false; // Enable the checkbox
//         // Optional: You could re-check it if it was previously saved,
//         // but the current requirement is just to enable/disable it.
//         console.log("Cookie consent accepted. 'Remember me' checkbox enabled.");
//     } else {
//         // If consent is 'false' (rejected) or not set yet (null)
//         saveLoginCheckbox.disabled = true;  // Disable the checkbox
//         saveLoginCheckbox.checked = false; // Ensure it's unchecked
//         console.log("Cookie consent not accepted or rejected. 'Remember me' checkbox disabled and unchecked.");
//         // Optional: If you have saved credentials using this checkbox before,
//         // you might want to clear them here if consent is revoked.
//     }
// }


// // --- Initialization Functions ---

// function showPageContent() {
//     document.body.classList.add('content-ready');
//     console.log("Page content displayed, loading indicator hidden.");
// }

// function handleCookieConsent() {
//     const cookiesCard = document.getElementById("cookie");
//     const acceptButton = document.getElementById("acceptCookies");
//     const rejectButton = document.getElementById("rejectCookies");

//     if (!cookiesCard) {
//         console.warn("Cookie consent card element (#cookie) not found.");
//         // Even if the banner isn't found, update checkbox based on existing cookie
//         updateSaveLoginCheckboxState();
//         return;
//     }

//     const consent = getCookie(COOKIE_CONSENT_NAME);

//     // Show banner only if consent hasn't been given or explicitly rejected
//     if (consent !== "true" && consent !== "false") {
//         cookiesCard.style.display = "block";

//         acceptButton?.addEventListener("click", () => {
//             setCookie(COOKIE_CONSENT_NAME, "true", COOKIE_EXPIRY_DAYS);
//             cookiesCard.style.display = "none";
//             console.log("User accepted cookies");
//             updateSaveLoginCheckboxState(); // *** Update checkbox state ***
//             // Optionally: trigger actions that depend on cookie acceptance
//         });

//         rejectButton?.addEventListener("click", () => {
//             setCookie(COOKIE_CONSENT_NAME, "false", COOKIE_EXPIRY_DAYS);
//             cookiesCard.style.display = "none";
//             console.log("User rejected cookies");
//             updateSaveLoginCheckboxState(); // *** Update checkbox state ***
//             // Optionally: disable features that require cookies
//         });

//     } else {
//         cookiesCard.style.display = "none";
//         console.log(`Cookie consent already set: ${consent}`);
//         // Update checkbox state based on existing consent on page load
//         updateSaveLoginCheckboxState(); // *** Update checkbox state ***
//     }
// }

// // Show cookies consent on click on save credentials checkbox's dialog
// const disabledSaveLoginCheckbox = document.getElementById(SAVE_LOGIN_CHECKBOX_ID);

// if (disabledSaveLoginCheckbox.disabled) {
//     disabledSaveLoginCheckbox.addEventListener('click', (event) => {
//         // Show the cookies consent dialog if the checkbox is disabled
//         handleCookieConsent();
//     });
// }

// // --- Bootstrap Popover Initialization ---
// const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
// const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

// // --- Main Execution ---

// document.addEventListener('DOMContentLoaded', () => {
//     console.log("DOM fully loaded and parsed");
//     showPageContent();
//     handleCookieConsent(); // Initializes consent logic AND updates the checkbox initially
//     // No need for a separate call to updateSaveLoginCheckboxState here,
//     // as handleCookieConsent now calls it internally in all relevant scenarios.
// });