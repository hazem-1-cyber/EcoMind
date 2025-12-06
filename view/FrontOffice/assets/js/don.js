// don.js - Validation complète SANS HTML5

const form = document.getElementById("don-form");

// === Fonction utilitaire pour afficher les messages ===
function setMsg(id, text, isValid) {
    const msg = document.getElementById(id);
    if (msg) {
        msg.innerHTML = text; // Utiliser innerHTML pour supporter les icônes
        msg.style.color = isValid ? "green" : "red";
        msg.style.fontSize = "0.85em";
        msg.style.marginTop = "4px";
        msg.style.fontWeight = "500";
    }
}

// === Type de don - affiche les champs conditionnels ===
document.getElementById("type-don").addEventListener("change", function () {
    const value = this.value;
    const moneyFields = document.getElementById("money-fields");
    const autreFields = document.getElementById("autre-type-fields");

    // Cacher tous les champs conditionnels et désactiver leurs inputs
    if (moneyFields) {
        moneyFields.style.display = "none";
        moneyFields.querySelectorAll("input, select, textarea").forEach(input => input.setAttribute("disabled", "disabled"));
    }
    if (autreFields) {
        autreFields.style.display = "none";
        autreFields.querySelectorAll("input, select, textarea").forEach(input => input.setAttribute("disabled", "disabled"));
    }

    console.log("Type de don sélectionné:", value); // Debug

    if (value === "money") {
        // Afficher les champs spécifiques pour les dons d'argent
        if (moneyFields) {
            moneyFields.style.display = "block";
            moneyFields.querySelectorAll("input, select, textarea").forEach(input => input.removeAttribute("disabled"));
            console.log("Affichage des champs argent");
            
            // Initialiser le message du montant APRÈS l'affichage des champs
            setTimeout(() => {
                initMontantMessage();
            }, 100);
        }
        setMsg("type-don_error", "✓ Type sélectionné", true);
    } else if (value !== "") {
        // Pour tous les autres types de dons (dynamiques), afficher les champs génériques
        if (autreFields) {
            autreFields.style.display = "block";
            autreFields.querySelectorAll("input, select, textarea").forEach(input => input.removeAttribute("disabled"));
            console.log("Affichage des champs autres types");
        }
        setMsg("type-don_error", "✓ Type sélectionné", true);
    } else {
        setMsg("type-don_error", "✗ Choisissez un type de don", false);
    }
});

// === Fonction pour obtenir le montant minimum ===
function getMinAmount() {
    const montantInput = document.getElementById("custom-amount");
    console.log("🔍 getMinAmount() - Élément trouvé:", montantInput ? "OUI" : "NON");
    
    if (montantInput) {
        const minAttr = montantInput.getAttribute('data-min');
        console.log("🔍 Attribut data-min:", minAttr);
        console.log("🔍 Type de data-min:", typeof minAttr);
        
        if (minAttr) {
            const min = parseFloat(minAttr);
            console.log("🔍 Après parseFloat:", min);
            console.log("🔍 isNaN?:", isNaN(min));
            
            if (!isNaN(min) && min > 0) {
                console.log("✅ Montant minimum récupéré:", min);
                return min;
            }
        } else {
            console.error("❌ data-min est NULL ou vide!");
        }
    } else {
        console.error("❌ Élément custom-amount NON TROUVÉ!");
    }
    
    console.warn("⚠️ FALLBACK: Utilisation de 10 par défaut");
    return 10;
}

// === Fonction pour obtenir la devise ===
function getCurrency() {
    const montantInput = document.getElementById("custom-amount");
    if (montantInput) {
        const formGroup = montantInput.closest('.form-group');
        if (formGroup) {
            const label = formGroup.querySelector('label');
            if (label) {
                const match = label.textContent.match(/\(([^)]+)\)/);
                console.log("Devise extraite:", match ? match[1] : 'TND');
                if (match) {
                    return match[1];
                }
            }
        }
    }
    console.warn("Devise non trouvée, utilisation de TND par défaut");
    return 'TND';
}

