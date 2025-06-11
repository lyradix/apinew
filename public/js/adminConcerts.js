document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delBtn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const concertId = this.getAttribute('data-id');
            if (window.confirm('Supprimer ce concert ?')) {
                fetch('/deleteConcert/' + concertId, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Optionally remove the concert from the DOM
                        this.closest('li').remove();
                    } else {
                        alert('Erreur lors de la suppression.');
                    }
                });
            }
        });
    });
});