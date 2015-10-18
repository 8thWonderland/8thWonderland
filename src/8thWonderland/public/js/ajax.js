function sendForm(action, formName, container, dataType) {
    if (formName === undefined || formName === '') {
        return false;
    }
    var params = $("form#"+formName).serialize();
    Ajax(action, params, container, dataType);
}

function Clic(action, arguments, container, dataType) {
    var params = '';

    if (arguments !== undefined && arguments !== '') {
        params = '&' + arguments;
    }
    Ajax(action, params, container, dataType);
}

function Redirect(action, arguments) {
    // Gestion des arguments
    var params='';
    if (arguments !== undefined && arguments !== '') {
        params = '&' + arguments;
    }
    Ajax(action, params, 'body', 'html');
}


// Envoi d'une requete pour l'autocomplete
// =======================================
function Autocomplete(field, action, container) {
    Ajax(action, '', container, 'html', false);
    $("#"+field).autocomplete({
        source: $("#"+container).val()
    });
}

function Ajax(action, params, container, dataType, isAsync) {
    var async = (isAsync !== undefined && isAsync !== '') ? isAsync : true;
    $.ajax({
        url: action,
        type: "POST",
        async : async,
        dataType: dataType,
        data: params,
        error: function(data, text, error) {
           var obj = '<div id="error" class="error" style="position: absolute; left:35%; bottom:150px; width:30%;"><table><tr>';
           obj += '<td><img alt="erreur" src="/public/icones/64x64/Error.png" style="width:48px; border:0"/></td>';
           obj += '<td>' + error + '</td>';
           obj += '</tr></table>';
           obj += '</div>';
           $(obj).appendTo(document.body);
        },
        success: function(data) {
            var obj = '';
            switch(data.status)
            {
                case 0:
                    // traitement normal
                    if (container !== 'body')  {
                        $("#"+container).empty();
                        $(data.reponse).appendTo($("#"+container));
                    } else {
                        $('body').empty();
                        $(data.reponse).appendTo($('body'));
                    }
                    break;

                case 1:
                    // traitement de redirection
                    Redirect(data.response);
                    break;

                case 2:
                    // erreur coté serveur
                    obj = '<div id="error" class="error" style="position: absolute; left:35%; bottom:150px; width:30%;"><table><tr>';
                    obj += '<td><img alt="erreur" src="/public/icones/64x64/Error.png" style="width:48px; border:0"/></td>';
                    obj += '<td>' + data.reponse + '</td></tr></table></div>';
                    $(obj).appendTo(document.body);
                    break;

                default:
                    // traitement normal quand les réponses ne sont pas au format JSON
                    if (container !== 'body')  {
                        $("#"+container).empty();
                        $(data).appendTo($("#"+container));
                    } else {
                        $('body').empty();
                        $(data).appendTo($('body'));
                    }
                    break;
            }
       }
    });
}
