// Gestion de l'upload d'image pour les dons
document.addEventListener('DOMContentLoaded', () => {
    const imageInput = document.getElementById('image-don');
    const uploadBox = document.getElementById('image-upload-box');
    const previewContainer = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeBtn = document.getElementById('remove-image');
    
    if (!imageInput || !uploadBox) return;
    
    // Clic sur la zone d'upload
    uploadBox.addEventListener('click', () => {
        imageInput.click();
    });
    
    // Drag & Drop
    uploadBox.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadBox.style.borderColor = '#88b04b';
        uploadBox.style.background = 'linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%)';
    });
    
    uploadBox.addEventListener('dragleave', () => {
        uploadBox.style.borderColor = '#A8E6CF';
        uploadBox.style.background = 'linear-gradient(135deg, #ffffff 0%, #f0fff4 100%)';
    });
    
    uploadBox.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadBox.style.borderColor = '#A8E6CF';
        uploadBox.style.background = 'linear-gradient(135deg, #ffffff 0%, #f0fff4 100%)';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageFile(files[0]);
        }
    });
    
    // Changement de fichier
    imageInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleImageFile(e.target.files[0]);
        }
    });
    
    // Supprimer l'image
    if (removeBtn) {
        removeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            imageInput.value = '';
            previewContainer.style.display = 'none';
            uploadBox.style.display = 'block';
            previewImg.src = '';
        });
    }
    
    // Traiter le fichier image
    function handleImageFile(file) {
        // Vérifier le type
        if (!file.type.match('image.*')) {
            alert('Veuillez sélectionner une image (JPG, PNG, GIF)');
            return;
        }
        
        // Vérifier la taille (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('L\'image est trop volumineuse. Taille maximale : 5MB');
            return;
        }
        
        // Créer un DataTransfer pour mettre à jour l'input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        imageInput.files = dataTransfer.files;
        
        // Afficher l'aperçu
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            uploadBox.style.display = 'none';
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
