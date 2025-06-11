document.addEventListener('DOMContentLoaded', function() {
    const titleSelect = document.getElementById(window.titleFieldId);
    const typeSelect = document.getElementById(window.typeFieldId);
    const descInput = document.getElementById(window.descFieldId);
    const refreshBtn = document.getElementById('refreshInfoBtn');
    const infoData = window.infoData || {};
    const titleToId = window.titleToId || {};
    const updateInfoPath = window.updateInfoPath;

    titleSelect.addEventListener('change', function() {
        const selected = this.value;
        if (infoData[selected]) {
            typeSelect.value = infoData[selected].type;
            descInput.value = infoData[selected].descriptif;
        } else {
            typeSelect.value = '';
            descInput.value = '';
        }
    });

    refreshBtn.addEventListener('click', function() {
        const selectedTitle = titleSelect.value;
        if (titleToId[selectedTitle]) {
            window.location.href = updateInfoPath.replace('INFO_ID', titleToId[selectedTitle]);
        } else {
            alert('Veuillez sélectionner un titre valide.');
        }
    });
});