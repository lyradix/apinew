document.addEventListener('DOMContentLoaded', function() {
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
                    } else {
                        typeSelect.value = '';
                        linkInput.value = '';
                        frontPageCheckbox.checked = false;
                    }
                });
                partnerSelect = newPartnerSelect;
            });
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
});