$(function() {
    $("#country").change(function() {
        getRegionsList();
    });
});

var website_root = $("input[name=website-root]").val();
var deployed_panel = "login";

function getRegionsList() {
    var country = $("#country").val();
    
    if(country === 'None') {
        return false;
    }
    
    $.ajax({
        type: "GET",
        url: website_root + "country/regions", 
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
        url: website_root + "authenticate/connect",
        contentType: "application/json",
        data: JSON.stringify(authentication),
        error: function(data)
        {
            displayFormErrors('login', JSON.parse(data.responseText));
        }
    }).done(function(data) {
        document.write(data);
    });
}

function register() {
    var registration = {
        login: $("#registration-form input[name=login]").val(),
        email: $("#registration-form input[name=email]").val(),
        password: $("#registration-form input[name=password]").val(),
        confirmation_password: $("#registration-form input[name=confirmation_password]").val(),
        country_id: $("#registration-form select[name=country]").val(),
        region_id: $("#registration-form select[name=region]").val()
    };
    
    $.ajax({
        type: "POST",
        url: website_root + "authenticate/subscribe", 
        dataType: "text",
        contentType: "application/json",
        success : function(data) {
            $("#login-form input[name=login]").val(registration.login);
            $("#login-form input[name=password]").val(registration.password);
            deployForm('login', 'registration');
        },
        data: JSON.stringify(registration), 
        error: function(data) {
            displayFormErrors('registration', JSON.parse(data.responseText));
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

function displayFormErrors(formName, data) {
    var errorsList = "<ul>";
    for (var i = 0; i < data.errors.length; i++) {
        errorsList += "<li>" + data.errors[i] + "</li>";
    }
    errorsList += "</ul>";
    
    $("#" + formName + "-form .form-errors").slideUp("fast").html(errorsList).slideDown("normal");
}