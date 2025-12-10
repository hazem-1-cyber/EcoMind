const formulaire = document.getElementById('form-eco');

// Validation en temps réel sur chaque champ
formulaire.addEventListener('blur', function(e) {
    if (e.target.matches('input, select, textarea')) {
        validerChamp(e.target);
    }
}, true);

// Validation à la soumission
formulaire.addEventListener('submit', function (event) {
    event.preventDefault();
    reinitialiserMessages();

    if (validerToutLeFormulaire()) {
        traiterFormulaire();
    } else {
        faireDefilerVersErreur();
        afficherMessageGlobal("Veuillez corriger les erreurs.", "error");
    }
});

// Réinitialiser les messages d'erreur lors du reset
formulaire.addEventListener('reset', function() {
    setTimeout(() => {
        reinitialiserMessages();
    }, 10);
});

function validerChamp(champ) {
    const valeur = champ.value.trim();
    const id = champ.id;
    const name = champ.name;

    // Champs obligatoires (tous sauf email et nb_personnes)
    const champsObligatoires = ['douche_freq', 'douche_duree', 'chauffage', 'temp_hiver', 'transport_travail', 'distance_travail'];
    
    if (champsObligatoires.includes(name) && valeur === "") {
        afficherErreur(champ, "Ce champ est obligatoire.");
        return false;
    }

    // Si le champ est vide et non obligatoire, on le considère comme valide
    if (valeur === "" && !champsObligatoires.includes(name)) {
        afficherSucces(champ);
        return true;
    }

    // Validation email
    if (id === "email" && valeur !== "") {
        const regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!regexEmail.test(valeur)) {
            afficherErreur(champ, "Veuillez entrer un email valide (ex: nom@domaine.com)");
            return false;
        }
        afficherSucces(champ);
        return true;
    }

    // Validation select chauffage
    if (id === "chauffage") {
        if (valeur === "") {
            afficherErreur(champ, "Veuillez choisir un type de chauffage.");
            return false;
        }
        afficherSucces(champ);
        return true;
    }

    // Validation transport
    if (id === "transport-travail") {
        // Vérifier si c'est uniquement des chiffres
        if (/^\d+$/.test(valeur)) {
            afficherErreur(champ, "Veuillez écrire un moyen de transport (voiture, bus, vélo, train, marche...)");
            return false;
        }

        // Vérifier si c'est trop court
        if (valeur.length < 3) {
            afficherErreur(champ, "Le moyen de transport doit contenir au moins 3 caractères.");
            return false;
        }

        // Liste de transports valides
        const transportsValides = ["voiture", "bus", "métro", "metro", "tram", "train", "vélo", "velo", "marche", "trottinette", "moto", "covoiturage", "télétravail", "teletravail", "avion", "taxi"];
        const valeurLower = valeur.toLowerCase();
        const estValide = transportsValides.some(t => valeurLower.includes(t));

        if (!estValide) {
            afficherErreur(champ, "Veuillez indiquer un moyen de transport valide (voiture, bus, vélo, train, marche...)");
            return false;
        }

        afficherSucces(champ);
        return true;
    }

    // Validation nombres
    const champsNumeriques = ["douche-freq", "douche-duree", "temp-hiver", "distance-travail", "nb-personnes"];

    if (champsNumeriques.includes(id)) {
        // Vérifier si c'est un nombre
        if (isNaN(valeur) || valeur === "") {
            afficherErreur(champ, "Veuillez entrer un nombre valide.");
            return false;
        }

        const nombre = parseFloat(valeur);

        // Vérifier si c'est positif
        if (nombre < 0) {
            afficherErreur(champ, "Le nombre doit être positif.");
            return false;
        }

        // Validations spécifiques
        if (id === "douche-freq" && (nombre < 0 || nombre > 50)) {
            afficherErreur(champ, "Veuillez entrer un nombre entre 0 et 50.");
            return false;
        }

        if (id === "douche-duree" && (nombre < 0 || nombre > 120)) {
            afficherErreur(champ, "Veuillez entrer une durée entre 0 et 120 minutes.");
            return false;
        }

        if (id === "temp-hiver" && (nombre < -40 || nombre > 50)) {
            afficherErreur(champ, "Veuillez entrer une température entre -40°C et 50°C.");
            return false;
        }

        if (id === "distance-travail" && nombre > 1000) {
            afficherErreur(champ, "La distance semble trop élevée (max 1000 km).");
            return false;
        }

        if (id === "nb-personnes" && valeur !== "") {
            if (!Number.isInteger(nombre) || nombre < 1 || nombre > 20) {
                afficherErreur(champ, "Veuillez entrer un nombre entier entre 1 et 20.");
                return false;
            }
        }

        afficherSucces(champ);
        return true;
    }

    afficherSucces(champ);
    return true;
}