// === Montant - boutons prédéfinis ===
document.querySelectorAll(".amount-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        const montantInput = document.getElementById("custom-amount");
        if (montantInput) {
            const minAmount = getMinAmount();
            const selectedAmount = parseFloat(this.dataset.value);
            const currency = getCurrency();
            
            console.log(`Bouton cliqué: ${selectedAmount}, Min: ${minAmount}, Devise: ${currency}`);
            
            // Vérifier si le montant du bouton est valide
            if (selectedAmount >= minAmount) {
                document.querySelectorAll(".amount-btn").forEach(b => b.classList.remove("active"));
                this.classList.add("active");
                montantInput.value = this.dataset.value;
                montantInput.style.borderColor = "#28a745";
                setMsg("montant_error", "✓ Montant sélectionné: " + this.dataset.value + " " + currency, true);
            } else {
                // Désactiver visuellement le bouton si montant < minimum
                setMsg("montant_error", `✗ Ce montant est inférieur au minimum (${minAmount} ${currency})`, false);
                this.style.opacity = "0.5";
                this.style.cursor = "not-allowed";
            }
        }
    });
});

// === Fonction pour initialiser le message du montant ===
function initMontantMessage() {
    const customAmountInput = document.getElementById("custom-amount");
    if (customAmountInput) {
        const minAmount = getMinAmount();
        const currency = getCurrency();
        const infoMsg = document.getElementById("montant_error");
        
        console.log("=== INITIALISATION MESSAGE MONTANT ===");
        console.log("Montant minimum:", minAmount);
        console.log("Devise:", currency);
        
        if (infoMsg) {
            infoMsg.innerHTML = `<i class="fas fa-info-circle"></i> Montant minimum: ${minAmount} ${currency}`;
            infoMsg.style.color = "#6c757d";
            infoMsg.style.fontSize = "0.9em";
            infoMsg.style.fontWeight = "normal";
        }
    }
}

