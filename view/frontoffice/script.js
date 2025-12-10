let currentProgress = 0;

function updateProgress(percentage) {
    if (currentProgress >= 100) {
        return;
    }
    
    currentProgress += percentage;
    
    if (currentProgress > 100) {
        currentProgress = 100;
    }
    
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    progressBar.style.width = currentProgress + '%';
    progressText.textContent = Math.round(currentProgress) + '%';
}

document.addEventListener('DOMContentLoaded', () => {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
});
