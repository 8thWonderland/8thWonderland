// *********************************
// **  Gestionnaire d'évènements  **
// **  Auteur : Brennan WACO      **
// *********************************


// Détection de la fermeture du navigateur 
// et du rafraichissement de la page
// ---------------------------------------
var SortieSite 	= true;
/*
function interceptKeyPress(e) {
	if( !e ) {
		if (window.event)
			e = window.event;
		else
			return;
	}

	var keyCode = e.keyCode ? e.keyCode : e.which ? e.which : void 0;
	if(e.charCode == null || e.charCode == 0 ) {
		// F5 pressed
		if ( keyCode == 116 ) {
			SortieSite 	= false;
		}
	}
}

function attachEventListener( obj, type, func, capture ) {
	if(window.addEventListener) {
		//Mozilla, Netscape, Firefox
		obj.addEventListener( type, func, capture );
	} else {
		//IE
		obj.attachEvent( 'on' + type, func );
	}
}

$(window).unload(function() { 
	// unloading the page when the user is leaving
	if (SortieSite == true) {
		Clic('membres', 'deco');
	}
});

attachEventListener(document,"keypress",interceptKeyPress,true);*/


// initialisation des elements au chargement de la page
// ----------------------------------------------------
$(document).ready(function(){
	// mise en place du menu
	jQuery('#Menu-slider').jcarousel({visible: 4, scroll: 3});
});



// Gestionnaire d'evenements
// -------------------------
function Menu_Extend(p_ConteneurMenu)
{
	$('#'+p_ConteneurMenu).show();
	var PosDepart = $(document).width();
	var PosArrivee = $(document).width()-$('#'+p_ConteneurMenu).width();
	$('#'+p_ConteneurMenu).css("left", PosDepart);
	$('#'+p_ConteneurMenu).animate({"left": PosArrivee+"px"}, 1000);
}


function Gestion_Onglets(p_Widget, p_Titre, p_TitresOnglets)
{
	var LargEcran = $(window).width(); var HautEcran = $(window).height();
	var TitresOnglets = p_TitresOnglets.split('##');
	var obj = '<div id="' + p_Widget + '" title="' + p_Titre + '">';
	obj += '<div id="' + p_Widget + '_Onglet" class="ui-tabs ui-widget-content ui-corner-all">';
	obj += '<ul class="tabNavigation ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">';
	obj += '<li id="tab1" class="tabs ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a>' + TitresOnglets[0] + '</a></li>';
	for(var nOnglets=1; nOnglets<TitresOnglets.length; nOnglets++)
	{
		obj += '<li id="tab' + (nOnglets+1) + '" class="tabs ui-state-default ui-corner-top"><a>' + TitresOnglets[nOnglets] + '</a></li>';
	}
	obj += '</ul>';
	obj += '<div id="' + p_Widget + '_Tabs-Container" class="ui-tabs-panel ui-widget-content ui-corner-bottom"></div>';
	obj += '</div></div>';
	$(obj).appendTo(document.body);
	$("#"+ p_Widget).dialog({autoOpen: true, show: "scale", hide: "scale", height: (HautEcran*0.6), width: 1125, close: function(ev, ui) { $(this).remove(); }});
	
	// Rollover sur les onglets
	$("div#"+p_Widget+"_Onglet ul.tabNavigation li.tabs").hover(
		function(){
			$(this).addClass("ui-state-hover");
		},
		function(){
			$(this).removeClass("ui-state-hover");
		});
	
	// Contenu via ajax
	$("div#"+p_Widget+"_Onglet ul.tabNavigation li.tabs").bind("click", function()
	{
		$("div#"+p_Widget+"_Onglet ul.tabNavigation li.tabs").removeClass("ui-tabs-selected"); $("div#"+p_Widget+"_Onglet ul.tabNavigation li.tabs").removeClass("ui-state-active");
		var str = $(this).attr("id");
		$("#"+str).addClass("ui-tabs-selected"); $("#"+str).addClass("ui-state-active");
		
		var nOnglet = str.replace("tab", "");
		Ajax(p_Widget, "afficher_onglet", "&onglet="+nOnglet, p_Widget + "_Tabs-Container");
	});
	
	Ajax(p_Widget, "afficher_onglet", "&onglet=0", p_Widget + "_Tabs-Container");

}


function Clic(p_Module, p_Action, p_Args, p_Conteneur, p_Vue)
{
	// Gestion des arguments
	if (!(p_Args == undefined) && p_Args != '')
	{
		if (String(p_Args).substring(0, 4) == 'form')	{	var params = '&' + $("form#"+p_Args).serialize();	}
		else	{	var params = '&' + p_Args;	}
	}
	else
	{
		var params='';
	}
	
	if (p_Vue == "accueil")	{	p_URL = "/Intranet/application/controleurs/ctrl_connexion.php";	}
	else	{	p_URL = "";	}
	Ajax(p_Module, p_Action, params, p_Conteneur, p_URL);
}



// Fonction AJAX
// -------------
function Ajax(p_Module, p_Action, p_Params, p_Conteneur, p_Url)
{
	if (p_Conteneur == undefined || p_Conteneur == "")		{	var Conteneur = document.body;	}
	else	{	var Conteneur = $('#' + p_Conteneur);	}
	
	if (p_Url == undefined || p_Url == "")		{	var Url = "/Intranet/application/controleurs/ctrl_intranet.php";	}
	else	{	var Url = p_Url;	}
	
	var msg = $.ajax({
			type: "POST",
			url: Url,
			data: "module="+p_Module+"&action="+p_Action+p_Params,
			dataType: "json",
			async: false,
			success: function(data)
			{
				switch(data.success)
				{
					case 1:
						// traitement normal
						if (data.message != "redirection")
						{
							var obj = data.message;
							if (Conteneur != document.body)	{	Conteneur.empty();	}
							$(obj).appendTo(Conteneur);
						}
						else
						{
							window.location = data.page;
						}
						break;
				
					case 0:
						// erreur lors du traitement par le serveur
						if(data.success == 0)
						{
							var obj = '<div id="error" class="Erreur Notifications" style="position: absolute; left:35%; bottom:150px; width:30%;"><table><tr>';
							obj += '<td><img alt="erreur" src="/Ressources/Images/Icones/Erreur-32.png" style="width:48px; border:0"/></td>';
							obj += '<td>' + data.message + '</td>';
							obj += '</tr></table>';
							obj += '</div>';
							$(obj).appendTo(document.body);
						}
						break;
				}
			},
			
			// erreur de format du JSON
			error: function(data, texte, erreur)
			{
				var obj = '<div id="error" class="Erreur Notifications" style="position: absolute; left:35%; bottom:150px; width:30%;"><table><tr>';
				obj += '<td><img alt="erreur" src="/Ressources/Images/Icones/Erreur-32.png" style="width:48px; border:0"/></td>';
				obj += '<td>' + erreur + '</td>';
				obj += '</tr></table>';
				obj += '</div>';
				$(obj).appendTo(document.body);					
			}
	});
}