// les fonctions d'affichage 

function afficherErreur(champ, message) {
    const ancien = champ.parentElement.querySelector('.message-erreur');
    if (ancien) ancien.remove();

    const msg = document.createElement('div');
    msg.className = "message-erreur";
    msg.textContent = message;
    msg.style.cssText = "color:#ff4d4d;font-size:0.9em;margin-top:5px;animation:slideDown 0.3s;";
    champ.parentElement.appendChild(msg);
    champ.style.borderColor = "#ff4d4d";
    champ.style.boxShadow = "0 0 5px rgba(255,0,0,0.3)";
}

function afficherSucces(champ) {
    const ancien = champ.parentElement.querySelector('.message-erreur');
    if (ancien) ancien.remove();
    champ.style.borderColor = "#A8E6CF";
    champ.style.boxShadow = "0 0 5px rgba(168,230,207,0.5)";
}

function reinitialiserMessages() {
    document.querySelectorAll('.message-erreur, .message-global').forEach(el => el.remove());
    document.querySelectorAll('input, select, textarea').forEach(el => {
        el.style.borderColor = "";
        el.style.boxShadow = "";
    });
}

function validerToutLeFormulaire() {
    let valide = true;
    const champs = formulaire.querySelectorAll('input, select, textarea');
    
    champs.forEach(champ => {
        // Valider chaque champ
        if (!validerChamp(champ)) {
            valide = false;
        }
    });
    
    return valide;
}

function faireDefilerVersErreur() {
    const erreur = document.querySelector('.message-erreur');
    if (erreur) erreur.closest('div, section, .form-group').scrollIntoView({ behavior: "smooth", block: "center" });
}

function traiterFormulaire() {
    // Soumettre le formulaire normalement (sans fetch)
    formulaire.submit();
}

function afficherMessageGlobal(texte, type) {
    const ancien = document.querySelector(".message-global");
    if (ancien) ancien.remove();

    const msg = document.createElement("div");
    msg.className = "message-global";
    msg.textContent = texte;
    msg.style.cssText = `
        padding:15px 30px; border-radius:12px; font-weight:600; text-align:center;
        position:fixed; top:20px; left:50%; transform:translateX(-50%);
        min-width:320px; z-index:10000; animation:slideDown 0.4s;
        background:${type === "success" ? "#A8E6CF" : "#ff4444"};
        color:${type === "success" ? "#013220" : "white"};
        box-shadow:0 10px 30px rgba(0,0,0,0.2);
    `;
    document.body.appendChild(msg);

    setTimeout(() => {
        msg.style.animation = "slideUp 0.4s forwards";
        setTimeout(() => msg.remove(), 400);
    }, 4000);
}

// Animations
const style = document.createElement("style");
style.textContent = `
@keyframes slideDown { from {opacity:0;transform:translateY(-30px);} to {opacity:1;transform:translateY(0);} }
@keyframes slideUp { from {opacity:1;transform:translateY(0);} to {opacity:0;transform:translateY(-30px);} }
`;
document.head.appendChild(style);