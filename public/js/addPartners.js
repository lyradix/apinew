//initialise variables

let partnerData = {};



document.addEventListener('DOMContentLoaded', function() {
    fetch('partners')
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

