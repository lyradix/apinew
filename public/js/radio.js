// Hide modifyForm, updateForm, and refresh button by default
document.getElementById('modifyForm').style.display = 'none';
document.getElementById('updateForm').style.display = 'none';
document.getElementById('refreshModify').style.display = 'none';


document.querySelectorAll('input[name="formMode"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        if (this.value === 'add') {
            document.getElementById('addForm').style.display = '';
            document.getElementById('modifyForm').style.display = 'none';
            document.getElementById('updateForm').style.display = 'none';
        } else if (this.value === 'modify') {
            document.getElementById('addForm').style.display = 'none';
            document.getElementById('modifyForm').style.display = '';
            document.getElementById('updateForm').style.display = 'none';
            reloadPoiData();
        }
    });
});

document.getElementById('refreshModify').addEventListener('click', reloadPoiData);
