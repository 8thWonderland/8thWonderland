var website_root = $("input[name=website-root]").val();

function log_out() {
    $.ajax({
        type: "GET",
        url: website_root + "authenticate/logout",
        success: function(data) {
            window.location.href = website_root;
        }
    });
}