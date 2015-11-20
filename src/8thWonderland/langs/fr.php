<?php

/*
 * Fichier de langue en Français
 * 
 */
$array = array(
    // Langues disponibles
    "fr"                    => "Français",
    "en"                    => "Anglais",
    "es"                    => "Espagnol",
    
    // Connexion / Perte de mot de passe
    "login"                 => "Identifiant",
    "password"              => "Mot de passe",
    "confirmation_password" => "Confirmation du mot de passe",
    "connection"             => "Entrer",
    "process_forgetpwd"     => "Etape 1 : Saisissez votre login puis cliquez sur le bouton 'Envoyer'. Un code sera envoyé à l'adresse électronique correspondante.<br/>" .
                               "Etape 2 : Consultez votre boite aux lettres et saisissez le code indiqué dans le mail.<br/>" .
                               "Etape 3 : Vous recevrez un second mail comportant votre nouveau mot de passe.",
    "mail_forgetpwd"        => "Bonjour,<br/><br/>Vous recevez ce mail car une procedure de perte de mot de passe a ete declenchee.<br/>Si vous avez vous-meme " .
                               "declencher cette procedure, vous devez saisir le code ci dessous sur la page du site.<br/><br/>Si vous n'avez pas effectue cette procedure, " .
                               "vous n'avez aucune action a faire et votre mot de passe reste inchange.<br/><br/><b>Votre code est : </b>",
    "mail_newpwd"           => "Bonjour,<br/><br/>Vous trouverez votre nouveau mot de passe ci dessous. Si vous rencontrez des difficultes pour vous connecter, " .
                               "n'hesitez pas a nous contacter a cette adresse : developpeurs@8thwonderland.com<br/><br/><b>Votre nouveau mot de passe est : </b>",
    "reponse_newpwd"        => "Votre mot de passe vous a été envoyé.",
    "forget_pwd"            => "Mot de passe oublié",
    "code_forgetpwd"        => "Code",
    "btn_codeforgetpwd"     => "Valider",
    "logout"                => "Se déconnecter",
    
    'registration.password_mismatch' => 'La confirmation du mot de passe est incorrecte',
    'registration.invalid_login' => 'L\'identifiant indiqué est invalide',
    'registration.existing_login' => 'L\'identifiant indiqué n\'est pas disponible',
    'registration.invalid_mail' => 'L\'adresse électronique indiquée est invalide',
    'registration.existing_mail' => 'L\'adresse électronique indiquée est déjà utilisée',
    'registration.invalid_country' => 'Le pays indiqué n\'existe pas',
    'registration.invalid_region' => 'La région indiquée n\'existe pas',
    
    'chatrooms.link'        => 'Chat',
    
    'welcome'               => 'Bienvenue',
    'login_form_title'      => 'Se Connecter',
    'registration_form_title' => 'Rejoignez-nous',
    
    // Menu citoyen
    "inform"                => "S'informer",
    "share"                 => "Partager",
    "forum"                 => "Forum",
    "communicate"           => "Communiquer",
    "finance"               => "Financer",
    "admin"                 => "Administrer",
    
    
    // Menu présentation
    "presentation"          => "Présentation",
    "subscribe"             => "Rejoindre la communauté",
    "partners"              => "Nos Soutiens",
    "news"                  => "Evènements",
    "contact-us"            => "Contact",
    
    // partners
    'citadel_knights'       => 'Les Chevaliers de la Citadelle',
    
    // Menu groupes
    "calendar"              => "Calendrier",
    "adressbook"            => "Annuaire",
    "bookmark"              => "Favoris",
    "quit_desktop"          => "Quitter le bureau",
    
    
    // Titres des composants
    "shop"                  => "Magasin",
    "what_new"              => "Dernières infos",
    "friend_groups"         => "Groupes sympathisants",
    "motions_inprogress"    => "Motions en cours",
    "motions_archives"      => "Motions archivées",
    "stats_country"         => "Statistiques du pays",
    "title_groupsmembers"   => "Mes groupes",
    "title_groups"          => "Groupes régionaux et Groupes thématiques",
        

    // Partie 'TACHES'
    "tasks_inprogress"      => "Les tâches en cours",
    "display_archivestasks" => "Voir les tâches résolues",
    "description_task"      => "Description",
    "date_task"             => "Date",
    "author_task"           => "Auteur",
    "create_task_ok"        => "La tâche a été crée.",
    "create_task_nok"       => "La tâche n'a pas pu être créée !",
    "delete_task_ok"        => "La tâche a été supprimée.",
    "delete_task_nok"       => "La tâche n'a pas pu être supprimée !",
    "btn_createtask"        => "Créer une tâche",
    "btn_detailstask"       => "Voir",
    "btn_createtask"        => "Créer la tâche",
    

    // Partie 'MAIL'
    "contact_title"         => 'Titre',
    "contact_mail"          => 'Adresse électronique',
    "contact_message"       => 'message',
    "contact_send"          => 'Envoyer',
    "mail_ok"               => 'Votre message a bien été envoyé.',
    
    
    // Partie 'MESSAGING'
    "messaging"             => "Messagerie interne",
    "received_messages"     => "Messages reçus",
    "sent_messages"         => "Messages envoyés",
    "manager_contacts"      => "Contacts",
    "compose_message"       => "Composer un message",
    "box_receive"           => "Boîte de réception",
    "box_send"              => "Boîte d'envoi",
    "title_message"         => "Titre",
    "recipient_message"     => "Destinataires",
    "content_message"       => "Contenu",
    "sender_message"        => "Emetteur",
    "date_received"         => "Date",
    "no_message"            => "Aucun message",
    "create_group"          => "Créer un groupe",
    "delete_msg_ok"         => "Le message a été supprimé.",
    "delete_msg_nok"        => "Le message n'a pas pu être supprimé !",
    
    
    
    // Partie 'MOTIONS'
    "create_motion"         => "Déposer une motion",
    "display_archives"      => "Voir les archives",
    "title_motion"          => "Titre",
    "theme_motion"          => "Thème",
    "means_motion"          => "Moyens",
    "vote_approved"         => "J'approuve",
    "vote_refused"          => "Je refuse",
    "vote_approve"          => "Pour",
    "vote_refuse"           => "Contre",
    "status_motion"         => "Validée / refusée",
    "date_deposit"          => "Date dépôt",
    "date_end_vote"         => "Fin du vote",
    "author_motion"         => "Auteur",
    "organization"          => "Structure interne",
    "constitutional"        => "Constitutionnelle",
    "justice"               => "Judiciaire",
    "actions"               => "Actions externes",
    "description_motion"    => "Description",
    "depot_motion_ok"       => "La motion a été déposée.",
    "depot_motion_nok"      => "La motion n'a pas pu être déposée.",
    "create_motion_alert1"  => "Toute motion doit être soumise à un débat dans le forum avant d'être ajoutée au vote !<br/>" .
                               "Merci de vous assurer que la motion répond parfaitement à la Constitution et aux motions précédemment votées.<br/>" .
                               "Toutes les motions ne répondant pas à ces critères se verront annulées !<br/>" .
                               "En cas de doute, merci d'en discuter avec les citoyens.",
    "vote_motion_ok"        => "Le vote a été enregistré",
    "vote_motion_nok"       => "Le vote n'a pas pu être enregistré",
    "btn_createmotion"      => "Déposer",
    "btn_votemotion"        => "Voter",
    "result"                => "Résultat",
    "approved"              => "Validée à ",
    "refused"               => "Rejeté à ",
    
    
    // Partie 'GROUPS'
    "manager_group"         => "Gestion du groupe",
    "group_name"            => "Nom du groupe",
    "group_description"     => "Description",
    "group_contact"         => "Contact de groupe",
    "group_nbmembers"       => "Nbre de membres",
    "group_datecreation"    => "Date Creation",
    "group_type"            => "Type du groupe",
    "btn_enterdesktop"      => "Entrer",
    "group_members"         => "membres",
    "group_listmembers"     => "Membres du groupe",
    
    
    // Actions profils
    "update_profile"        => "Modifier votre profil",
    "btn_updateprofile"     => "Modifier",
    "read_mails"            => "Lire vos messages",
    "discover_groups"       => "Découvrir les groupes",
    
    
    // Mise à jour du profil
    "avatar"                => "Avatar",
    "last_connexion"        => "Dernière connexion",
    "subscription"          => "Inscription",
    "identity"              => "Pseudo",
    "mail"                  => "Email",
    "gender"                => "Sexe",
    "lang"                  => "Langue",
    "male"                  => "Homme",
    "female"                => "Femme",
    "country"               => "Pays",
    "region"                => "Région",
    "btn_subscribe"         => "S'inscrire",
    "btn_updatezone"        => "Mettre à jour",
    
    
    // erreurs & messages divers
    "dev_inprogress"        => "Ce module est en cours de développement.",
    "error"                 => "Une erreur est survenue !",
    "update_site"           => "Le site est actuellement en maintenance.<br/><br/>Les inscriptions sont momentanément fermées.",
    "subscribe_ok"          => "L'inscription s'est terminée avec succès",
    "modif_ok"              => "Vos modifications ont été faites.",
    "translate_notfound"    => "Le module de langue est introuvable !",
    "no_action"             => "L'action demandée est introuvable !",
    "connection_failed"     => "Connexion échouée !",
    "no_result"             => "Aucun résultat trouvé.",
    "no-subscription"       => "L'inscription est actuellement fermée.",
    "avatar_invalid"        => "L'avatar est invalide !",
    "identity_invalid"      => "L'identité est invalide !",
    "identity_exist"        => "Cette identité est déjà utilisée !",
    "mail_invalid"          => "L'adresse mail est invalide !",
    "mail_exist"            => "L'adresse mail existe déjà !",
    "fields_empty"          => "Tous les champs sont obligatoires !",
    "file_notfound"         => "Le fichier est introuvable !",
    
    
    // textes et descriptions
    "open_register"         => "Les inscriptions sont à nouveau ouvertes !! Cliquez sur 'Rejoindre la Communauté' et remplissez le formulaire.",
    "open_chat"             => "Venez discuter avec nous, <a style='font-weight:bold; font-size: 15px; color:#e14f1c;' href='/Intranet/openChatroom' target='_blank'>ici</a>.",
    "txt_profileaccess"     => "Attention ! Ces informations servent lors de la connexion sur ce site.",
    "page_previous"         => "Précédente",
    "page_next"             => "Suivante",
    "item_of"               => " de ",
    "unknown"               => "Inconnu",
    "back"                  => "Retour",
    "btn_search"            => "Rechercher",
    "btn_add"               => "Ajouter",
    "btn_modify"            => "Modifier",
    "btn_delete"            => "Supprimer",
    "periodicity_days"      => "Périodicité - Jours",
    "periodicity_hours"     => "Périodicité - Heures",
    "all_days"              => "Tous les jours",
    "all_hours"             => "Toutes les heures",
    "monday"                => "Lundi",
    "tuesday"               => "Mardi",
    "wednesday"             => "Mercredi",
    "thursday"              => "Jeudi",
    "friday"                => "Vendredi",
    "saturday"              => "Samedi",
    "sunday"                => "Dimanche",
    
    
    // console d'administration
    "stats_members"         => "Inscrits : ",
    "stats_members_actives" => "Actifs : ",
    "stats_region_unknown"  => "Régions inconnues : ",
    "admin_logs"            => "journaux de logs",
    "admin_users"           => "Utilisateurs",
    "admin_groups"          => "Groupes",
    "admin_server"          => "Serveur",
    "logs_level"            => "Niveau",
    "logs_description"      => "Description",
    "logs_date"             => "Date",
    "logs_delete"           => "Supprimer les logs",
    "cron_id"               => "Id",
    "cron_file"             => "Fichier",
    "cron_description"      => "Description",
    "btn_addcron"           => "Ajouter une tache",
    "cron_active"           => "Active",
    "quit_console"          => "Quitter la console",
    
    
    'decouvrir_8th'         => "<table><caption>Présentation du projet</caption>" .
                               "<tbody><tr><td><b>I) Nos origines.</b></td><tr>" .
                               "<tr><td>Le projet 8th Wonderland est en construction. Il s'inspire du film et du concept de la démocratie directe sur internet.<br/>" .
                               "Loin d'être un fan site, les objectifs sont bien réels : favoriser la création et le soutien d'initiatives citoyennes.<br/><br/></td></tr>" .
                               "<tr><td><b>II) Les objectifs</b></td><tr>" .
                               "<tr><td>8th Wonderland est un regroupement de personnes de par le monde. Celles-ci souhaitent apporter ou participer à un projet visant à construire des solutions face aux problèmes que rencontre la société.<br/><br/></td></tr>" .
                               "<tr><td><b>III) Le fonctionnement.</b></td><tr>" .
                               "<tr><td>La démocratie directe permet aux citoyens de contrôler ensemble les décisions à travers des motions. Les motions sont rédigées, débattues et votées par les citoyens." .
                               "La Constitution est le coeur de 8th Wonderland, contenant ses lois et ses principes." .
                               "Elle est modifiable uniquement par le vote de l'ensemble des citoyens.<br/><br/></td></tr></tbody>" .
                               "<tfoot><tr><td style='text-align:center;'><span style='color:red;'>B</span>ienvenue !</td></tr></tfoot></table>",
    
    'regions.missing_country_argument'  => 'Le pays doit être indiqué',
    
    'countries.france'      => 'France' 
);