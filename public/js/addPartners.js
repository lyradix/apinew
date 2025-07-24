//initialise variables

let partnerData = {};



document.addEventListener('DOMContentLoaded', function() {
    fetch('/partners')
    .then(response => response.json() )
    .then(data => {
        const select = document.getElementById('Id');
        const typeSelect = document.getElementById('typeSelect');
        const typeSet = new Set();
    
        data.partners.forEach( partner => {
            const option = document.createElement('option');
            option.value = partner.id;  
            option.textContent = partner.title + partner.id;
            select.appendChild(option);


    });

      // Fill type select
            typesSet.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                typeSelect.appendChild(option);
            });

    //Add event listener for partners select change
    select.addEventListener('change', function(){
        const selectedId = this.value;
         const frontPageCheckBox = document.querySelector
        ('input[name="frontPage"]').value === 'true';
        const type = document.getElementById('typeSelect');
        const linkInput = document.querySelector
        ('#modifForm input[placeholder^="https//:.."]');
        if(partnerData[selectedId]){
            document.getElementById('typeSelect').value = partnerData[selectedId].data;
        }else {

             document.getElementById('typeSelect').value = '';
        }
    })
});

document.getElementById('modifForm').addEventListener('submit', function(e){
    e.preventDefault();
    const Id = document.getElementById('Id').value;
    const frontPageCheckBox = document.querySelector
        ('input[name="frontPage"]').value === 'true';
    const type = document.getElementById('typeSelect').value;
    const linkInput = document.querySelector
        ('#modifForm input[placeholder^="https//:.."]');
  
        if (partnerData){
            fetch('./update-partner',{
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id : Id,
                    frontPage : frontPageCheckBox,
                    typeSelect : type,
                    link : linkInput
                })
             
            })
           .then(response => response.json())
                .then(data => {
                    if(data.sucess || data.message){
                        alert('Partenaire commerciale modifié avec succès');
                    } else {
                        alert('Erreur : ' + (data.error || 'Une erreur est survenue.'));
                    }
                })
                .catch(()=>alert('Erreur lors de l\'envoi du formulaire'));
        }
  
    }) 
});

//function to reload partners data and reset the modify form
function reloadpartnersData(){
    const select = document.getElementById('Id');
    const typeSelect = document.getElementById('typeSelect');
    select.innerHTML = '<option value="">-- Choisir un partenaire --</option>';
    typeSelect.innerHTML = '<option value="">-- Choisir un type --</option>';
    fetch('/partners')
    .then(response => response.json())
    .then(data =>{
        const typeSelect = new Set();
            data.forEach(partner => {
                const option = document.getElementById('option');
               option.value = partner.id;  
               option.textContent = partner.title + partner.id;
               select.appendChild(option);
            });
     // Fill type select
            typesSet.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                typeSelect.appendChild(option);
            });

    //Add event listener for partners select change
    select.addEventListener('change', function(){
        const selectedId = this.value;
        if(partnerData[selectedId]){
            document.getElementById('typeSelect').value = partnerData[selectedId].data;
        }else {

             document.getElementById('typeSelect').value = '';
        }
    })        
    })
}

document.getElementById('refreshModify').addEventListener('click', reloadpartnersData);


document.getElementById('partnerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const typeField = form.querySelector('[name$="[type]"]');
    const type = typeField ? typeField.value : '';

    if(type ==='scène') {
        fetch(window.addSceneUrl, {
            method: 'PUT'?
            BODY: formData,
            headers: {'X-Requested-Width': 'WMLHtpRequest'}
        })
        .then(response => response.json())
        .then(data => {
            if(data.sucess) {
                alert('Partnaire commerciale ajouté avec succès')
                form.reset();
            }
            else{
               alert('Erreur : ' + (data.error || 'Une erreur est survenue.')); 
            }
        }).catch(()=>alert('Erreur lors de l\'envoi du formulaire.'))
    }else{
        form.submit();
    }
});


// document.querySelectorAll('input[name="formMode"]').forEach(function(radio){
//         radio.addEventListener('change', function(){
//             if(this.value === 'add'){
//                 document.getElementById('addForm').style.display = '';
//                 document.getElementById('modifyForm').style.display = 'none';
//                 document.getElementById('updateForm').style.display = 'none';
//             } else if(this.value === 'modify'){
//                 document.getElementById('addForm').style.display = 'none';
//                 document.getElementById('modifyForm').style.display = '';
//                 document.getElementById('updateForm').style.display = 'none';
//             }
//         })
// })



