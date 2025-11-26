// assets/js/form-validation.js
function confirmProposition() {
    const form = document.getElementById('proposer-form');
    const assoc = form['association_nom'].value.trim();
    const email = form['email_contact'].value.trim();
    const tel = form['tel'].value.trim();
    const type = form['type'].value.trim();
    const description = form['description'].value.trim();
    
    console.log("Validation - assoc:", assoc, "email:", email, "tel:", tel, "type:", type, "description:", description);
    
    // Vérifier que tous les champs sont remplis
    if (!assoc) {
        alert("Le nom de l'association est obligatoire.");
        return false;
    }
    if (!email) {
        alert("L'email est obligatoire.");
        return false;
    }
    if (!tel) {
        alert("Le téléphone est obligatoire.");
        return false;
    }
    if (!type) {
        alert("Le type d'événement est obligatoire.");
        return false;
    }
    if (!description) {
        alert("La description est obligatoire.");
        return false;
    }
    
    // Validation nom de l'association (lettres uniquement, espaces autorisés)
    const lettresRegex = /^[a-zA-ZÀ-ÿ\s]+$/;
    if (!lettresRegex.test(assoc)) {
        alert("Le nom de l'association doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation email
    const emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
        alert("Adresse e-mail invalide.");
        return false;
    }
    
    // Validation téléphone (8 chiffres maximum, chiffres uniquement)
    const telRegex = /^[0-9]{1,8}$/;
    if (!telRegex.test(tel)) {
        alert("Le téléphone doit contenir uniquement des chiffres (maximum 8 chiffres).");
        return false;
    }
    
    // Validation type (lettres uniquement, espaces autorisés)
    if (!lettresRegex.test(type)) {
        alert("Le type d'événement doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation description (lettres et chiffres uniquement, espaces autorisés)
    const lettresChiffresRegex = /^[a-zA-Z0-9À-ÿ\s.,!?'-]+$/;
    if (!lettresChiffresRegex.test(description)) {
        alert("La description doit contenir uniquement des lettres et des chiffres.");
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
    
    console.log("Validation - nom:", nom, "prenom:", prenom, "age:", age, "email:", email, "tel:", tel);
    
    // Vérifier que tous les champs sont remplis
    if (!nom) {
        alert("Le nom est obligatoire.");
        return false;
    }
    if (!prenom) {
        alert("Le prénom est obligatoire.");
        return false;
    }
    if (!age) {
        alert("L'âge est obligatoire.");
        return false;
    }
    if (!email) {
        alert("L'email est obligatoire.");
        return false;
    }
    if (!tel) {
        alert("Le téléphone est obligatoire.");
        return false;
    }
    
    // Validation nom (lettres uniquement, espaces autorisés)
    const lettresRegex = /^[a-zA-ZÀ-ÿ\s]+$/;
    if (!lettresRegex.test(nom)) {
        alert("Le nom doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation prénom (lettres uniquement, espaces autorisés)
    if (!lettresRegex.test(prenom)) {
        alert("Le prénom doit contenir uniquement des lettres.");
        return false;
    }
    
    // Validation âge (chiffres uniquement, entre 12 et 70)
    const ageNum = parseInt(age);
    if (isNaN(ageNum) || ageNum < 12 || ageNum > 70) {
        alert("L'âge doit être un nombre entre 12 et 70.");
        return false;
    }
    
    // Validation email
    const emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
        alert("Adresse e-mail invalide.");
        return false;
    }
    
    // Validation téléphone (8 chiffres maximum, chiffres uniquement)
    const telRegex = /^[0-9]{1,8}$/;
    if (!telRegex.test(tel)) {
        alert("Le téléphone doit contenir uniquement des chiffres (maximum 8 chiffres).");
        return false;
    }
    
    return confirm("Confirmez-vous votre inscription ?");
}
