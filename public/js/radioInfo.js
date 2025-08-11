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
            // Only call reloadInfoData if it exists
            if (typeof window.reloadInfoData === 'function') {
                window.reloadInfoData();
            } else if (typeof reloadInfoData === 'function') {
                reloadInfoData();
            } else {
                // Optionally, show an error or fallback
                // alert('reloadInfoData is not available');
            }
        }
    });
});



if (document.getElementById('refreshModifyPartner')) {
    if (typeof window.reloadInfoData === 'function') {
        document.getElementById('refreshModifyPartner').addEventListener('click', window.reloadInfoData);
    } else if (typeof reloadInfoData === 'function') {
        document.getElementById('refreshModifyPartner').addEventListener('click', reloadInfoData);
    }
}