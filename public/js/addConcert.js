// Ajouter un addEventListener pour le select nom égale "sceneFK"
document.addEventListener('DOMContentLoaded', function() {
    const select = document.querySelector('select[name$="[sceneFK]"]');
    // variable pour pour le bouton delete
    const delBtn = document.querySelector('.delBtn');
    // Ajouter option + Ajouter une scène avec valeur "add_scene si Select"
    if (select) {
        const option = document.createElement('option');
        option.value = "add_scene";
        option.text = "+ Ajouter une scène";
        // append la valeur au select
        select.appendChild(option);

        // si delBtn est cliqué, l'id prend la valeur du select
        if (delBtn) {
            delBtn.dataset.sceneId = select.value;
        }


        select.addEventListener('change', function() {
            if (delBtn) {
                delBtn.dataset.sceneId = this.value;
            }
            if (this.value === "add_scene") {
                window.location.href = addSceneUrl;
            }
        });
    }

    // Met à jour la fonction du bouton delete
    // si select et delBtn existent
    // sinon, ne rien faire
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