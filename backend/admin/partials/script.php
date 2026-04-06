<!-- Sisa script lama Material Dashboard telah dihapus untuk mencegah bentrok dengan Bootstrap 5 -->
<!-- JQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Sidebar Toggle for Mobile
    $('#sidebarToggle, #sidebarOverlay').on('click', function() {
        $('body').toggleClass('sidebar-open');
    });

    // Close sidebar when clicking a nav link on mobile
    $('.sidebar .nav-link').on('click', function() {
        if (window.innerWidth <= 1024) {
            $('body').removeClass('sidebar-open');
        }
    });

    // Handle window resize
    $(window).on('resize', function() {
        if (window.innerWidth > 1024) {
            $('body').removeClass('sidebar-open');
        }
    });
});
</script>