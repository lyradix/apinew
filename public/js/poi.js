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
                        } else {
                            alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
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

    if (type === 'scène') {
        fetch(window.addSceneUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Scène ajoutée avec succès !');
                form.reset();
            } else {
                alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
            }
        })
        .catch(() => alert('Erreur lors de l\'envoi du formulaire.'));
    } else {
        form.submit(); // fallback for other types
    }
});