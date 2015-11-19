$(function() {
    $("#country").change(function() {
        getRegionsList();
    });
});

var deployed_panel = "login";

function getRegionsList() {
    var country = $("#country").val();
    
    if(country === 'None') {
        return false;
    }
    
    $.ajax({
        type: "GET",
        url: "country/regions", 
        dataType: "json",
        success : function(regions) {
            var regions_list = $("select[name=region]");
            regions_list.html('<option value="None"></option>');
            $.each(regions, function(index, region) {
                regions_list.append('<option value="' + region.id + '">' + region.name + '</option>');
            });
        },
        data: "country_id=" + $("#country").val(), 
        error: function(data, texte, erreur) {
           console.log(data);
           console.log(texte);
           console.log(erreur);
        }
    });
}

function authenticate() {
    var authentication = {
        login: $("#login-form input[name=login]").val(),
        password: $("#login-form input[name=password]").val()
    };
    $.ajax({
        type: "POST",
        url: "authenticate/connect",
        dataType: "json",
        contentType: "application/json",
        success : function(data) {
            window.location.href = '.';
        },
        data: JSON.stringify(authentication), 
        error: function(error)
        {
            console.log(error);
        }
    });
}

function register() {
    var registration = {
        login: $("#login-form input[name=login]").val(),
        email: $("#login-form input[name=email]").val(),
        password: $("#login-form input[name=password]").val(),
        confirmation_password: $("#login-form input[name=confirmation_password]").val(),
        country: $("#login-form select[name=country]").val(),
        region: $("#login-form select[name=region]").val()
    };
    
    $.ajax({
        type: "POST",
        url: "authenticate/subscribe", 
        dataType: "json",
        contentType: "application/json",
        success : function(data) {
            console.log(data);
        },
        data: JSON.stringify(registration), 
        error: function(data, texte, erreur)
        {
            console.log(data);
            console.log(texte);
            console.log(erreur);
        }
    });
}

function deployForm(deploy, remove) {
    if(deployed_panel === deploy) {
        return false;
    }
    deployed_panel = deploy;
    $("#" + deploy + "-form").slideUp("normal", function() {
        $(this).children().filter('form').css('display', 'block');
        $(this).prependTo("#account-forms").slideDown("normal");
        $("#" + remove + "-form form").slideUp("normal");
    });
}