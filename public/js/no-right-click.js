$(document).ready(function() {
    $(document).on("contextmenu", function(e) {
        e.preventDefault();
        toastr.warning('This action is disabled!');
    });
    document.addEventListener('keydown', function(e) {
        // Disable Ctrl+U (View Source), Ctrl+Shift+I (DevTools), and F12
        if ((e.ctrlKey && e.key === 'u') || 
            (e.ctrlKey && e.shiftKey && e.key === 'I') || 
            e.key === 'F12') {
          e.preventDefault();
          toastr.warning('This action is disabled!');
        }
      });
      document.addEventListener('keyup', function (e) {
        if (e.key === 'PrintScreen') {
          toastr.warning('Screenshots are disabled!');
        
        }
      });
});

