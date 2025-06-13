
// la fonction du radio pour afficher les formulaires d'ajout et de modification des partenaires
// Pour le formulaire fecth des données depuis la route /partners

// la déclarations des variables au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.getElementsByName('formMode');
    const addForm = document.getElementById('addForm');
    const modifyFormDiv = document.getElementById('modifyForm');
    let partnerSelect = document.getElementById('Id');
    const typeSelect = document.getElementById('typeSelect');
    const linkInput = document.querySelector('#modifForm input[name="link"]');
    const frontPageCheckbox = document.querySelector('#modifForm input[type="checkbox"]');
    let partnersData = [];

    // La fonction pour charger le formulaire de modification
    function loadModifyForm() {
        addForm.style.display = 'none';
        modifyFormDiv.style.display = '';

        //Réccupération des données depuis /partners et promise pour remplir le select
        fetch('/partners')
            .then(response => response.json())
            .then(data => {
                partnersData = data;
                partnerSelect.innerHTML = '<option value="">-- Choisir un partenaire --</option>';
                typeSelect.innerHTML = '<option value="">-- Choisir un type --</option>';
                partnersData.forEach(partner => {
                    const option = document.createElement('option');
                    option.value = partner.id;
                    option.textContent = partner.title;
                    // Ajouter l'option choisie à la liste déroulante
                    partnerSelect.appendChild(option);
                });
                const types = [...new Set(partnersData.map(p => p.type))];
                types.forEach(type => {
                    if (type) {
                        const option = document.createElement('option');
                        option.value = type;
                        option.textContent = type;
                        typeSelect.appendChild(option);
                    }
                });
         
                // réinitialise la variable epour éviter les doublons
                const newPartnerSelect = partnerSelect.cloneNode(true);
                partnerSelect.parentNode.replaceChild(newPartnerSelect, partnerSelect);
                newPartnerSelect.addEventListener('change', function() {
                    const selected = partnersData.find(p => p.id == this.value);
                    if (selected) {
                        typeSelect.value = selected.type || '';
                        linkInput.value = selected.link || '';
                        frontPageCheckbox.checked = !!selected.frontPage;
                    } else {
                        typeSelect.value = '';
                        linkInput.value = '';
                        frontPageCheckbox.checked = false;
                    }
                });
                partnerSelect = newPartnerSelect;
            });
    }

    // Event listener pour les radios
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'add') {
                addForm.style.display = '';
                modifyFormDiv.style.display = 'none';
            } else {
                loadModifyForm();
            }
        });
    });

    //Renvoyer le formulaire d'ajout ou de modification au chargement de la page
    if (document.getElementById('modifyRadio').checked) {
        loadModifyForm();
    } else {
        addForm.style.display = '';
        modifyFormDiv.style.display = 'none';
    }
});