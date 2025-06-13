document.addEventListener('DOMContentLoaded', function() {
    const profileImg = document.getElementById('profileImg');
    const dropdown = document.getElementById('profileDropdown');
    const logoutBtn = document.getElementById('logoutBtn');

    if (profileImg && dropdown) {
        profileImg.addEventListener('click', function(e) {
            dropdown.style.display = dropdown.style.display === 'none' ? 'flex' : 'none';
        });

        document.addEventListener('click', function(e) {
            if (!profileImg.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            window.location.href = window.logoutUrl;
        });
    }
});