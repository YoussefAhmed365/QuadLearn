
// --- Constants ---
const COOKIE_CONSENT_NAME = 'userCookieConsent'; // Use a more descriptive name
const COOKIE_EXPIRY_DAYS = 30;

// --- Cookie Helper Functions ---
function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    // Ensure cookie value is properly encoded
    document.cookie = name + "=" + (encodeURIComponent(value) || "") + expires + "; path=/; Secure; HttpOnly; SameSite=Lax"; // Added SameSite
}

function getCookie(name) {
    const nameEQ = name + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length); // Trim leading space
        if (c.indexOf(nameEQ) === 0) {
            try {
                // Decode the value
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
            } catch (e) {
                console.error(`Error decoding cookie "${name}":`, e);
                return null; // Return null if decoding fails
            }
        }
    }
    return null; // Cookie not found
}

// --- Initialization Functions ---

function showPageContent() {
    // Add class to body instead of directly manipulating styles
    document.body.classList.add('content-ready');
    console.log("Page content displayed, loading indicator hidden.");
}

function handleCookieConsent() {
    const cookiesCard = document.getElementById("cookie");
    const acceptButton = document.getElementById("acceptCookies");
    const rejectButton = document.getElementById("rejectCookies");
    
    // Ensure the main card element exists
    if (!cookiesCard) {
        console.warn("Cookie consent card element (#cookie) not found.");
        return; // Exit if the card isn't there
    }
    
    const consent = getCookie(COOKIE_CONSENT_NAME);
    
    // Show banner only if consent hasn't been given or explicitly rejected
    if (consent !== "true" && consent !== "false") {
        cookiesCard.style.display = "block"; // Show the banner
        
        // Add event listeners only if buttons exist
        acceptButton?.addEventListener("click", () => {
            setCookie(COOKIE_CONSENT_NAME, "true", COOKIE_EXPIRY_DAYS);
            cookiesCard.style.display = "none";
            console.log("User accepted cookies");
            // Optionally: trigger actions that depend on cookie acceptance
        });
        
        rejectButton?.addEventListener("click", () => {
            setCookie(COOKIE_CONSENT_NAME, "false", COOKIE_EXPIRY_DAYS);
            cookiesCard.style.display = "none";
            console.log("User rejected cookies");
            // Optionally: disable features that require cookies
        });
        
    } else {
        cookiesCard.style.display = "none"; // Ensure it's hidden if consent exists
        console.log(`Cookie consent already set: ${consent}`);
    }
}

// --- Main Execution ---

// Use DOMContentLoaded for faster readiness for DOM manipulation
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed");
    showPageContent();      // Show content / hide loader
    handleCookieConsent();  // Initialize cookie consent logic
});