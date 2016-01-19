<?php

/*
 * Fichier de langue en Français
 * 
 */
$array = array(
    // Langues disponibles
    'fr' => 'Français',
    'en' => 'Anglais',
    'es' => 'Espagnol',
    
    'date_format' => 'd/m/Y H:i:s',

    // Connexion / Perte de mot de passe
    'login' => 'Identifiant',
    'password' => 'Mot de passe',
    'confirmation_password' => 'Confirmation du mot de passe',
    'connection' => 'Entrer',
    'process_forgetpwd' => "Etape 1 : Saisissez votre login puis cliquez sur le bouton 'Envoyer'. Un code sera envoyé à l'adresse électronique correspondante.<br/>".
                               'Etape 2 : Consultez votre boite aux lettres et saisissez le code indiqué dans le mail.<br/>'.
                               'Etape 3 : Vous recevrez un second mail comportant votre nouveau mot de passe.',
    'mail_forgetpwd' => 'Bonjour,<br/><br/>Vous recevez ce mail car une procedure de perte de mot de passe a ete declenchee.<br/>Si vous avez vous-meme '.
                               "declencher cette procedure, vous devez saisir le code ci dessous sur la page du site.<br/><br/>Si vous n'avez pas effectue cette procedure, ".
                               "vous n'avez aucune action a faire et votre mot de passe reste inchange.<br/><br/><b>Votre code est : </b>",
    'mail_newpwd' => 'Bonjour,<br/><br/>Vous trouverez votre nouveau mot de passe ci dessous. Si vous rencontrez des difficultes pour vous connecter, '.
                               "n'hesitez pas a nous contacter a cette adresse : developpeurs@8thwonderland.com<br/><br/><b>Votre nouveau mot de passe est : </b>",
    'reponse_newpwd' => 'Votre mot de passe vous a été envoyé.',
    'forget_pwd' => 'Mot de passe oublié',
    'code_forgetpwd' => 'Code',
    'btn_codeforgetpwd' => 'Valider',
    'logout' => 'Se déconnecter',

    'registration.password_mismatch' => 'La confirmation du mot de passe est incorrecte',
    'registration.invalid_login' => 'L\'identifiant indiqué est invalide',
    'registration.existing_login' => 'L\'identifiant indiqué n\'est pas disponible',
    'registration.invalid_mail' => 'L\'adresse électronique indiquée est invalide',
    'registration.existing_mail' => 'L\'adresse électronique indiquée est déjà utilisée',
    'registration.invalid_country' => 'Le pays indiqué n\'existe pas',
    'registration.invalid_region' => 'La région indiquée n\'existe pas',

    'chatrooms.link' => 'Chat',

    'welcome' => 'Bienvenue',
    'login_form_title' => 'Se Connecter',
    'registration_form_title' => 'Rejoignez-nous',

    // Menu citoyen
    'inform' => "S'informer",
    'share' => 'Partager',
    'forum' => 'Forum',
    'communicate' => 'Communiquer',
    'finance' => 'Financer',
    'admin' => 'Administrer',

    // Menu présentation
    'presentation' => 'Présentation',
    'subscribe' => 'Rejoindre la communauté',
    'partners' => 'Nos Soutiens',
    'news' => 'Evènements',
    'contact-us' => 'Contact',

    // partners
    'citadel_knights' => 'Les Chevaliers de la Citadelle',

    // Menu groupes
    'calendar' => 'Calendrier',
    'adressbook' => 'Annuaire',
    'bookmark' => 'Favoris',
    'quit_desktop' => 'Quitter le bureau',

    // Titres des composants
    'shop' => 'Magasin',
    'what_new' => 'Dernières infos',
    'friend_groups' => 'Groupes sympathisants',
    'motions_inprogress' => 'Motions en cours',
    'motions_archives' => 'Motions archivées',
    'stats_country' => 'Statistiques du pays',
    'my_groups' => 'Mes groupes',

    // Groupes
    'groups' => 'Groupes',
    'groups.thematic' => 'Groupes Thématiques',
    'groups.regional' => 'Groupes Régionaux',
    'groups.back_to_list' => 'Revenir à la liste des Groupes',

    // Motion Themes
    'motion_themes.constitutional' => 'Motion constitutionnelle',
    'motion_themes.justice' => 'Motion Judiciaire',
    'motion_themes.organization' => 'Motion organisationnelle',
    'motion_themes.actions' => 'Motion d\'action',
    
    // Motions
    'motions.vote' => 'Voter',
    'motions.new_title' => 'Déposition de motion',
    'motions.creation_date' => 'Date de début',
    'motions.end_date' => 'Date de fin',
    'motions.select_type_for_duration' => 'Selon le thème de la motion',
    'motions.theme' => 'Thème de la motion',
    'motions.title' => 'Titre de la motion',
    'motions.description' => 'Description de la motion',
    'motions.means' => 'Moyens nécessaires à l\'application de la motion',
    'motions.validate' => 'Créer la motion',
    'motions.already_voted' => 'Vous avez déjà voté cette motion',
    'motions.vote.request' => 'Approuvez-vous cette motion ?',
    'motions.vote.yes' => 'Oui',
    'motions.vote.no' => 'Non',
    'motions.archives' => 'Motions archivées',
    'motions.approved' => 'Motion approuvée',
    'motions.rejected' => 'Motion rejetée',
    
    'messages.inbox' => 'Boîte de réception',
    'messages.box_list_title' => 'Messagerie',
    'messages.creation.title' => 'Rédiger un message',
    'messages.title' => 'Objet',
    'messages.recipient' => 'Destinataire',
    'messages.content' => 'Message',
    'messages.creation.close' => 'Annuler',
    'messages.creation.send' => 'Envoyer',
    'messages.creation.success' => 'Votre message a été correctement envoyé !',
    'messages.creation.recipient_not_found' => 'Le destinataire indiqué n\'existe pas',
    'messages.creation.empty_content' => 'Le message ne peut être vide',
    'messages.creation.empty_title' => 'L\'objet ne peut être vide',
    
    'developers.local_installation' => 'Installer le site en local',
    'developers.environment_installation_title' => 'Installation de l\'environnement',
    'developers.environment_installation' => 
        '<p>
            Pour faire fonctionner correctement l\'application et pouvoir travailler avec en toute quiétude,
            il vous faudra certains outils sur votre poste de développement.
        </p>
        <p>
            Retrouvez ici la liste des différents outils nécessaires, accompagnés d\'un lien vers leurs sites respectifs, où vous pourrez les télécharger et consulter leurs documentations :
        </p>
        <ul>
            <li>
                Outil de gestion de dépendances : Composer
                <a href="https://getcomposer.org">(Site officiel)</a>
            </li>
            <li>
                Outil de versionning : Git
                <a href="https://git-for-windows.github.io/">(Site du terminal Git Bash pour Windows)</a>
            </li>
            <li>
                Server Web (Exemple : Apache ou Nginx)
                <a href="http://www.easyphp.org/">(EasyPHP: un utilitaire prêt à l\'emploi avec serveur web et base de données)</a>
            </li>
        </ul>',
    'developers.project_installation_title' => 'Installation du projet',
    'developers.project_installation_1' =>
        '<p>
            Afin d\'installer le projet, il est tout d\'abord nécessaire de le forker sur Github
            (afin de pouvoir ensuite pousser vos modifications et ensuite effectuer une Merge Request
            à destination du dépôt officiel de 8thWonderland).
        </p>
        <p>
            Pour ce faire, rendez-vous sur le <a href="https://github.com/8thWonderland/8thWonderland">dépôt officiel de 8th Wonderland</a>
            après vous être connectés avec votre compte Github et appuyez sur le bouton <code>Fork</code>.
        </p>
        <p>
            Vous disposez à présent de votre copie du dépôt, qui vous permettra d\'organiser vos modifications avant de demander à les intégrer dans le code officiel du site.
        </p>
        <p>
            Placez-vous donc dans le dossier où vous mettez vos différents projets et tapez la commande suivante, en remplaçant {{login}} par votre nom d\'utilisateur Github :
        </p>',
    'developers.project_installation_2' => 
        '<p>
            Un dossier 8thWonderland a maintenant été créé dans votre répertoire. En vous y plaçant dans votre terminal, vous vous situerez sur la branche master du projet.

            Il faut maintenant configurer l\'application pour qu\'elle fonctionne sur votre poste.

            Pour ce faire, il faut copier le fichier de configuration modèle et le modifier :
        </p>',
    'developers.project_installation_3' =>
        '<p>
            Vous pouvez à présent modifier les valeurs du fichier <code>config.json</code> pour y mettre les bonnes valeurs (par exemple pour votre base de données).
        </p>
        <p>
            Ensuite, nous allons utiliser Composer pour installer les dépendances du projet et générer les fichiers d\'autoload.
        </p>
        <div class="code-snippet">
            <span># Utilisez php composer.phar au lieu de composer si vous avez installé l\'archive plutôt que la commande</span>
            <p>composer install</p>
        </div>',
    'developers.server_installation_title' => 'Installation du serveur web',
    'developers.server_installation_easyphp_title' => 'Avec EasyPHP',
    'developers.server_installation_easyphp' =>
        '<p>
            Avec EasyPHP, nous allons créer un alias qui pointera vers votre répertoire.
            Il faut vous rendre sur la page d\'administration d\'EasyPHP, et dans la section "Alias" créer un nouvel alias.
            Il vous sera demandé d\'indiquer le nom de l\'alias ainsi que le chemin.
            Comme chemin vous pouvez renseigner le chemin menant à la racine du projet 8thWonderland (qui contient les dossiers dump et src).
        </p>',
    'developers.database_installation_title' => 'Installation de la base de données',
    'developers.database_installation' => 
        '<p>
            Il faut maintenant créer la base de données de l\'application et construire sa structure avec des données initiales.
        </p>
        <p>
            Vous pouvez utiliser PHPMyAdmin (module nativement installé sur EasyPHP) pour administrer vos bases de données, ou bien vous connecter directement avec MySQL
        </p>
        <p>
            En premier lieu, créez une base de données nommée <code>thwonderbdd</code>
        </p>
        <p>
            Une fois ceci fait, nous allons charger la structure de la base de données avec des données initiales.
        </p>
        <p>
            Vous devez charger dans votre base de données les contenus des différents fichiers SQL du projet.
        </p>
        <ul>
            <li>
                <code>./dump/thwonderbdd.sql</code> contient l\'ensemble de la structure et des tables de la base.
            </li>
            <li>
                <code>./dump/init_data.sql</code> contient des données initiales nécessaires au fonctionnement du projet.
            </li>
        </ul>',
    
    // Partie 'TACHES'
    'tasks_inprogress' => 'Les tâches en cours',
    'display_archivestasks' => 'Voir les tâches résolues',
    'description_task' => 'Description',
    'date_task' => 'Date',
    'author_task' => 'Auteur',
    'create_task_ok' => 'La tâche a été crée.',
    'create_task_nok' => "La tâche n'a pas pu être créée !",
    'delete_task_ok' => 'La tâche a été supprimée.',
    'delete_task_nok' => "La tâche n'a pas pu être supprimée !",
    'btn_createtask' => 'Créer une tâche',
    'btn_detailstask' => 'Voir',
    'btn_createtask' => 'Créer la tâche',

    // Partie 'MAIL'
    'contact_title' => 'Titre',
    'contact_mail' => 'Adresse électronique',
    'contact_message' => 'message',
    'contact_send' => 'Envoyer',
    'mail_ok' => 'Votre message a bien été envoyé.',

    // Partie 'MESSAGING'
    'messaging' => 'Messagerie interne',
    'received_messages' => 'Messages reçus',
    'sent_messages' => 'Messages envoyés',
    'manager_contacts' => 'Contacts',
    'compose_message' => 'Composer un message',
    'box_receive' => 'Boîte de réception',
    'box_send' => "Boîte d'envoi",
    'title_message' => 'Titre',
    'recipient_message' => 'Destinataires',
    'content_message' => 'Contenu',
    'sender_message' => 'Emetteur',
    'date_received' => 'Date',
    'no_message' => 'Aucun message',
    'create_group' => 'Créer un groupe',
    'delete_msg_ok' => 'Le message a été supprimé.',
    'delete_msg_nok' => "Le message n'a pas pu être supprimé !",

    // Partie 'MOTIONS'
    'create_motion' => 'Déposer une motion',
    'display_archives' => 'Voir les archives',
    'title_motion' => 'Titre',
    'theme_motion' => 'Thème',
    'means_motion' => 'Moyens',
    'vote_approved' => "J'approuve",
    'vote_refused' => 'Je refuse',
    'vote_approve' => 'Pour',
    'vote_refuse' => 'Contre',
    'status_motion' => 'Validée / refusée',
    'date_deposit' => 'Date dépôt',
    'date_end_vote' => 'Fin du vote',
    'author_motion' => 'Auteur',
    'organization' => 'Structure interne',
    'constitutional' => 'Constitutionnelle',
    'justice' => 'Judiciaire',
    'actions' => 'Actions externes',
    'description_motion' => 'Description',
    'depot_motion_ok' => 'La motion a été déposée.',
    'depot_motion_nok' => "La motion n'a pas pu être déposée.",
    'create_motion_alert1' => "Toute motion doit être soumise à un débat dans le forum avant d'être ajoutée au vote !<br/>".
                               'Merci de vous assurer que la motion répond parfaitement à la Constitution et aux motions précédemment votées.<br/>'.
                               'Toutes les motions ne répondant pas à ces critères se verront annulées !<br/>'.
                               "En cas de doute, merci d'en discuter avec les citoyens.",
    'vote_motion_ok' => 'Le vote a été enregistré',
    'vote_motion_nok' => "Le vote n'a pas pu être enregistré",
    'btn_createmotion' => 'Déposer',
    'btn_votemotion' => 'Voter',
    'result' => 'Résultat',
    'approved' => 'Validée à ',
    'refused' => 'Rejeté à ',

    // Partie 'GROUPS'
    'manager_group' => 'Gestion du groupe',
    'group_name' => 'Nom du groupe',
    'group_description' => 'Description',
    'group_contact' => 'Contact de groupe',
    'group_nbmembers' => 'Nbre de membres',
    'group_datecreation' => 'Date Creation',
    'group_type' => 'Type du groupe',
    'btn_enterdesktop' => 'Entrer',
    'group_members' => 'membres',
    'group_listmembers' => 'Membres du groupe',

    // Actions profils
    'update_profile' => 'Modifier votre profil',
    'btn_updateprofile' => 'Modifier',
    'read_mails' => 'Lire vos messages',
    'discover_groups' => 'Découvrir les groupes',

    // Mise à jour du profil
    'avatar' => 'Avatar',
    'last_connexion' => 'Dernière connexion',
    'subscription' => 'Inscription',
    'identity' => 'Pseudo',
    'mail' => 'Email',
    'gender' => 'Sexe',
    'lang' => 'Langue',
    'male' => 'Homme',
    'female' => 'Femme',
    'country' => 'Pays',
    'region' => 'Région',
    'btn_subscribe' => "S'inscrire",
    'btn_updatezone' => 'Mettre à jour',

    // erreurs & messages divers
    'dev_inprogress' => 'Ce module est en cours de développement.',
    'error' => 'Une erreur est survenue !',
    'update_site' => 'Le site est actuellement en maintenance.<br/><br/>Les inscriptions sont momentanément fermées.',
    'subscribe_ok' => "L'inscription s'est terminée avec succès",
    'modif_ok' => 'Vos modifications ont été faites.',
    'translate_notfound' => 'Le module de langue est introuvable !',
    'no_action' => "L'action demandée est introuvable !",
    'connection_failed' => 'Connexion échouée !',
    'no_result' => 'Aucun résultat trouvé.',
    'no-subscription' => "L'inscription est actuellement fermée.",
    'avatar_invalid' => "L'avatar est invalide !",
    'identity_invalid' => "L'identité est invalide !",
    'identity_exist' => 'Cette identité est déjà utilisée !',
    'mail_invalid' => "L'adresse mail est invalide !",
    'mail_exist' => "L'adresse mail existe déjà !",
    'fields_empty' => 'Tous les champs sont obligatoires !',
    'file_notfound' => 'Le fichier est introuvable !',

    // textes et descriptions
    'open_register' => "Les inscriptions sont à nouveau ouvertes !! Cliquez sur 'Rejoindre la Communauté' et remplissez le formulaire.",
    'open_chat' => "Venez discuter avec nous, <a style='font-weight:bold; font-size: 15px; color:#e14f1c;' href='/Intranet/openChatroom' target='_blank'>ici</a>.",
    'txt_profileaccess' => 'Attention ! Ces informations servent lors de la connexion sur ce site.',
    'page_previous' => 'Précédente',
    'page_next' => 'Suivante',
    'item_of' => ' de ',
    'unknown' => 'Inconnu',
    'back' => 'Retour',
    'btn_search' => 'Rechercher',
    'btn_add' => 'Ajouter',
    'btn_modify' => 'Modifier',
    'btn_delete' => 'Supprimer',
    'periodicity_days' => 'Périodicité - Jours',
    'periodicity_hours' => 'Périodicité - Heures',
    'all_days' => 'Tous les jours',
    'all_hours' => 'Toutes les heures',
    'monday' => 'Lundi',
    'tuesday' => 'Mardi',
    'wednesday' => 'Mercredi',
    'thursday' => 'Jeudi',
    'friday' => 'Vendredi',
    'saturday' => 'Samedi',
    'sunday' => 'Dimanche',

    // console d'administration
    'stats_members' => 'Inscrits : ',
    'stats_members_actives' => 'Actifs : ',
    'stats_region_unknown' => 'Régions inconnues : ',
    'admin_logs' => 'journaux de logs',
    'admin_users' => 'Utilisateurs',
    'admin_groups' => 'Groupes',
    'admin_server' => 'Serveur',
    'logs_level' => 'Niveau',
    'logs_description' => 'Description',
    'logs_date' => 'Date',
    'logs_delete' => 'Supprimer les logs',
    'cron_id' => 'Id',
    'cron_file' => 'Fichier',
    'cron_description' => 'Description',
    'btn_addcron' => 'Ajouter une tache',
    'cron_active' => 'Active',
    'quit_console' => 'Quitter la console',

    'decouvrir_8th' => '<table><caption>Présentation du projet</caption>'.
                               '<tbody><tr><td><b>I) Nos origines.</b></td><tr>'.
                               "<tr><td>Le projet 8th Wonderland est en construction. Il s'inspire du film et du concept de la démocratie directe sur internet.<br/>".
                               "Loin d'être un fan site, les objectifs sont bien réels : favoriser la création et le soutien d'initiatives citoyennes.<br/><br/></td></tr>".
                               '<tr><td><b>II) Les objectifs</b></td><tr>'.
                               '<tr><td>8th Wonderland est un regroupement de personnes de par le monde. Celles-ci souhaitent apporter ou participer à un projet visant à construire des solutions face aux problèmes que rencontre la société.<br/><br/></td></tr>'.
                               '<tr><td><b>III) Le fonctionnement.</b></td><tr>'.
                               '<tr><td>La démocratie directe permet aux citoyens de contrôler ensemble les décisions à travers des motions. Les motions sont rédigées, débattues et votées par les citoyens.'.
                               'La Constitution est le coeur de 8th Wonderland, contenant ses lois et ses principes.'.
                               "Elle est modifiable uniquement par le vote de l'ensemble des citoyens.<br/><br/></td></tr></tbody>".
                               "<tfoot><tr><td style='text-align:center;'><span style='color:red;'>B</span>ienvenue !</td></tr></tfoot></table>",

    'regions.missing_country_argument' => 'Le pays doit être indiqué',

    'countries.france' => 'France',
);
