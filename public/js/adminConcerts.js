// au chargement de la page, ajoute un event listener pour le bouton delete
// pour supprimer un concert
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delBtn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const concertId = this.getAttribute('data-id');
            const btn = this; // store reference to the button
            // un window de confirmation avant de supprimer
            if (window.confirm('Supprimer ce concert ?')) {
                fetch('/deleteConcert/' + concertId, {
                    // l'utilisation de la méthode DELETE
                    method: 'DELETE',
                    // données envoyés en format JSON avec un header
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                                alert('Concert supprimé avec succès !');
                        btn.closest('li').remove();
                   } else if (data.error) {
                            alert('Erreur : ' + data.error);
                        }
                        else {
                            alert('Une erreur est survenue.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Erreur lors de la suppression');
                    });
            }
        });
    });
});