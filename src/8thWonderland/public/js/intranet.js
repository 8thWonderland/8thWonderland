var website_root = $("input[name=website-root]").val();

$.fn.serializeFormJSON = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function log_out() {
    $.ajax({
        type: "GET",
        url: website_root + "authenticate/logout",
        success: function(data) {
            window.location.href = website_root;
        }
    });
}