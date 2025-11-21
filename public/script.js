document.addEventListener("DOMContentLoaded", function(){
    let prodForm = document.getElementById('prodForm');
    if(prodForm){
        prodForm.onsubmit = function(e){
            let nom = document.getElementsByName('nom')[0].value;
            let prix = document.getElementsByName('prix')[0].value;
            if(nom.length < 3){
                alert('Le nom doit comporter au moins 3 caractères.');
                e.preventDefault();
            }
            if(isNaN(prix) || prix <= 0){
                alert('Prix doit être supérieur à zéro.');
                e.preventDefault();
            }
        };
    }
});
// Une fonction qui ajoute une ville au DOM dynamiquement
function ajouterVille(nomVille) {
    const ul = document.getElementById('ville-list');
    const li = document.createElement('li');
    li.textContent = nomVille + " ";
    // Bouton de suppression
    const btn = document.createElement('button');
    btn.textContent = "Supprimer";
    btn.className = "supprimer";
    // Ajout action suppression
    btn.addEventListener('click', function() {
        li.remove();
    });
    li.appendChild(btn);
    ul.appendChild(li);
}

// Gérer le bouton ajouter
document.getElementById('ajouter-btn').onclick = function() {
    const champ = document.getElementById('ville-input');
    const val = champ.value.trim();
    if (val.length > 0) {
        ajouterVille(val);
        champ.value = "";
    } else {
        alert("Merci de saisir un nom de ville valide.");
    }
}

// Initialiser les boutons supprimer déjà présents dans le HTML
document.querySelectorAll('.supprimer').forEach(function(btn){
    btn.addEventListener('click', function(e){
        btn.parentElement.remove();
    });
});
document.getElementById('depotForm').onsubmit = function(e){
    var region = document.getElementsByName('region')[0].value;
    if(region.length < 2){
        alert('Le nom de la région doit avoir au moins 2 caractères.');
        e.preventDefault();
    }
};
document.getElementById('depotForm').onsubmit = function(e){
    var region = document.getElementsByName('region')[0].value;
    if(region.length < 2){
        alert('Le nom de la région doit avoir au moins 2 caractères.');
        e.preventDefault();
    }
};
document.getElementById('buyForm').onsubmit = function(e){
    var qty = document.getElementsByName('quantite')[0].value;
    if(parseInt(qty) <= 0){
        alert('La quantité doit être > 0.');
        e.preventDefault();
    }
};
document.getElementById('buyForm').onsubmit = function(e){
    var qty = document.getElementsByName('quantite')[0].value;
    if(parseInt(qty) <= 0){
        alert('La quantité doit être > 0.');
        e.preventDefault();
    }
};