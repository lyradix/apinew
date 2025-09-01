// Remove DOMContentLoaded to avoid conflicts with Stimulus
// Initialize immediately when script loads
initializePartnerForm();

function initializePartnerForm() {
    // Add defensive checks to ensure elements exist
    if (!document.getElementById('addForm') || !document.getElementById('modifyForm')) {
        // Elements not ready yet, try again in a moment
        setTimeout(initializePartnerForm, 50);
        return;
    }

    const radios = document.getElementsByName('formMode');
    const addForm = document.getElementById('addForm');
    const modifyFormDiv = document.getElementById('modifyForm');
    let partnerSelect = document.getElementById('Id');
    const typeSelect = document.getElementById('typeSelect');
    const linkInput = document.querySelector('#modifForm input[name="link"]');
    const frontPageCheckbox = document.querySelector('#modifForm input[type="checkbox"]');
    let partnersData = [];

    function loadModifyForm() {
        addForm.style.display = 'none';
        modifyFormDiv.style.display = '';

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
                // Remove previous event listeners by cloning
                const newPartnerSelect = partnerSelect.cloneNode(true);
                partnerSelect.parentNode.replaceChild(newPartnerSelect, partnerSelect);
                newPartnerSelect.addEventListener('change', function() {
                    const selected = partnersData.find(p => p.id == this.value);
                    if (selected) {
                        typeSelect.value = selected.type || '';
                        linkInput.value = selected.link || '';
                        frontPageCheckbox.checked = !!selected.frontPage;
                        
                        // Show image if available
                        if (selected.image) {
                            displayImage(selected);
                        } else {
                            displayNoImage(selected);
                        }
                    } else {
                        typeSelect.value = '';
                        linkInput.value = '';
                        frontPageCheckbox.checked = false;
                        hidePartnerImage();
                    }
                });
                partnerSelect = newPartnerSelect;
            });

            // Use event delegation to handle clicks on delete buttons within the modify form.
            // This ensures that when a delete button is clicked, the selected partner is deleted after confirmation.
            modifyFormDiv.addEventListener('click', function(event) {
                const delBtn = event.target.closest('.delBtn');
                if (delBtn) {
                    const selectedId = partnerSelect.value;
                    if (!selectedId) {
                        alert('Veuillez sélectionner un partenaire à supprimer.');
                        return;
                    }

                    if (confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?')) {
                        fetch(`/deletePartner/${selectedId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ id: selectedId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                alert('Partenaire commercial supprimé avec succès !');
                                // Refresh the partner list
                                reloadPartnersData();
                            } else if (data.error) {
                                alert('Erreur : ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Erreur lors de la suppression');
                        });
                    }
                }
            });
    }

    // Define reloadPartnersData to reload and update the modify form
    function reloadPartnersData() {
        loadModifyForm();
    }

    function displayImage(partner) {
        const imageContainer = document.getElementById('currentImageContainer');
        const currentImage = document.getElementById('currentImage');
        const imageError = document.getElementById('imageError');
        
        if (!imageContainer || !currentImage) {
            return;
        }
        
        if (partner.image) {
            const imagePath = '/images/' + partner.image;
            
            // Reset error state
            if (imageError) imageError.style.display = 'none';
            
            // Set image source and show
            currentImage.src = imagePath;
            currentImage.alt = 'Image de ' + partner.title;
            currentImage.style.display = 'block';
            
            // Show the container
            imageContainer.style.display = 'block';
            
        } else {
            displayNoImage(partner);
        }
    }

    function displayNoImage(partner) {
        const imageContainer = document.getElementById('currentImageContainer');
        const currentImage = document.getElementById('currentImage');
        const imageError = document.getElementById('imageError');
        if (currentImage) {
            currentImage.src = '';
            currentImage.style.display = 'none';
        }
        if (imageError) {
            imageError.style.display = 'block';
            imageError.textContent = 'Aucune image pour ce partenaire';
        }
        if (imageContainer) imageContainer.style.display = 'block';
    }

    function hidePartnerImage() {
        const imageContainer = document.getElementById('currentImageContainer');
        const currentImage = document.getElementById('currentImage');
        const imageError = document.getElementById('imageError');
        if (imageContainer) imageContainer.style.display = 'none';
        if (currentImage) {
            currentImage.src = '';
            currentImage.style.display = 'none';
        }
        if (imageError) imageError.style.display = 'none';
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
                
                const formData = new FormData();
                const id = document.getElementById('Id').value;
                
                if (!id) {
                    alert('Veuillez sélectionner un partenaire');
                    return;
                }
                
                const frontPageCheckbox = document.getElementById('frontPageCheckbox');
                const typeSelect = document.getElementById('typeSelect');
                const linkInput = document.getElementById('linkInput');
                const imageUpload = document.querySelector('input[name="imageUpload"]');
                
                // Add all form data
                formData.append('id', id);
                formData.append('frontPage', frontPageCheckbox ? frontPageCheckbox.checked : false);
                formData.append('type', typeSelect ? typeSelect.value : '');
                formData.append('link', linkInput ? linkInput.value : '');
                
                if (imageUpload && imageUpload.files && imageUpload.files[0]) {
                    formData.append('imageFile', imageUpload.files[0]);
                }
                
                // Submit the form data
                fetch('./update-partner', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success || data.message) {
                        alert('Partenaire commercial modifié avec succès');
                        // Refresh the partner data to show new image
                        reloadPartnersData();
                    } else {
                        alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                    }
                })
                .catch(error => {
                    console.error('Error updating partner:', error);
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
            refreshButton.addEventListener('click', reloadPartnersData);
            refreshButton.setAttribute('data-listener-attached', 'true');
        }
    }

    // Add partner form event
    const partnerForm = document.getElementById('partnerForm');
    if (partnerForm) {
        if (!partnerForm.hasAttribute('data-listener-attached')) {
            partnerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const typeField = form.querySelector('[name$="[type]"]');
                const type = typeField ? typeField.value : '';

                if (type === 'scène') {
                    fetch(window.addSceneUrl || './add-partner', {
                        method: 'POST',
                        body: formData,
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Partenaire commercial ajouté avec succès');
                            form.reset();
                            reloadPartnersData();
                        } else {
                            alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error adding partner:', error);
                        alert('Erreur lors de l\'envoi du formulaire.');
                    });
                } else {
                    form.submit();
                }
            });
            partnerForm.setAttribute('data-listener-attached', 'true');
        }
    }
}
