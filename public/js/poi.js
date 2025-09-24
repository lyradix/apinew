// Initialize global object for POI data
window.poiData = window.poiData || {};

/**
 * Reloads POI data from the server and populates select elements with options.
 * This function handles the initial load and subsequent refreshes.
 */
window.reloadPoiData = function() {
    const select = document.getElementById('poiId');
    const typeSelect = document.getElementById('typeSelect');
    
    if (!select || !typeSelect) {
        return;
    }
    
    select.innerHTML = '<option value="">-- Choisir un lieu --</option>';
    typeSelect.innerHTML = '<option value="">-- Choisir un type --</option>';
    
    fetch('/poi')
        .then(response => response.json())
        .then(data => {
            try {
                const typesSet = new Set();
                
                if (!data.features || !Array.isArray(data.features)) {
                    return;
                }
                
                data.features.forEach(feature => {
                    if (!feature || !feature.id || !feature.properties) {
                        return;
                    }
                    
                    const option = document.createElement('option');
                    option.value = feature.id;
                    option.textContent = feature.properties.popup || 'POI ' + feature.id;
                    select.appendChild(option);
                    window.poiData[feature.id] = feature;
                    
                    if (feature.properties && feature.properties.type) {
                        typesSet.add(feature.properties.type);
                    }
                });
                
                typesSet.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type;
                    option.textContent = type;
                    typeSelect.appendChild(option);
                });

                // Remove previous listeners by cloning
                const newSelect = select.cloneNode(true);
                select.parentNode.replaceChild(newSelect, select);
                
                // Add change event listener to the select
                newSelect.addEventListener('change', function() {
                    try {
                        const selectedId = this.value;
                        const longitudeInput = document.querySelector('#modifForm input[name="longitude"]');
                        const latitudeInput = document.querySelector('#modifForm input[name="latitude"]');
                        
                        if (!longitudeInput || !latitudeInput) {
                            return;
                        }
                        
                        if (window.poiData[selectedId]) {
                            const coords = window.poiData[selectedId].geometry.coordinates;
                            longitudeInput.value = coords[0];
                            latitudeInput.value = coords[1];
                            if (window.poiData[selectedId].properties.type) {
                                typeSelect.value = window.poiData[selectedId].properties.type;
                            }
                        } else {
                            longitudeInput.value = '';
                            latitudeInput.value = '';
                            typeSelect.value = '';
                        }
                    } catch (e) {
                        console.error('Error in change handler:', e);
                    }
                });
            } catch (e) {
                console.error('Error processing POI data:', e);
            }
        })
        .catch(error => console.error('Error:', error));
};

// Initialize immediately when script loads
initializePoiForm();

/**
 * Initializes the POI form handlers and loads data
 * Ensures DOM elements are ready before attempting to interact with them
 */
