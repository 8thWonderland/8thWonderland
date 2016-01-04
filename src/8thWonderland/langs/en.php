<?php

/*
 * Fichier de langue en Français
 * 
 */
$array = array(
    // Langues disponibles
    "fr"                    => "french",
    "en"                    => "english",
    "es"                    => "spanish",
    
    
    // Connexion / Perte de mot de passe
    "login"                 => "Login",
    "password"              => "Password",
    "confirmation_password" => "Confirm Password",
    "connection"             => "Enter",
    "process_forgetpwd"     => "Step 1 : Type your login and click 'Send'. A code will be sent to the mail adress.<br/>" .
                               "Step 2 : Read yours mails and type the code indicated in the e-mail.<br/>" .
                               "Step 3 : You will receive a second mail containing your new password.",
    "mail_forgetpwd"        => "Hello,<br/><br/>You receive this e-mail because a procedure of loss of password has been activated.<br/>If you have to start " .
                               "this procedure, you have type the code below in the website.<br/><br/>If you don't have to start this, " .
                               "you have no action has to make and your password remains inchange.<br/><br/><b>Your code is : </b>",
    "mail_newpwd"           => "Hello,<br/><br/>You will find your new password this below. If you have problems to connect, " .
                               "don't hesitate to contact us : developpeurs@8thwonderland.com<br/><br/><b>Your new password is : </b>",
    "reponse_newpwd"        => "Your new password has been sent.",
    "forget_pwd"            => "Forget password",
    "code_forgetpwd"        => "Code",
    "btn_codeforgetpwd"     => "Valid",
    "logout"                => "Se déconnecter",
    
    'chatrooms.link'        => 'Chat',
    
    'registration.password_mismatch' => 'La confirmation du mot de passe est incorrecte',
    'registration.invalid_login' => 'L\'identifiant indiqué est invalide',
    'registration.existing_login' => 'L\'identifiant indiqué n\'est pas disponible',
    'registration.invalid_mail' => 'L\'adresse électronique indiquée est invalide',
    'registration.existing_mail' => 'L\'adresse électronique indiquée est déjà utilisée',
    'registration.invalid_country' => 'Le pays indiqué n\'existe pas',
    'registration.invalid_region' => 'La région indiquée n\'existe pas',
    
    'welcome'               => 'Welcome',
    'login_form_title'      => 'Log in',
    'registration_form_title' => 'Join us',
    
    // Menu citoyen
    "inform"                => "Inform me",
    "share"                 => "Share",
    "forum"                 => "Forum",
    "communicate"           => "Communicate",
    "finance"               => "Donate",
    "admin"                 => "Manage",
    
    
    // Menu présentation
    "presentation"          => "Presentation",
    "subscribe"             => "Join the community",
    "partners"              => "Our network",
    "news"                  => "Events",
    "contact-us"            => "Contact",
        
    // partners
    'citadel_knights'       => 'Les Chevaliers de la Citadelle',
    
    // Menu groupes
    "calendar"              => "Calendar",
    "adressbook"            => "Adress Book",
    "bookmark"              => "Bookmark",
    "quit_desktop"          => "Quit the desktop",

    
    // Titres des composants
    "shop"                  => "Magasin",
    "what_new"              => "Last news",
    "friend_groups"         => "Friendly groups",
    "motions_inprogress"    => "Motions en cours",
    "motions_archives"      => "Classified motions",
    "stats_country"         => "Statistics of the country",
    'my_groups'             => 'My groups',
    
    
    // Groupes
    'groups'                => 'Groups',
    'groups.thematic'       => 'Thematic Groups',
    'groups.regional'       => 'Regional Groups',
    'groups.back_to_list'   => 'Back to the groups list',
    
    // Motions
    'motions.vote'          => 'Vote',
    'motions.new_title'     => 'Create a motion',
    'motions.creation_date' => 'Creation date',
    'motions.end_date'      => 'End date',
    'motions.select_type_for_duration' => 'Depending on the motion\'s theme',
    'motions.theme'         => 'Motion\'s theme',
    'motions.title'         => 'Motion\'s title',
    'motions.description'   => 'Motion\'s description',
    'motions.means'         => 'Application means',
    'motions.validate'      => 'Create the motion',
    'motions.already_voted' => 'You already voted this motion',
    'motions.vote.request'  => 'Do you approve this motion ?',
    'motions.vote.yes'      => 'Yes',
    'motions.vote.no'       => 'No',
    
    // Partie 'TACHES'
    "tasks_inprogress"      => "The tasks in progress",
    "display_archivestasks" => "Display the archives",
    "description_task"      => "Description",
    "date_task"             => "Date",
    "author_task"           => "Author",
    "create_task_ok"        => "The task has been created.",
    "create_task_nok"       => "The task has not been created !",
    "delete_task_ok"        => "The task has been deleted.",
    "delete_task_nok"       => "The task was not able to be deleted !",
    "btn_createtask"        => "Create a task",
    "btn_detailstask"       => "See",
    "btn_createtask"        => "Create the task",
        

    // Partie 'MAIL'
    "contact_title"         => 'Title',
    "contact_mail"          => 'Email',
    "contact_message"       => 'Message',
    "contact_send"          => 'Send',
    "mail_ok"               => 'Your message has been sent successfully.',
    
    
    // Partie 'MESSAGING'
    "messaging"             => "Internal messaging",
    "received_messages"     => "Received messages",
    "sent_messages"         => "Sent message",
    "manager_contacts"      => "Contacts",
    "compose_message"       => "Compose a message",
    "box_receive"           => "Reception Box",
    "box_send"              => "Sent box",
    "title_message"         => "Title",
    "recipient_message"     => "Recipients",
    "content_message"       => "Content",
    "sender_message"        => "Author",
    "date_received"         => "Date",
    "no_message"            => "No message",
    "create_group"          => "Create a group",
    "delete_msg_ok"         => "The message has been deleted.",
    "delete_msg_nok"        => "The message has not been deleted !",
    
    
    // Partie 'MOTIONS'
    "create_motion"         => "Create a motion",
    "display_archives"      => "Display the archives",
    "title_motion"          => "Title",
    "theme_motion"          => "Theme",
    "means_motion"          => "Means",
    "vote_approved"         => "I approve",
    "vote_refused"          => "I refuse",
    "vote_approve"          => "Approve",
    "vote_refuse"           => "Refuse",
    "status_motion"         => "Validated / refused",
    "date_deposit"          => "Date deposit",
    "date_end_vote"         => "End vote",
    "author_motion"         => "Author",
    "organization"          => "Organization",
    "constitutional"        => "Constitutional",
    "justice"               => "Justice",
    "actions"               => "Externals actions",
    "description_motion"    => "Description",
    "depot_motion_ok"       => "The motion has been deposited.",
    "depot_motion_nok"      => "The motion has not been deposited.",
    "create_motion_alert1"  => "All motions must be subjected to a debate in the forum before being added to the vote!<br/>" .
                               "Thank you for assuring you that the motion answers perfectly the Constitution and the previously voted motions.<br/>" .
                               "All motions not answering these criteria will see each other cancelled!<br/>" .
                               "In case of doubt, thank you for discussing it with the citizens.",
    "vote_motion_ok"        => "The poll has been saved.",
    "vote_motion_nok"       => "The poll has not been saved !",
    "btn_createmotion"      => "Deposit",
    "btn_votemotion"        => "Vote",
    "result"                => "Result",
    "approved"              => "Validated at ",
    "refused"               => "Refused at ",    
    
    // Partie 'GROUPS'
    "manager_group"         => "Manager of the group",
    "group_name"            => "Name of the group",
    "group_description"     => "Description",
    "group_contact"         => "Contact of group",
    "group_nbmembers"       => "Numbers of members",
    "group_datecreation"    => "Creation date",
    "group_type"            => "Type of the group",
    "btn_enterdesktop"      => "Enter",
    "group_members"         => "members",
    "group_listmembers"     => "Members of group",
    
    
    // Actions profils
    "update_profile"        => "Modify your profile",
    "btn_updateprofile"     => "Modify",
    "read_mails"            => "Read yours messages",
    "discover_groups"       => "Discover the groups",
    
    
    // Mise à jour du profil
    "avatar"                => "Avatar",
    "last_connexion"        => "Last connection",
    "subscription"          => "Subscription",
    "identity"              => "Nickname",
    "mail"                  => "Adr. Electronique",
    "gender"                => "Sex",
    "lang"                  => "Lang",
    "male"                  => "Man",
    "female"                => "Woman",
    "country"               => "Country",
    "region"                => "Region",
    "btn_subscribe"         => "register",
    "btn_updatezone"        => "Update",
    
    
    // erreurs & messages divers
    "dev_inprogress"        => "This module is in maintenance.",
    "error"                 => "An error appeared !",
    "update_site"           => "The website is actually in update.<br/><br/>The registrations are closed during this time.",
    "subscribe_ok"          => "The subscription is finished with success.",
    "modif_ok"              => "Vos modifications ont été faites.",
    "translate_notfound"    => "The module of translate is not found !",
    "no_action"             => "The action is not found !",
    "connection_failed"     => "Connection failed !",
    "no_result"             => "No résult found.",
    "no-subscription"       => "The subscription is closed actually.",
    "avatar_invalid"        => "The avatar is invalid !",
    "identity_invalid"      => "The identity is invalid !",
    "identity_exist"        => "This identity already exists !",
    "mail_invalid"          => "The email is invalid !",
    "mail_exist"            => "The email already exists !",
    "fields_empty"          => "All fields are required !",
    "file_notfound"         => "The file is not found !",
    
    
    // textes et descriptions
    "open_register"         => "The registrations are re-opened !! Click 'Join the community' and fill the form.",
    "open_chat"             => "Come to discuss with us. <a style='font-weight:bold; font-size: 15px; color:#e14f1c;' href='/Intranet/openChatroom' target='_blank'>Click here</a> to open the chat ...",
    "txt_profileaccess"     => "Attention ! Ces informations servent lors de la connexion sur ce site.",
    "page_previous"         => "Previous",
    "page_next"             => "Next",
    "item_of"               => " of ",
    "unknown"               => "Unknown",
    "back"                  => "Retour",
    "btn_search"            => "Search",
    "btn_add"               => "Add",
    "btn_modify"            => "Modify",
    "btn_delete"            => "Delete",
    "periodicity_days"      => "Periodicity - Days",
    "periodicity_hours"     => "Periodicity - Hours",
    "all_days"              => "All days",
    "all_hours"             => "All hours",
    "monday"                => "Monday",
    "tuesday"               => "Tuesday",
    "wednesday"             => "Wednesday",
    "thursday"              => "Thursday",
    "friday"                => "Friday",
    "saturday"              => "Saturday",
    "sunday"                => "Sunday",
    
    
    // console d'administration
    "stats_members"         => "Subscribers : ",
    "stats_members_actives" => "Actives : ",
    "stats_region_unknown"  => "Regions unknown : ",
    "admin_logs"            => "Events logs",
    "admin_users"           => "Users",
    "admin_groups"          => "Groups",
    "admin_server"          => "Server",
    "logs_level"            => "Level",
    "logs_description"      => "Description",
    "logs_date"             => "Date",
    "logs_delete"           => "Supprimer les logs",
    "cron_id"               => "Id",
    "cron_file"             => "File",
    "cron_description"      => "Description",
    "btn_addcron"           => "Add a task",
    "cron_active"           => "Active",
    "quit_console"          => "Exit the monitor",
    
    
    'decouvrir_8th'         => "<table><caption>Presentation of the project</caption>" .
                               "<tbody><tr><td><b>I) Our origins.</b></td><tr>" .
                               "<tr><td>8th Wonderland project is under construction. It takes roots from the film that he tool name, and from the concept about Direct Democracy on the Internet.<br/>" .
                               "Its not a fan site, there are clear objectives: allow and encourage populars actions.<br/><br/></td></tr>" .
                               "<tr><td><b>II) Objectives</b></td><tr>" .
                               "<tr><td>8th Wonderland consists of people all around the world. Each of them creates or participates in that project, aiming to imagine solutions to our society's problems.<br/><br/></td></tr>" .
                               "<tr><td><b>III) Process</b></td><tr>" .
                               "<tr><td>Direct Democracy allows citizens to overview all decisions trough Motions. Motions are debated, written and voted by citizens. " .
                               "Constitution is 8th Wonderland's code, filled with all rules and principles. It can be modified only trough citizen's vote.<br/><br/></td></tr></tbody>" .
                               "<tfoot><tr><td style='text-align:center;'><span style='color:red;'>W</span>elcome !</td></tr></tfoot></table>",
    
    'regions.missing_country_argument'  => 'The country ID must be set',
    
    'countries.france'      => 'France' 
);