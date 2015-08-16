// *********************************
// **  Gestionnaire d'évènements  **
// **  Auteur : Brennan WACO      **
// *********************************



// Envoi d'un formulaire via Ajax
// ==============================
function Envoi_form(p_Action, p_NomForm, p_Conteneur, p_Datatype)
{
    if (p_Action.charAt(0) != "/")     {p_Action = "/" + p_Action;}
    Action = p_Action.split("/");
    if (p_NomForm == undefined || p_NomForm == '')      {exit();}
    var params = '&' + $("form#"+p_NomForm).serialize();
    var datatype = "html";

    // Gestion du type de données (html, json, xml)
    if (p_Datatype == 'html' || p_Datatype == 'json' || p_Datatype == 'xml')    {datatype = p_Datatype;}

    Ajax(Action, params, p_Conteneur, datatype);
}


// Envoi d'une requete de clic via Ajax
// ====================================
function Clic(p_Action, p_Args, p_Conteneur, p_Datatype)
{
    if (p_Action.charAt(0) != "/")     {p_Action = "/" + p_Action;}
    Action = p_Action.split("/");
    var params='';
    var datatype = "html";

    // Gestion des arguments
    if (!(p_Args == undefined) && p_Args != '')    {params = '&' + p_Args;}

    // Gestion du type de données (html, json, xml)
    if (p_Datatype == 'html' || p_Datatype == 'json' || p_Datatype == 'xml')    {datatype = p_Datatype;}

    Ajax(Action, params, p_Conteneur, datatype);
}


// Envoi d'une requete de redirection via AJax
// ===========================================
function Redirect(p_Action, p_Args)
{
    if (p_Action.charAt(0) != "/")     {p_Action = "/" + p_Action;}
    Action = p_Action.split("/");

    // Gestion des arguments
    var params='';
    if (!(p_Args == undefined) && p_Args != '')    {params = '&' + p_Args;}

    Ajax(Action, params, 'body', 'html');
}


// Envoi d'une requete pour l'autocomplete
// =======================================
function Autocomplete(p_field, p_Action, p_Conteneur) 
{
    if (p_Action.charAt(0) != "/")     {p_Action = "/" + p_Action;}
    Action = p_Action.split("/");
    
    Ajax(Action, '', p_Conteneur, 'html', false);
    $("#"+p_field).autocomplete({
        source: $("#"+p_Conteneur).val()
    });
}


// Fonction AJAX
// =============
function Ajax(p_Action, p_Params, p_Conteneur, p_DataType, p_Async)
{
    var Async = true;
    if (!(p_Async == undefined) && p_Async != '')    {Async = p_Async;}
    $.ajax(
    {
        url: "index.php",
        type: "POST",
        async : Async,
        dataType: p_DataType,
        data: "controller=" + p_Action[1] + "&action=" + p_Action[2] + p_Params,
        error: function(data, texte, erreur)
        {
           var obj = '<div id="error" class="error" style="position: absolute; left:35%; bottom:150px; width:30%;"><table><tr>';
           obj += '<td><img alt="erreur" src="/public/icones/64x64/Error.png" style="width:48px; border:0"/></td>';
           obj += '<td>' + erreur + '</td>';
           obj += '</tr></table>';
           obj += '</div>';
           $(obj).appendTo(document.body);
        },
        success: function(data)
        {
            var obj = '';
            switch(data.status)
            {
                case 0:
                    // traitement normal
                    if (p_Conteneur != 'body')  {
                        $("#"+p_Conteneur).empty();
                        $(data.reponse).appendTo($("#"+p_Conteneur));
                    } else {
                        $('body').empty();
                        $(data.reponse).appendTo($('body'));
                    }
                    break;

                case 1:
                    // traitement de redirection
                    Redirect(data.reponse);
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
                    if (p_Conteneur != 'body')  {
                        $("#"+p_Conteneur).empty();
                        $(data).appendTo($("#"+p_Conteneur));
                    } else {
                        $('body').empty();
                        $(data).appendTo($('body'));
                    }
                    break;
            }
       }
    });
}
