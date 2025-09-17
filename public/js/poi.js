let poiData = {};

document.addEventListener('DOMContentLoaded', function() {
    fetch('/poi') 
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('poiId');
            const typeSelect = document.getElementById('typeSelect');
            const typesSet = new Set();

            data.features.forEach(feature => {
                // Fill POI select
                const option = document.createElement('option');
                option.value = feature.id;
                option.textContent = feature.properties.popup || 'POI ' + feature.id;
                select.appendChild(option);

                // Store POI data for quick lookup
                poiData[feature.id] = feature;
                // Collect unique types
                if (feature.properties.type) {
                    typesSet.add(feature.properties.type);
                }
            });

            // Fill type select
            typesSet.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                typeSelect.appendChild(option);
            });

            // Add event listener for delete button
            document.querySelector('.delBtn').addEventListener('click', function(e) {
                e.preventDefault();
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
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ id: poiId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert('Lieu supprimé avec succès !');
                            // Reload the POI data to update the list
                            reloadPoiData();
                        } else if (data.error) {
                            alert('Erreur : ' + data.error);
                        } else {
                            alert('Une erreur est survenue.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Erreur lors de la suppression');
                    });
                }
            });

            // Add event listener for POI select change
            select.addEventListener('change', function() {
                const selectedId = this.value;
                const longitudeInput = document.querySelector('#modifForm input[placeholder^="exemple"]');
                const latitudeInput = document.querySelector('#modifForm input[placeholder^="48."]');
                if (poiData[selectedId]) {
                    const coords = poiData[selectedId].geometry.coordinates;
                    longitudeInput.value = coords[0];
                    latitudeInput.value = coords[1];
                    if (poiData[selectedId].properties.type) {
                        document.getElementById('typeSelect').value = poiData[selectedId].properties.type;
                    }
                } else {
                    longitudeInput.value = '';
                    latitudeInput.value = '';
                    document.getElementById('typeSelect').value = '';
                }
            });
        });

    document.getElementById('modifForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const poiId = document.getElementById('poiId').value;
        const longitude = document.querySelector('#modifForm input[name="longitude"]').value;
        const latitude = document.querySelector('#modifForm input[name="latitude"]').value;
        const type = document.getElementById('typeSelect').value;
        const popup = poiData[poiId] && poiData[poiId].properties.popup ? poiData[poiId].properties.popup : '';

        if (poiId) {
            fetch('/updatePoi', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id: poiId,
                    popup: popup,
                    longitude: parseFloat(longitude),
                    latitude: parseFloat(latitude),
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.message) {
                    alert('Lieu modifié avec succès !');
                } else {
                    alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                }
            })
            .catch(() => alert('Erreur lors de l\'envoi du formulaire.'));
        }
    });
});

//radio

// Function to reload POI data and reset the modify form
function reloadPoiData() {
    const select = document.getElementById('poiId');
    const typeSelect = document.getElementById('typeSelect');
    select.innerHTML = '<option value="">-- Choisir un lieu --</option>';
    typeSelect.innerHTML = '<option value="">-- Choisir un type --</option>';
    fetch('/poi') 
        .then(response => response.json())
        .then(data => {
            const typesSet = new Set();
            data.features.forEach(feature => {
                const option = document.createElement('option');
                option.value = feature.id;
                option.textContent = feature.properties.popup || 'POI ' + feature.id;
                select.appendChild(option);
                poiData[feature.id] = feature;
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
                const longitudeInput = document.querySelector('#modifForm input[placeholder^="exemple"]');
                const latitudeInput = document.querySelector('#modifForm input[placeholder^="48."]');
                if (poiData[selectedId]) {
                    const coords = poiData[selectedId].geometry.coordinates;
                    longitudeInput.value = coords[0];
                    latitudeInput.value = coords[1];
                    if (poiData[selectedId].properties.type) {
                        document.getElementById('typeSelect').value = poiData[selectedId].properties.type;
                    }
                } else {
                    longitudeInput.value = '';
                    latitudeInput.value = '';
                    document.getElementById('typeSelect').value = '';
                }
            });
        });
}

document.getElementById('refreshModify').addEventListener('click', reloadPoiData);

document.getElementById('poiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const typeField = form.querySelector('[name$="[type]"]');
    const type = typeField ? typeField.value : '';

    // Log all form data for debugging
    console.log('Form data being submitted:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Get CSRF token - look for a hidden input with token in the name
    let csrfToken = null;
    // Check different possible CSRF token field names
    const possibleTokenFields = ['_token', '_csrf_token', form.id + '_token'];
    
    for (const tokenName of possibleTokenFields) {
        const tokenField = form.querySelector(`input[name="${tokenName}"]`);
        if (tokenField) {
            csrfToken = tokenField.value;
            console.log(`Found CSRF token with name ${tokenName}:`, csrfToken);
            break;
        }
    }
    
    // As a fallback, try a more generic selector
    if (!csrfToken) {
        const tokenField = form.querySelector('input[name*="token"]');
        if (tokenField) {
            csrfToken = tokenField.value;
            console.log('Found CSRF token with generic selector:', csrfToken);
        }
    }

    // For both scene and regular POI submissions, use AJAX with proper CSRF handling
    const isSceneSubmission = type === 'scène';
    const url = isSceneSubmission ? '/postScene' : form.action;
    
    console.log(`Submitting form to ${url} for ${isSceneSubmission ? 'scene' : 'regular POI'}`);
    
    // Since we're now using form_start/form_end in the template,
    // the CSRF token is automatically included in the FormData
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (response.redirected) {
            // If the server redirected us, follow the redirect
            window.location.href = response.url;
            return { redirected: true };
        }
        
        if (!response.ok) {
            console.error('Response error:', response.statusText);
            return response.text().then(text => {
                try {
                    // Try to parse as JSON for structured error
                    return JSON.parse(text);
                } catch (e) {
                    // If parsing fails, it might be HTML or other content
                    // Check if it contains CSRF token error
                    if (text.includes('CSRF') || text.includes('csrf')) {
                        throw new Error('Invalid CSRF token. Please refresh the page and try again.');
                    } else {
                        throw new Error('Server responded with status: ' + response.status);
                    }
                }
            });
        }
        
        // Try to parse as JSON
        return response.text().then(text => {
            if (!text) return { success: true };
            
            try {
                return JSON.parse(text);
            } catch (e) {
                // If the response is not JSON but the request was successful,
                // consider it a success and reload the page
                if (response.ok) {
                    window.location.reload();
                    return { success: true, reloaded: true };
                }
                throw new Error('Unexpected response format');
            }
        });
    })
    .then(data => {
        if (data.redirected || data.reloaded) return;
        
        console.log('Response data:', data);
        
        if (data.success) {
            alert(isSceneSubmission ? 'Scène ajoutée avec succès !' : 'Lieu ajouté avec succès !');
            form.reset();
            
            // Ensure radio button for "add" remains selected and styling is maintained
            document.querySelector('input[name="formMode"][value="add"]').checked = true;
            
            // Make sure the radio container has proper styling
            const radioContainer = document.querySelector('.radio');
            if (radioContainer) {
                radioContainer.style.display = 'flex';
                radioContainer.style.justifyContent = 'center';
                
                // Add an active class for additional styling if needed
                radioContainer.classList.add('active-radio');
            }
        } else {
            alert('Erreur : ' + (data.message || data.error || 'Une erreur est survenue.'));
        }
    })
    .catch(error => {
        console.error('Error submitting form:', error);
        alert('Erreur lors de l\'envoi du formulaire: ' + error.message || error);
    });
});