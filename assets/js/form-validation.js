// assets/js/form-validation.js

// Helper function to show error message under field
function showError(fieldName, message) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (!field) return;
    
    // Remove any existing error
    clearError(fieldName);
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.marginTop = '5px';
    errorDiv.setAttribute('data-error-for', fieldName);
    
    // Add red border to field
    field.style.borderColor = '#dc3545';
    
    // Insert error after field
    field.parentNode.appendChild(errorDiv);
}

// Helper function to clear error message
function clearError(fieldName) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    if (!field) return;
    
    // Reset border color
    field.style.borderColor = '#A8E6CF';
    
    // Remove error message
    const existingError = document.querySelector(`[data-error-for="${fieldName}"]`);
    if (existingError) {
        existingError.remove();
    }
}

// Clear all errors
function clearAllErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('input, textarea').forEach(field => {
        field.style.borderColor = '#A8E6CF';
    });
}

function confirmProposition() {
    const form = document.getElementById('proposer-form');
    const assoc = form['association_nom'].value.trim();
    const email = form['email_contact'].value.trim();
    const tel = form['tel'].value.trim();
    const type = form['type'].value.trim();
    const description = form['description'].value.trim();
    
    // Clear all previous errors
    clearAllErrors();
    
    let hasError = false;
    
    // Vérifier que tous les champs sont remplis
    if (!assoc) {
        showError('association_nom', "Le nom de l'association est obligatoire.");
        hasError = true;
    }
    if (!email) {
        showError('email_contact', "L'email est obligatoire.");
        hasError = true;
    }
    if (!tel) {
        showError('tel', "Le téléphone est obligatoire.");
        hasError = true;
    }
    if (!type) {
        showError('type', "Le type d'événement est obligatoire.");
        hasError = true;
    }
    if (!description) {
        showError('description', "La description est obligatoire.");
        hasError = true;
    }
    
    if (hasError) return false;
    
    // Validation nom de l'association (lettres uniquement, espaces autorisés)
    const lettresRegex = /^[a-zA-ZÀ-ÿ\s]+$/;
    if (!lettresRegex.test(assoc)) {
        showError('association_nom', "Le nom de l'association doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation email
    const emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
        showError('email_contact', "Adresse e-mail invalide.");
        return false;
    }
    
    // Validation téléphone (exactement 8 chiffres, chiffres uniquement)
    const telRegex = /^[0-9]{8}$/;
    if (!telRegex.test(tel)) {
        showError('tel', "Le téléphone doit contenir exactement 8 chiffres.");
        return false;
    }
    
    // Validation type (lettres uniquement, espaces autorisés)
    if (!lettresRegex.test(type)) {
        showError('type', "Le type d'événement doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation description (lettres et chiffres uniquement, espaces autorisés)
    const lettresChiffresRegex = /^[a-zA-Z0-9À-ÿ\s.,!?'-]+$/;
    if (!lettresChiffresRegex.test(description)) {
        showError('description', "La description doit contenir uniquement des lettres et des chiffres.");
        return false;
    }
    
    return confirm("Voulez-vous vraiment envoyer cette proposition ?");
}

function confirmInscription() {
    const form = document.getElementById('inscription-form');
    const nom = form['nom'].value.trim();
    const prenom = form['prenom'].value.trim();
    const age = form['age'].value.trim();
    const email = form['email'].value.trim();
    const tel = form['tel'].value.trim();
    
    // Clear all previous errors
    clearAllErrors();
    
    let hasError = false;
    
    // Vérifier que tous les champs sont remplis
    if (!nom) {
        showError('nom', "Le nom est obligatoire.");
        hasError = true;
    }
    if (!prenom) {
        showError('prenom', "Le prénom est obligatoire.");
        hasError = true;
    }
    if (!age) {
        showError('age', "L'âge est obligatoire.");
        hasError = true;
    }
    if (!email) {
        showError('email', "L'email est obligatoire.");
        hasError = true;
    }
    if (!tel) {
        showError('tel', "Le téléphone est obligatoire.");
        hasError = true;
    }
    
    if (hasError) return false;
    
    // Validation nom (lettres uniquement, espaces autorisés)
    const lettresRegex = /^[a-zA-ZÀ-ÿ\s]+$/;
    if (!lettresRegex.test(nom)) {
        showError('nom', "Le nom doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation prénom (lettres uniquement, espaces autorisés)
    if (!lettresRegex.test(prenom)) {
        showError('prenom', "Le prénom doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation âge (chiffres uniquement, entre 12 et 70)
    const ageNum = parseInt(age);
    if (isNaN(ageNum) || ageNum < 12 || ageNum > 70) {
        showError('age', "L'âge doit être un nombre entre 12 et 70.");
        return false;
    }
    
    // Validation email
    const emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
        showError('email', "Adresse e-mail invalide.");
        return false;
    }
    
    // Validation téléphone (exactement 8 chiffres, chiffres uniquement)
    const telRegex = /^[0-9]{8}$/;
    if (!telRegex.test(tel)) {
        showError('tel', "Le téléphone doit contenir exactement 8 chiffres.");
        return false;
    }
    
    return confirm("Confirmez-vous votre inscription ?");
}


// Real-time validation for phone fields - only allow numbers and max 8 digits
document.addEventListener('DOMContentLoaded', function() {
    const phoneFields = document.querySelectorAll('input[name="tel"]');
    
    phoneFields.forEach(field => {
        field.addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 8 digits
            if (this.value.length > 8) {
                this.value = this.value.slice(0, 8);
            }
        });
        
        // Prevent pasting non-numeric content
        field.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericOnly = pastedText.replace(/[^0-9]/g, '').slice(0, 8);
            this.value = numericOnly;
        });
    });
});