function initializePoiForm() {
    try {
        // Add defensive checks to ensure elements exist
        if (!document.getElementById('addForm') || !document.getElementById('modifyForm')) {
            // Elements not ready yet, try again in a moment
            setTimeout(initializePoiForm, 50);
            return;
        }
        
        fetchPoiData();
        setupPoiForm();
        
        // Add event listener for delete button using event delegation
        if (modifyForm) {
            modifyForm.addEventListener('click', function(event) {
                const deleteButton = event.target.closest('.delBtn');
                if (deleteButton) {
                    event.preventDefault();
                    const poiId = document.getElementById('poiId').value;
                    
                    if (!poiId) {
                        alert('Veuillez sélectionner un lieu à supprimer');
                        return;
                    }

                    if (confirm('Êtes-vous sûr de vouloir supprimer ce lieu ?')) {
                        fetch('/deletepoi', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ id: poiId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                alert('Lieu supprimé avec succès !');
                                reloadPoiData();
                            } else if (data.error) {
                                alert('Erreur : ' + data.error);
                            } else {
                                alert('Une erreur est survenue.');
                            }
                        })
                        .catch(error => {
                            alert('Erreur lors de la suppression');
                        });
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error initializing POI form:', error);
    }
}

/**
 * Fetches POI data from the server and populates the form elements.
 * Handles initial data load.
 */
function fetchPoiData() {
    fetch('/poi')
        .then(response => response.json())
        .then(data => {
            // Same logic as reloadPoiData but for initial load
            const select = document.getElementById('poiId');
            const typeSelect = document.getElementById('typeSelect');
            if (!select || !typeSelect) return;
            
            const typesSet = new Set();
            data.features.forEach(feature => {
                const option = document.createElement('option');
                option.value = feature.id;
                option.textContent = feature.properties.popup || 'POI ' + feature.id;
                select.appendChild(option);
                window.poiData[feature.id] = feature;
                if (feature.properties.type) {
                    typesSet.add(feature.properties.type);
                }
            });
            
            typesSet.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                typeSelect.appendChild(option);
            });
            
            select.addEventListener('change', function() {
                const selectedId = this.value;
                const longitudeInput = document.querySelector('#modifForm input[name="longitude"]');
                const latitudeInput = document.querySelector('#modifForm input[name="latitude"]');
                
                if (!longitudeInput || !latitudeInput) return;
                
                if (window.poiData[selectedId]) {
                    const coords = window.poiData[selectedId].geometry.coordinates;
                    longitudeInput.value = coords[0];
                    latitudeInput.value = coords[1];
                    if (window.poiData[selectedId].properties.type) {
                        typeSelect.value = window.poiData[selectedId].properties.type;
                    }
                } else {
                    longitudeInput.value = '';
                    latitudeInput.value = '';
                    typeSelect.value = '';
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

/**
 * Sets up form submission handlers for POI forms
 */
function setupPoiForm() {
    // Set up modify form
    const modifForm = document.getElementById('modifForm');
    if (modifForm) {
        if (!modifForm.hasAttribute('data-listener-attached')) {
            modifForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const poiId = document.getElementById('poiId')?.value;
                const longitudeInput = document.querySelector('#modifForm input[name="longitude"]');
                const latitudeInput = document.querySelector('#modifForm input[name="latitude"]');
                const typeSelect = document.getElementById('typeSelect');
                
                if (!poiId || !longitudeInput || !latitudeInput || !typeSelect) {
                    alert('Formulaire incomplet.');
                    return;
                }
                
                const data = {
                    id: poiId,
                    popup: window.poiData[poiId]?.properties?.popup || '',
                    longitude: parseFloat(longitudeInput.value),
                    latitude: parseFloat(latitudeInput.value),
                    type: typeSelect.value
                };

                fetch('/updatePoi', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success || data.message) {
                        alert('Lieu modifié avec succès !');
                        // Refresh the POI data
                        reloadPoiData();
                    } else {
                        alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                    }
                })
                .catch(error => {
                    alert('Erreur lors de l\'envoi du formulaire.');
                });
            });
            modifForm.setAttribute('data-listener-attached', 'true');
        }
    }
    
    // Set up add form
    const poiForm = document.getElementById('poiForm');
    if (poiForm) {
        if (!poiForm.hasAttribute('data-listener-attached')) {
            if (!poiForm.action || poiForm.action.includes('undefined')) {
                poiForm.action = '/postPoi';
            }
            
            poiForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = e.target;
                const formData = new FormData(form);
                
                // Determine if this is a scene based on the type field value
                let typeFieldValue = '';
                for (let [key, value] of formData.entries()) {
                    if (key === 'type' || key.endsWith('[type]')) {
                        typeFieldValue = value;
                    }
                }
                
                const isScene = (typeFieldValue === 'scène');
                const endpoint = isScene ? '/add-scene' : '/postPoi';
                
                fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success || data.message) {
                        alert('Lieu ajouté avec succès !');
                        form.reset();
                        reloadPoiData();
                    } else {
                        alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                    }
                })
                .catch(error => {
                    alert('Erreur lors de l\'envoi du formulaire.');
                });
            });
            poiForm.setAttribute('data-listener-attached', 'true');
        }
    }
}