<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background-color: #048e2b;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="fullscreen" href="#" role="button" id="fullscreen-toggle">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fullscreenToggle = document.getElementById('fullscreen-toggle');
    
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(console.error);
                localStorage.setItem('fullscreen', 'true');
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
                localStorage.setItem('fullscreen', 'false');
            }
        }
    
        fullscreenToggle.addEventListener('click', function(event) {
            event.preventDefault();
            toggleFullscreen();
        });
    
        // Check if fullscreen mode was enabled on the previous visit
        if (localStorage.getItem('fullscreen') === 'true') {
            document.documentElement.requestFullscreen().catch(console.error);
        }
    });

</script>