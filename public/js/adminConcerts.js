// au chargement de la page, ajoute un event listener pour le bouton delete
// pour supprimer un concert
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delBtn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const concertId = this.getAttribute('data-id');
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
                        //l'objet this est référencé par le bouton cliqué
                        // et le concert est supprimé de la liste
                        this.closest('li').remove();
                    } else {
                        //message d'erreur si la suppression échoue
                        alert('Erreur lors de la suppression.');
                    }
                });
            }
        });
    });
});