// === Montant personnalisé ===
const customAmountInput = document.getElementById("custom-amount");
if (customAmountInput) {
    // Afficher le message d'information au chargement
    initMontantMessage();
    
    // Validation en temps réel
    customAmountInput.addEventListener("input", function () {
        const minAmount = getMinAmount();
        const currency = getCurrency();
        const value = this.value.replace(/[^0-9.]/g, "");
        this.value = value;
        const num = parseFloat(value);

        console.log(`Input montant: ${num}, Min: ${minAmount}, Devise: ${currency}`);

        const submitBtn = document.getElementById("submit-btn");

        if (value === "") {
            // Afficher le message d'information
            setMsg("montant_error", `<i class="fas fa-info-circle"></i> Montant minimum: ${minAmount} ${currency}`, false);
            const errorMsg = document.getElementById("montant_error");
            if (errorMsg) {
                errorMsg.style.color = "#6c757d";
                errorMsg.style.fontSize = "0.9em";
                errorMsg.style.fontWeight = "normal";
                errorMsg.style.backgroundColor = "transparent";
                errorMsg.style.padding = "0";
            }
            this.style.borderColor = "";
            if (submitBtn) submitBtn.disabled = false;
        } else if (num >= minAmount) {
            setMsg("montant_error", "✓ Montant valide", true);
            this.style.borderColor = "#28a745";
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
                submitBtn.style.cursor = "pointer";
            }
            document.querySelectorAll(".amount-btn").forEach(b => b.classList.remove("active"));
        } else {
            setMsg("montant_error", `✗ Le montant doit être au moins ${minAmount} ${currency}`, false);
            this.style.borderColor = "#dc3545";
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = "0.5";
                submitBtn.style.cursor = "not-allowed";
            }
        }
    });
    
    // Validation au blur (perte de focus)
    customAmountInput.addEventListener("blur", function () {
        const minAmount = getMinAmount();
        const currency = getCurrency();
        const value = this.value;
        const num = parseFloat(value);
        
        console.log(`Blur - Montant: ${num}, Min: ${minAmount}`);
        
        if (value && num < minAmount) {
            // Afficher une alerte plus visible
            const errorMsg = document.getElementById("montant_error");
            if (errorMsg) {
                errorMsg.style.fontSize = "0.95em";
                errorMsg.style.fontWeight = "bold";
                errorMsg.style.padding = "10px";
                errorMsg.style.backgroundColor = "#ffe6e6";
                errorMsg.style.borderRadius = "4px";
                errorMsg.style.display = "block";
                errorMsg.style.marginTop = "10px";
                errorMsg.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Le montant doit être au moins ${minAmount} ${currency}`;
            }
        }
    });
}

// === Validation Ville ===
const villeAutreInput = document.getElementById("ville-autre");
if (villeAutreInput) {
    villeAutreInput.addEventListener("keyup", function () {
        const value = this.value.trim();
        if (value.length >= 2 && /^[a-zA-ZÀ-ÿ\s-]+$/.test(value)) {
            setMsg("ville-autre_error", "✓ Ville valide", true);
        } else {
            setMsg("ville-autre_error", "✗ Minimum 2 lettres", false);
        }
    });
}

// === Validation Code Postal (4 chiffres) ===
const cpAutreInput = document.getElementById("cp-autre");
if (cpAutreInput) {
    cpAutreInput.addEventListener("keyup", function () {
        const value = this.value.replace(/[^0-9]/g, "");
        this.value = value;
        if (/^\d{4}$/.test(value)) {
            setMsg("cp-autre_error", "✓ Code postal valide", true);
        } else {
            setMsg("cp-autre_error", "✗ 4 chiffres requis", false);
        }
    });
}

// === Validation Localisation (optionnel mais doit être URL) ===
const localisationAutreInput = document.getElementById("localisation-autre");
if (localisationAutreInput) {
    localisationAutreInput.addEventListener("keyup", function () {
        const value = this.value.trim();
        if (value === "" || value.length >= 10) {
            setMsg("localisation-autre_error", value ? "✓ Lien valide" : "", true);
        } else {
            setMsg("localisation-autre_error", "✗ Lien incomplet", false);
        }
    });
}

// === Validation Téléphone (8 chiffres) ===
const telAutreInput = document.getElementById("tel-autre");
if (telAutreInput) {
    telAutreInput.addEventListener("keyup", function () {
        const value = this.value.replace(/[^0-9]/g, "");
        this.value = value;
        if (/^\d{8}$/.test(value)) {
            setMsg("tel-autre_error", "✓ Téléphone valide", true);
        } else {
            setMsg("tel-autre_error", "✗ 8 chiffres requis (ex: 98765432)", false);
        }
    });
}

// === Validation Description ===
const descriptionInput = document.getElementById("description-don");
if (descriptionInput) {
    descriptionInput.addEventListener("keyup", function () {
        const value = this.value.trim();
        if (value.length >= 10) {
            setMsg("description_error", "✓ Description valide", true);
        } else {
            setMsg("description_error", "✗ Minimum 10 caractères", false);
        }
    });
}

// === Validation Association ===
const associationSelect = document.getElementById("association");
if (associationSelect) {
    associationSelect.addEventListener("change", function () {
        const value = this.value;
        if (value) {
            setMsg("association_error", "✓ Association sélectionnée", true);
        } else {
            setMsg("association_error", "✗ Sélectionnez une association", false);
        }
    });
}

// === Validation Email (au blur) ===
const emailInput = document.getElementById("email");
if (emailInput) {
    emailInput.addEventListener("blur", function () {
        const value = this.value.trim();
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (regex.test(value)) {
            setMsg("email_error", "✓ Email valide", true);
        } else {
            setMsg("email_error", "✗ Format email invalide", false);
        }
    });
}

// === VALIDATION FINALE À LA SOUMISSION (sans alert) ===
form.addEventListener("submit", function (event) {
    // TOUJOURS empêcher la soumission par défaut
    event.preventDefault();
    event.stopPropagation();
    
    let isValid = true;
    const typeDon = document.getElementById("type-don").value;

    console.log("=== DÉBUT VALIDATION FORMULAIRE ===");
    console.log("Type de don:", typeDon);

    // Vérifier Type de don
    if (!typeDon) {
        setMsg("type-don_error", "✗ Type de don requis", false);
        isValid = false;
        console.log("❌ Type de don manquant");
    }

    // Si type = money
    if (typeDon === "money") {
        const montantInput = document.getElementById("custom-amount");
        const montant = montantInput ? montantInput.value : "";
        const minAmount = getMinAmount();
        const currency = getCurrency();
        
        console.log(`Validation montant - Saisi: ${montant}, Min requis: ${minAmount}, Devise: ${currency}`);
        
        if (!montant || montant.trim() === "") {
            console.log("❌ Montant vide");
            setMsg("montant_error", `✗ Veuillez entrer un montant`, false);
            isValid = false;
        } else {
            const montantNum = parseFloat(montant);
            console.log(`Montant numérique: ${montantNum}`);
            
            if (isNaN(montantNum)) {
                console.log("❌ Montant invalide (pas un nombre)");
                setMsg("montant_error", `✗ Montant invalide`, false);
                isValid = false;
            } else if (montantNum < minAmount) {
                console.log(`❌ Montant trop petit: ${montantNum} < ${minAmount}`);
                setMsg("montant_error", `✗ Le montant doit être au moins ${minAmount} ${currency}`, false);
                isValid = false;
                
                // Faire défiler jusqu'au champ montant et le mettre en focus
                montantInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => {
                    montantInput.focus();
                    montantInput.select();
                }, 300);
                montantInput.style.borderColor = 'red';
                montantInput.style.borderWidth = '3px';
                
                // Afficher une alerte visuelle TRÈS visible
                const errorMsg = document.getElementById("montant_error");
                if (errorMsg) {
                    errorMsg.style.fontSize = "1.1em";
                    errorMsg.style.fontWeight = "bold";
                    errorMsg.style.animation = "shake 0.5s";
                    errorMsg.style.backgroundColor = "#ff4444";
                    errorMsg.style.color = "white";
                    errorMsg.style.padding = "15px";
                    errorMsg.style.borderRadius = "8px";
                    errorMsg.style.display = "block";
                    errorMsg.style.marginTop = "10px";
                    errorMsg.innerHTML = `<i class="fas fa-exclamation-circle"></i> ATTENTION: Le montant minimum est ${minAmount} ${currency}`;
                }
                
                // BLOQUER COMPLÈTEMENT - Ne jamais soumettre
                console.log("🛑 SOUMISSION BLOQUÉE - Montant invalide");
                return false;
            } else {
                console.log(`✅ Montant valide: ${montantNum} >= ${minAmount}`);
            }
        }
        
        // Si montant invalide, ARRÊTER ICI
        if (!isValid) {
            console.log("🛑 SOUMISSION BLOQUÉE - Validation échouée");
            return false;
        }
    }

    // Si type != money (tous les autres types de dons nécessitent les champs génériques)
    if (typeDon && typeDon !== "money") {
        const ville = document.getElementById("ville-autre").value.trim();
        const cp = document.getElementById("cp-autre").value.trim();
        const tel = document.getElementById("tel-autre").value.trim();

        if (!ville || ville.length < 2) {
            setMsg("ville-autre_error", "✗ Ville requise", false);
            isValid = false;
        }
        if (!/^\d{4}$/.test(cp)) {
            setMsg("cp-autre_error", "✗ Code postal invalide", false);
            isValid = false;
        }
        if (!/^\d{8}$/.test(tel)) {
            setMsg("tel-autre_error", "✗ Téléphone invalide", false);
            isValid = false;
        }

        // Description requise pour tous les types de dons non-argent
        const description = document.getElementById("description-don").value.trim();
        if (!description || description.length < 10) {
            setMsg("description_error", "✗ Description requise (min 10 caractères)", false);
            isValid = false;
        }
    }

    // Vérifier Association
    if (!document.getElementById("association").value) {
        setMsg("association_error", "✗ Association requise", false);
        isValid = false;
    }

    // Vérifier Email
    const email = document.getElementById("email").value.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setMsg("email_error", "✗ Email invalide", false);
        isValid = false;
    }

    console.log("=== FIN VALIDATION ===");
    console.log("Résultat validation:", isValid ? "✅ VALIDE" : "❌ INVALIDE");

    // Si PAS valide, BLOQUER COMPLÈTEMENT
    if (!isValid) {
        console.log("🛑 SOUMISSION BLOQUÉE - Formulaire invalide");
        
        // Faire défiler jusqu'à la première erreur
        const firstError = document.querySelector('.error-msg[style*="color: red"], .error-msg[style*="color:red"]');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // ARRÊTER COMPLÈTEMENT
        return false;
    }

    // Si VALIDE, soumettre le formulaire
    console.log("✅ SOUMISSION AUTORISÉE - Formulaire valide");
    
    // Si don d'argent, s'assurer que le champ montant et livraison sont correctement renseignés
    if (typeDon === 'money') {
        const montantInput = document.getElementById('custom-amount');
        const hiddenLivraison = document.querySelector('input[name="livraison"]');
        if (montantInput) {
            // garantir que le montant est transmis avec le bon nom
            montantInput.name = 'montant';
            console.log("Montant à soumettre:", montantInput.value);
        }
        if (hiddenLivraison) {
            hiddenLivraison.value = 'en_ligne';
            hiddenLivraison.removeAttribute('disabled');
        }
    }

    // SOUMETTRE LE FORMULAIRE
    console.log("📤 Soumission du formulaire...");
    this.submit();
});

// Ajouter l'animation shake pour les erreurs
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;
document.head.appendChild(style);