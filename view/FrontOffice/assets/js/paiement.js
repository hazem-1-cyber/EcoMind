// paiement.js - Validation complète du formulaire de paiement (JavaScript uniquement)

const form = document.getElementById("payment-form");

// === Fonction utilitaire pour afficher les messages ===
function setMsg(id, text, isValid) {
    const msg = document.getElementById(id);
    if (msg) {
        msg.innerText = text;
        msg.style.color = isValid ? "green" : "red";
        msg.style.fontSize = "0.85em";
        msg.style.marginTop = "4px";
        msg.style.fontWeight = "500";
    }
}

// === Formatage automatique du numéro de carte ===
const cardNumberInput = document.getElementById("card-number");
if (cardNumberInput) {
    cardNumberInput.addEventListener("focus", function() {
        setMsg("card-number_error", "", true);
    });

    cardNumberInput.addEventListener("input", function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
        if (value.length > 16) value = value.slice(0, 16);
        value = value.replace(/(.{4})/g, '$1 ').trim();
        e.target.value = value;
        
        // Validation en temps réel
        const cleanValue = value.replace(/\s/g, '');
        if (cleanValue.length === 16) {
            setMsg("card-number_error", "✓ Numéro de carte valide", true);
        }
    });

    cardNumberInput.addEventListener("blur", function() {
        const value = this.value.replace(/\s/g, '');
        if (value.length === 0) {
            setMsg("card-number_error", "✗ Numéro de carte requis", false);
        } else if (!/^\d{16}$/.test(value)) {
            setMsg("card-number_error", "✗ Le numéro de carte doit contenir exactement 16 chiffres", false);
        } else {
            setMsg("card-number_error", "✓ Numéro de carte valide", true);
        }
    });
}

// === Validation formelle uniquement (paiement non sécurisé) ===
// Pas d'algorithme de Luhn - accepte tous les numéros de format correct

// === Validation du nom du titulaire ===
const cardHolderInput = document.getElementById("card-holder");
if (cardHolderInput) {
    cardHolderInput.addEventListener("input", function() {
        this.value = this.value.toUpperCase();
    });

    cardHolderInput.addEventListener("blur", function() {
        const value = this.value.trim();
        if (value.length === 0) {
            setMsg("card-holder_error", "✗ Nom du titulaire requis", false);
        } else if (value.length < 3) {
            setMsg("card-holder_error", "✗ Nom trop court (minimum 3 caractères)", false);
        } else if (!/^[A-ZÀ-ÿ\s-]+$/i.test(value)) {
            setMsg("card-holder_error", "✗ Nom invalide (lettres uniquement)", false);
        } else {
            setMsg("card-holder_error", "✓ Nom valide", true);
        }
    });
}

// === Formatage et validation de la date d'expiration ===
const expiryInput = document.getElementById("expiry");
if (expiryInput) {
    expiryInput.addEventListener("input", function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        e.target.value = value;
    });

    expiryInput.addEventListener("blur", function() {
        const value = this.value;
        const regex = /^(\d{2})\/(\d{2})$/;
        const match = value.match(regex);
        
        if (!value) {
            setMsg("expiry_error", "✗ Date d'expiration requise", false);
        } else if (!match) {
            setMsg("expiry_error", "✗ Format invalide (MM/AA)", false);
        } else {
            const month = parseInt(match[1]);
            if (month < 1 || month > 12) {
                setMsg("expiry_error", "✗ Mois invalide (01-12)", false);
            } else {
                setMsg("expiry_error", "✓ Date valide", true);
            }
        }
    });
}

// === Validation du CVV ===
const cvvInput = document.getElementById("cvv");
if (cvvInput) {
    cvvInput.addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, '');
    });

    cvvInput.addEventListener("blur", function() {
        const value = this.value;
        if (value.length === 0) {
            setMsg("cvv_error", "✗ CVV requis", false);
        } else if (!/^\d{3,4}$/.test(value)) {
            setMsg("cvv_error", "✗ CVV invalide (3-4 chiffres)", false);
        } else {
            setMsg("cvv_error", "✓ CVV valide", true);
        }
    });
}

// === Validation de l'acceptation des conditions ===
const acceptTermsCheckbox = document.getElementById("accept-terms");
if (acceptTermsCheckbox) {
    acceptTermsCheckbox.addEventListener("change", function() {
        if (this.checked) {
            setMsg("accept-terms_error", "✓ Conditions acceptées", true);
        } else {
            setMsg("accept-terms_error", "✗ Vous devez accepter les conditions", false);
        }
    });
}

// === VALIDATION FINALE À LA SOUMISSION ===
if (form) {
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        
        let isValid = true;

        // Valider numéro de carte (16 chiffres uniquement)
        const cardNumber = cardNumberInput.value.replace(/\s/g, '');
        if (!cardNumber || !/^\d{16}$/.test(cardNumber)) {
            setMsg("card-number_error", "✗ Le numéro de carte doit contenir exactement 16 chiffres", false);
            isValid = false;
        }

        // Valider nom du titulaire
        const cardHolder = cardHolderInput.value.trim();
        if (!cardHolder || cardHolder.length < 3) {
            setMsg("card-holder_error", "✗ Nom du titulaire invalide", false);
            isValid = false;
        }

        // Valider date d'expiration (format uniquement)
        const expiry = expiryInput.value;
        const regex = /^(\d{2})\/(\d{2})$/;
        const match = expiry.match(regex);
        if (!match) {
            setMsg("expiry_error", "✗ Date d'expiration invalide (format MM/AA)", false);
            isValid = false;
        } else {
            const month = parseInt(match[1]);
            if (month < 1 || month > 12) {
                setMsg("expiry_error", "✗ Mois invalide (01-12)", false);
                isValid = false;
            }
        }

        // Valider CVV
        const cvv = cvvInput.value;
        if (!/^\d{3,4}$/.test(cvv)) {
            setMsg("cvv_error", "✗ CVV invalide", false);
            isValid = false;
        }

        // Valider acceptation des conditions
        if (!acceptTermsCheckbox.checked) {
            setMsg("accept-terms_error", "✗ Vous devez accepter les conditions", false);
            isValid = false;
        }

        // Si tout est valide, soumettre le formulaire
        if (isValid) {
            this.submit();
        }
    });
}
