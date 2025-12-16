/**
 * Validation des formulaires de catégories (add/update)
 * Contrôle de saisie en JavaScript pur (pas de validation HTML5)
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.modern-form');
    
    if (!form) return;

    // Empêcher la soumission par défaut
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Nettoyer les messages d'erreur existants
        clearErrors();
        
        // Valider tous les champs
        let isValid = true;
        
        isValid = validateNom() && isValid;
        isValid = validateCode() && isValid;
        
        // Si tout est valide, soumettre le formulaire
        if (isValid) {
            form.submit();
        }
    });

    // Validation en temps réel sur chaque champ
    const nomInput = document.getElementById('nom');
    const codeInput = document.getElementById('code');
    
    if (nomInput) {
        nomInput.addEventListener('blur', validateNom);
        nomInput.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateNom();
            }
        });
    }
    
    if (codeInput) {
        codeInput.addEventListener('blur', validateCode);
        codeInput.addEventListener('input', function() {
            // Forcer minuscules et underscores
            this.value = this.value.toLowerCase().replace(/[^a-z_]/g, '');
            if (this.classList.contains('error')) {
                validateCode();
            }
        });
    }
});

/**
 * Validation du champ Nom
 */
function validateNom() {
    const nomInput = document.getElementById('nom');
    const value = nomInput.value.trim();
    
    // Vérifier si le champ est vide
    if (value === '') {
        showError(nomInput, 'Le nom de la catégorie est obligatoire.');
        return false;
    }
    
    // Vérifier la longueur minimale
    if (value.length < 3) {
        showError(nomInput, 'Le nom doit contenir au moins 3 caractères.');
        return false;
    }
    
    // Vérifier la longueur maximale
    if (value.length > 100) {
        showError(nomInput, 'Le nom ne peut pas dépasser 100 caractères.');
        return false;
    }
    
    // Validation réussie
    clearError(nomInput);
    showSuccess(nomInput, '✓ Nom valide');
    return true;
}

/**
 * Validation du champ Code
 */
function validateCode() {
    const codeInput = document.getElementById('code');
    const value = codeInput.value.trim();
    
    // Vérifier si le champ est vide
    if (value === '') {
        showError(codeInput, 'Le code technique est obligatoire.');
        return false;
    }
    
    // Vérifier le format : uniquement lettres minuscules et underscores
    const codePattern = /^[a-z_]+$/;
    if (!codePattern.test(value)) {
        showError(codeInput, 'Le code doit contenir uniquement des lettres minuscules et underscores (_).');
        return false;
    }
    
    // Vérifier la longueur minimale
    if (value.length < 2) {
        showError(codeInput, 'Le code doit contenir au moins 2 caractères.');
        return false;
    }
    
    // Vérifier la longueur maximale
    if (value.length > 50) {
        showError(codeInput, 'Le code ne peut pas dépasser 50 caractères.');
        return false;
    }
    
    // Validation réussie
    clearError(codeInput);
    showSuccess(codeInput, '✓ Code valide');
    return true;
}

/**
 * Afficher un message d'erreur sous un champ
 */
function showError(input, message) {
    // Ajouter la classe d'erreur
    input.classList.add('error');
    input.style.borderColor = '#dc3545';
    
    // Supprimer l'ancien message s'il existe
    const existingError = input.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    const existingSuccess = input.parentElement.querySelector('.success-message');
    if (existingSuccess) {
        existingSuccess.remove();
    }
    
    // Créer et ajouter le nouveau message d'erreur
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '13px';
    errorDiv.style.marginTop = '6px';
    errorDiv.style.display = 'flex';
    errorDiv.style.alignItems = 'center';
    errorDiv.style.gap = '5px';
    errorDiv.style.fontWeight = '500';
    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
    
    input.parentElement.appendChild(errorDiv);
}

/**
 * Afficher un message de succès sous un champ
 */
function showSuccess(input, message) {
    // Supprimer l'ancien message s'il existe
    const existingSuccess = input.parentElement.querySelector('.success-message');
    if (existingSuccess) {
        existingSuccess.remove();
    }
    
    // Créer et ajouter le message de succès
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.style.color = '#28a745';
    successDiv.style.fontSize = '13px';
    successDiv.style.marginTop = '6px';
    successDiv.style.display = 'flex';
    successDiv.style.alignItems = 'center';
    successDiv.style.gap = '5px';
    successDiv.style.fontWeight = '500';
    successDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
    
    input.parentElement.appendChild(successDiv);
}

/**
 * Effacer l'erreur d'un champ spécifique
 */
function clearError(input) {
    input.classList.remove('error');
    input.style.borderColor = '';
    const errorMessage = input.parentElement.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

/**
 * Effacer toutes les erreurs du formulaire
 */
function clearErrors() {
    const errorInputs = document.querySelectorAll('.error');
    errorInputs.forEach(input => {
        input.classList.remove('error');
        input.style.borderColor = '';
    });
    
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(msg => {
        msg.remove();
    });
    
    const successMessages = document.querySelectorAll('.success-message');
    successMessages.forEach(msg => {
        msg.remove();
    });
}
