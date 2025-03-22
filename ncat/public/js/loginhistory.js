$(document).ready(function () {


    $("#login-history-table").dataTable({
        "responsive": true,
        "autoWidth": false,
        "destroy": true,
        "ajax": {
            "url": 'getLoginHistory'
        },
        "columns": [
            { data: "username" },
            { data: "ip_address" },
            {
                data: "login_at",
                render: function(data) {
                    return formatDate(data);
                }
            },
            {
                data: "logout_at",
                render: function(data) {
                    return formatDate(data);
                }
            },
            { data: "user_agent" }
        ]
    });
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        
        const optionsDate = { year: 'numeric', month: 'long', day: 'numeric' };
        const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        
        const formattedDate = date.toLocaleDateString('en-US', optionsDate);
        const formattedTime = date.toLocaleTimeString('en-US', optionsTime);
        
        return `${formattedDate} ${formattedTime}`;
    }
    
    
    
   

});