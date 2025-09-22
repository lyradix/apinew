// Remove DOMContentLoaded to avoid conflicts with Stimulus
// Initialize immediately when script loads
initializeInfoForm();

function initializeInfoForm() {
    // Add defensive checks to ensure elements exist
    if (!document.getElementById('addForm') || !document.getElementById('modifyForm')) {
        // Elements not ready yet, try again in a moment
        setTimeout(initializeInfoForm, 50);
        return;
    }

    const radios = document.getElementsByName('formMode');
    const addForm = document.getElementById('addForm');
    const modifyFormDiv = document.getElementById('modifyForm');
    let InfoSelect = document.getElementById('Id');
    const typeSelect = document.getElementById('typeSelect');
    let InfosData = [];

    function loadModifyForm() {
        addForm.style.display = 'none';
        modifyFormDiv.style.display = '';

        fetch('/info')
            .then(response => response.json())
            .then(data => {
                InfosData = data;
                InfoSelect.innerHTML = '<option value="">-- Choisir une information --</option>';
                typeSelect.innerHTML = '<option value="">-- Choisir un type --</option>';
                InfosData.forEach(Info => {
                    const option = document.createElement('option');
                    option.value = Info.id;
                    option.textContent = Info.title;
                    InfoSelect.appendChild(option);
                });
                const types = [...new Set(InfosData.map(p => p.type))];
                types.forEach(type => {
                    if (type) {
                        const option = document.createElement('option');
                        option.value = type;
                        option.textContent = type;
                        typeSelect.appendChild(option);
                    }
                });
                // Remove previous event listeners by cloning
                const newInfoSelect = InfoSelect.cloneNode(true);
                InfoSelect.parentNode.replaceChild(newInfoSelect, InfoSelect);
                newInfoSelect.addEventListener('change', function() {
                    const selected = InfosData.find(p => p.id == this.value);
                    if (selected) {
                        // Update form fields with selected info data
                        typeSelect.value = selected.type || '';
                        
                        // If we have title and descriptif fields in the form, update them
                        const titleInput = document.querySelector('#modifForm input[name="title"]');
                        const descriptifInput = document.querySelector('#modifForm textarea[name="descriptif"]');
                        
                        if (titleInput) titleInput.value = selected.title || '';
                        if (descriptifInput) descriptifInput.value = selected.descriptif || '';
                    } else {
                        // Reset form fields
                        typeSelect.value = '';
                        const titleInput = document.querySelector('#modifForm input[name="title"]');
                        const descriptifInput = document.querySelector('#modifForm textarea[name="descriptif"]');
                        
                        if (titleInput) titleInput.value = '';
                        if (descriptifInput) descriptifInput.value = '';
                    }
                });
                InfoSelect = newInfoSelect;
            });

        //delete button
        const delBtn = document.querySelector('.delBtn');
        if (delBtn) {
            delBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const infoId = InfoSelect.value;
                if (!infoId) {
                    alert('Veuillez sélectionner une information à supprimer.');
                    return;
                }
                if (confirm('Êtes-vous sûr de vouloir supprimer cette information ?')) {
                    fetch(`/deleteInfo/${infoId}`, {
                        method: 'DELETE',
                        headers: {
                             'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ id: infoId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert('Information supprimée avec succès !');
                            // Reload the Info data to update the list
                            reloadInfosData();
                        } else if (data.error) {
                            alert('Erreur : ' + data.error);
                        }
                        else {
                            alert('Une erreur est survenue.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Erreur lors de la suppression');
                    });
                }
            });
        }

    }

//radio

// Function to reload info data and reset the modify form

    function reloadInfosData(

    ) {
        return loadModifyForm();
    }

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

    // Show correct form on page load
    if (document.getElementById('modifyRadio').checked) {
        loadModifyForm();
    } else {
        addForm.style.display = '';
        modifyFormDiv.style.display = 'none';
    }

    // Form submission event
    const modifForm = document.getElementById('modifForm');
    if (modifForm) {
        if (!modifForm.hasAttribute('data-listener-attached')) {
            modifForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const id = document.getElementById('Id').value;
                
                if (!id) {
                    alert('Veuillez sélectionner une information');
                    return;
                }
                
                const typeSelect = document.getElementById('typeSelect');
                const titleInput = document.querySelector('#modifForm input[name="title"]');
                const descriptifInput = document.querySelector('#modifForm textarea[name="descriptif"]');
                
                if (!titleInput || !descriptifInput) {
                    alert('Formulaire incomplet: champs manquants');
                    return;
                }
                
                // Convert formData to JSON for API
                const jsonData = {
                    id: id, // Include the selected info ID
                    title: titleInput.value,
                    type: typeSelect.value,
                    descriptif: descriptifInput.value
                };
                
                // Submit the form data
                fetch(`/updateInfo`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Information modifiée avec succès');
                        // Refresh the Info data
                        reloadInfosData();
                    } else {
                        alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                    }
                })
                .catch(error => {
                    console.error('Error updating Info:', error);
                    alert('Erreur lors de l\'envoi du formulaire');
                });
            });
            modifForm.setAttribute('data-listener-attached', 'true');
        }
    }

    // Refresh button event
    const refreshButton = document.getElementById('refreshModify');
    if (refreshButton) {
        if (!refreshButton.hasAttribute('data-listener-attached')) {
            refreshButton.addEventListener('click', reloadInfosData);
            refreshButton.setAttribute('data-listener-attached', 'true');
        }
    }

    // Add Info form event
    const InfoForm = document.getElementById('infoForm');
    if (InfoForm) {
        if (!InfoForm.hasAttribute('data-listener-attached')) {
            InfoForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const typeField = form.querySelector('[name$="[type]"]');
                
                // Extract data from the form
                const title = form.querySelector('[name$="[title]"]').value;
                const type = typeField ? typeField.value : '';
                const descriptif = form.querySelector('[name$="[descriptif]"]').value;
                
                if (!title || !type || !descriptif) {
                    alert('Veuillez remplir tous les champs obligatoires');
                    return;
                }
                
                // Create JSON data
                const jsonData = {
                    title: title,
                    type: type,
                    descriptif: descriptif
                };
                
                fetch('/addInfo', {
                    method: 'POST',
                    body: JSON.stringify(jsonData),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.id) {
                        alert('Information ajoutée avec succès');
                        form.reset();
                        reloadInfosData();
                    } else {
                        alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                    }
                })
                .catch(error => {
                    console.error('Error adding Info:', error);
                    alert('Erreur lors de l\'envoi du formulaire.');
                });
            });
            InfoForm.setAttribute('data-listener-attached', 'true');
        }
    }
}
