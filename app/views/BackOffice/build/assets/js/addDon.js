// addDon.js - Validation en temps réel (STYLE ESPrIT comme addBook.js)

const form = document.getElementById("don-form");

// Type de don
document.getElementById("type-don").addEventListener("change", function () {
    const msg = document.getElementById("type-don_error");
    const value = this.value;
    const montantField = document.getElementById("montant-field");

    if (["money", "panneau_solaire", "materiel", "autre"].includes(value)) {
        msg.style.color = "green";
        msg.innerText = "Type valide";
        montantField.style.display = value === "money" ? "block" : "none";
    } else {
        msg.style.color = "red";
        msg.innerText = "Veuillez choisir un type";
    }
});

// Montant
document.getElementById("montant").addEventListener("keyup", function () {
    const msg = document.getElementById("montant_error");
    const value = parseFloat(this.value);
    if (document.getElementById("type-don").value !== "money") {
        msg.innerText = "";
        return;
    }
    if (isNaN(value) || value < 10) {
        msg.style.color = "red";
        msg.innerText = "Montant ≥ 10 TND requis";
    } else {
        msg.style.color = "green";
        msg.innerText = "Montant valide";
    }
});

// Email
document.getElementById("email").addEventListener("blur", function () {
    const msg = document.getElementById("email_error");
    const value = this.value.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (regex.test(value)) {
        msg.style.color = "green";
        msg.innerText = "Email valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "Email invalide";
    }
});

// Association
document.getElementById("association").addEventListener("change", function () {
    const msg = document.getElementById("association_error");
    if (this.value) {
        msg.style.color = "green";
        msg.innerText = "Association valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "Choisissez une association";
    }
});

// Livraison
document.querySelectorAll('input[name="livraison"]').forEach(radio => {
    radio.addEventListener("change", function () {
        const msg = document.getElementById("livraison_error");
        msg.style.color = "green";
        msg.innerText = this.value === "domicile" ? "Livraison à domicile" : "Point relais";
    });
});

// Ville
document.getElementById("ville").addEventListener("keyup", function () {
    const msg = document.getElementById("ville_error");
    const value = this.value.trim();
    const regex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{2,}$/;
    if (regex.test(value)) {
        msg.style.color = "green";
        msg.innerText = "Ville valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "≥ 2 lettres";
    }
});

// Code postal
document.getElementById("cp").addEventListener("keyup", function () {
    const msg = document.getElementById("cp_error");
    const value = this.value;
    const regex = /^\d{4}$/;
    if (regex.test(value)) {
        msg.style.color = "green";
        msg.innerText = "CP valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "4 chiffres";
    }
});

// Adresse
document.getElementById("adresse").addEventListener("keyup", function () {
    const msg = document.getElementById("adresse_error");
    if (this.value.length >= 5) {
        msg.style.color = "green";
        msg.innerText = "Adresse valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "≥ 5 caractères";
    }
});

// Téléphone
document.getElementById("tel").addEventListener("keyup", function () {
    const msg = document.getElementById("tel_error");
    const value = this.value;
    const regex = /^\d{8}$/;
    if (regex.test(value)) {
        msg.style.color = "green";
        msg.innerText = "Téléphone valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "8 chiffres";
    }
});

// Validation finale
form.addEventListener("submit", function (e) {
    e.preventDefault();
    let isValid = true;

    document.querySelectorAll('.error-msg').forEach(span => {
        if (span.style.color === "red") isValid = false;
    });

    if (!document.querySelector('input[name="livraison"]:checked')) {
        document.getElementById("livraison_error").style.color = "red";
        document.getElementById("livraison_error").innerText = "Choisissez un mode";
        isValid = false;
    }

    if (isValid) {
        alert("Don ajouté avec succès !");
        this.submit();
    } else {
        alert("Veuillez corriger les erreurs.");
    }
});