// public/js/addConcert.js
document.addEventListener('DOMContentLoaded', function() {
    const select = document.querySelector('select[name$="[sceneFK]"]');
    const delBtn = document.querySelector('.delBtn');
    if (select) {
        // Add the "Ajouter une scène" option
        const option = document.createElement('option');
        option.value = "add_scene";
        option.text = "➕ Ajouter une scène";
        select.appendChild(option);

        // Set initial value for delBtn if present
        if (delBtn) {
            delBtn.dataset.sceneId = select.value;
        }

        // Listen for change
        select.addEventListener('change', function() {
            if (delBtn) {
                delBtn.dataset.sceneId = this.value;
            }
            if (this.value === "add_scene") {
                window.location.href = addSceneUrl;
            }
        });
    }
    if (select && delBtn) {
        function updateDelBtnHref() {
            const sceneId = select.value;
            if (sceneId && sceneId !== "add_scene") {
                delBtn.href = '/deletePoi/' + sceneId;
            } else {
                delBtn.href = '#';
            }
        }
        updateDelBtnHref();
        select.addEventListener('change', updateDelBtnHref);
    }
});