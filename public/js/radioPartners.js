// Hide modifyForm, updateForm, and refresh button by default
if (document.getElementById('modifyForm')) document.getElementById('modifyForm').style.display = 'none';
if (document.getElementById('updateForm')) document.getElementById('updateForm').style.display = 'none';
if (document.getElementById('refreshModifyPartner')) document.getElementById('refreshModifyPartner').style.display = 'none';
// if (document.getElementById('reloadBtn')) document.getElementById('reloadBtn').style.display = 'none';




document.querySelectorAll('input[name="formMode"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        if (this.value === 'add') {
            if (document.getElementById('addForm')) document.getElementById('addForm').style.display = '';
            if (document.getElementById('modifyForm')) document.getElementById('modifyForm').style.display = 'none';
            if (document.getElementById('updateForm')) document.getElementById('updateForm').style.display = 'none';
        } else if (this.value === 'modify') {
            if (document.getElementById('addForm')) document.getElementById('addForm').style.display = 'none';
            if (document.getElementById('modifyForm')) document.getElementById('modifyForm').style.display = '';
            if (document.getElementById('updateForm')) document.getElementById('updateForm').style.display = 'none';
            // Only call reloadPartnersData if it exists
            if (typeof window.reloadPartnersData === 'function') {
                window.reloadPartnersData();
            } else if (typeof reloadPartnersData === 'function') {
                reloadPartnersData();
            } else {
                // Optionally, show an error or fallback
                // alert('reloadPartnersData is not available');
            }
        }
    });
});



if (document.getElementById('refreshModifyPartner')) {
    if (typeof window.reloadPartnersData === 'function') {
        document.getElementById('refreshModifyPartner').addEventListener('click', window.reloadPartnersData);
    } else if (typeof reloadPartnersData === 'function') {
        document.getElementById('refreshModifyPartner').addEventListener('click', reloadPartnersData);
    }
}