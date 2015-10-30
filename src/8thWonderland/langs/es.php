<?php

/*
 * Fichier de langue en Espagnol
 * 
 */
$array = array(
    // Langues disponibles
    "fr"                    => "francès",
    "en"                    => "inglès",
    "es"                    => "español",
    
    
    // Connexion / Perte de mot de passe
    "login"                 => "Usuario",
    "password"              => "Contraseña",
    "connexion"             => "Entrar",
    "process_forgetpwd"     => "Etapa 1 : Coja su login y haga clic sobre 'Enviar'. Un código será enviado al correo electrónico correspondiente.<br/>" .
                               "Etapa 2 : Consulte vuestro e-mail y coja el código indicado en el e-mail.<br/>" .
                               "Etapa 3 : Usted recibirá un segundo e-mail que contendrá su nueva contraseña",
    "mail_forgetpwd"        => "Hola,<br/><br/>Usted recibe este e-mail porque un procedure de pérdida de contraseña ha sido puesto en marcha.<br/>Si usted tiene poner en " .
                               "marcha este procedimiento, usted debe coger el código aquí abajo sobre el sitio.<br/><br/>Si usted no efectuó este procedimiento, " .
                               "Usted no tiene ninguna acción tiene hacer y su contraseña queda no cambiada.<br/><br/><b>Su código es : </b>",
    "mail_newpwd"           => "Hola,<br/><br/>Usted encontrará su nueva contraseña aquí abajo. Si usted encuentra difficultes para conectarse, " .
                               "No vacile tiene ponernos en contacto tiene esta dirección : developpeurs@8thwonderland.com<br/><br/><b>Su nueva contraseña es : </b>",
    "reponse_newpwd"        => "Su contraseña ha sido enviada.",
    "forget_pwd"            => "Contraseña olvidada",
    "code_forgetpwd"        => "Código",
    "btn_codeforgetpwd"     => "Validar",
    "logout"                => "Salir",
    
    
    // Menu citoyen
    "inform"                => "Informarse",
    "share"                 => "Compartir",
    "forum"                 => "Forum",
    "communicate"           => "Comunicar",
    "finance"               => "Financiar",
    "admin"                 => "Administrar",
    
    
    // Menu présentation
    "presentation"          => "Presentación",
    "subscribe"             => "Suscribir",
    "partners"              => "Socios",
    "news"                  => "Noticias",
    "contact-us"            => "Contactarnos",
        
    
    // Menu groupes
    "calendar"              => "Calendario",
    "adressbook"            => "Anuario",
    "bookmark"              => "marcador de libros",
    "quit_desktop"          => "Dejar la oficina",
    
    
    // Titres des composants
    "shop"                  => "Tienda",
    "what_new"              => "Ultima noticia",
    "friend_groups"         => "Grupos simpatisantes",
    "motions_inprogress"    => "Mociones pendientes",
    "motions_archives"      => "Mociones archivadas",
    "stats_country"         => "Estadístico del país",
    "title_groupsmembers"   => "Mis groupos",
    "title_groups"          => "Grupos titulados",
                

    // Partie 'TACHES'
    "tasks_inprogress"      => "Las manchas pendientes",
    "display_archivestasks" => "Ver las manchas archivadas",
    "description_task"      => "Descripción",
    "date_task"             => "Fecha",
    "author_task"           => "Autor",
    "create_task_ok"        => "La mancha ha creada .",
    "create_task_nok"       => "La mancha ha no sido creada !",
    "delete_task_ok"        => "La mancha ha sido suprimida.",
    "delete_task_nok"       => "La mancha no ha podido ser suprimida !",
    "btn_createtask"        => "Crear una mancha",
    "btn_detailstask"       => "Ver",
    "btn_createtask"        => "Crear la mancha",
    

    // Partie 'MAIL
    "contact_title"         => 'Título',
    "contact_mail"          => 'Correo electronico',
    "contact_message"       => 'mensaje',
    "contact_send"          => 'Enviar',
    "mail_ok"               => 'Vuestro mensaje ha sido bien enviado',
    
    
    // Partie 'MESSAGING'
    "messaging"             => "Mensajeria interna",
    "received_messages"     => "Mensajes recibidos",
    "sent_messages"         => "Messages enviados",
    "manager_contacts"      => "Contactos",
    "compose_message"       => "Componer un mensaje",
    "box_receive"           => "Caja de recepción",
    "box_send"              => "Caja de envío",
    "title_message"         => "Título",
    "recipient_message"     => "Destinatarios",
    "content_message"       => "Contenido",
    "sender_message"        => "Autor",
    "date_received"         => "Date",
    "no_message"            => "No mensaje",
    "create_group"          => "Crear un grupo",
    "delete_msg_ok"         => "El mensaje ha sido suprimido.",
    "delete_msg_nok"        => "El mensaje no ha podido ser suprimido !",
    
    
    // Partie 'MOTIONS'
    "create_motion"         => "Depositar una moción",
    "display_archives"      => "Ver los archivos",
    "title_motion"          => "Título",
    "theme_motion"          => "Tema",
    "means_motion"          => "Medios",
    "vote_approved"         => "Estoy de acuerdo",
    "vote_refused"          => "Rechazo",
    "vote_approve"          => "Acuerdo",
    "vote_refuse"           => "Rechazar",
    "status_motion"         => "Validada / rechazada",
    "date_deposit"          => "Fecha deposito",
    "date_end_vote"         => "Fin del voto",
    "author_motion"         => "Autor",
    "organization"          => "Estructura interna",
    "constitutional"        => "Constitucional",
    "justice"               => "Judicial",
    "actions"               => "Acciones externas",
    "description_motion"    => "Descripción",
    "depot_motion_ok"       => "La moción ha sido depositada.",
    "depot_motion_nok"      => "La moción no ha podido ser depositada !",
    "create_motion_alert1"  => "Toda moción tiene que estar sometida a un debate en el forum ante de ser añadida al voto !<br/>" .
                               "Gracias por asegurarse que la moción responde perfectamente a la Constitucion e a las mociones precedementes votadas.<br/>" .
                               "Todas las mociones que no responden a estos criterios seran anulados !<br/>" .
                               "En caso de duda, gracias de discutir con los ciudadanos.",
    "vote_motion_ok"        => "El voto ha sido registrado",
    "vote_motion_nok"       => "El voto no ha podido ser registrado",
    "btn_createmotion"      => "Depositar",
    "btn_votemotion"        => "Votar",
    "result"                => "Resultado",
    "approved"              => "Validada al ",
    "refused"               => "rechazada al ",
    
    // Partie 'GROUPS'
    "manager_group"         => "Gestión del grupo",
    "group_name"            => "Nombre del grupo",
    "group_description"     => "Descripción",
    "group_contact"         => "Contacto de grupo",
    "group_nbmembers"       => "Número de miembros",
    "group_datecreation"    => "Fecha de creación",
    "group_type"            => "Tipo del grupo",
    "btn_enterdesktop"      => "Entrar",
    "group_members"         => "miembros",
    "group_listmembers"     => "Miembros del grupo",
    
    
    // Actions profils
    "update_profile"        => "Modificar vuestro perfil",
    "btn_updateprofile"     => "Modificar",
    "read_mails"            => "Leer vuestros mensajes",
    "discover_groups"       => "Descubrir los grupos",
    
    
    // Mise à jour du profil
    "avatar"                => "Avatar",
    "last_connexion"        => "Ultima conexión",
    "subscription"          => "inscripción",
    "identity"              => "Pseudo",
    "mail"                  => "Correo electrónico",
    "gender"                => "Sexo",
    "lang"                  => "Lengua",
    "male"                  => "Hombre",
    "female"                => "Mujer",
    "country"               => "País",
    "region"                => "Región",
    "btn_subscribe"         => "Inscribirse",
    "btn_updatezone"        => "Actualización",
    
    
    // erreurs & messages divers
    "dev_inprogress"        => "Este módulo esta en el progreso de desarollo.",
    "error"                 => "Un error apareció !",
    "update_site"           => "El sitio esta en mantenimiento.<br/><br/>Las inscripciones estan momentáneamente cerradas.",
    "subscribe_ok"          => "La inscripción se acabó con éxito.",
    "modif_ok"              => "Vuestras modificaciones han sido echas.",
    "translate_notfound"    => "El módulo de lengua no se encuentra !",
    "no_action"             => "La acción pedida no se encuentra !",
    "connexion_nok"         => "Conexión suspendida !",
    "no_result"             => "Ningún resultado encontrado.",
    "no-subscription"       => "La inscripción esta cerrada.",
    "avatar_invalid"        => "El avatar es inválida !",
    "identity_invalid"      => "La identidad es inválida !",
    "identity_exist"        => "La identidad ya existe !",
    "mail_invalid"          => "El correo electrónico esta inválida !",
    "mail_exist"            => "El correo electrónico ya existe !",
    "fields_empty"          => "Todos los campos son obligatorios !",
    "file_notfound"         => "El fichero es imposible de encontrar !",
    
    
    // textes et descriptions
    "open_register"         => "Las inscripciones son reabiertas. Haga clic sobre 'Suscribir' y cumpla el formulario.",
    "open_chat"             => "Venga para discutir con nosotros. <a style='font-weight:bold; font-size: 15px; color:#e14f1c;' href='/Intranet/openChatroom' target='_blank'>Haga clic aquí</a> para abrir al chat ...",
    "txt_profileaccess"     => "Atención ! Estas informaciones sirven durante la conexión en este sitio.",
    "page_previous"         => "Precedente",
    "page_next"             => "Siguiente",
    "item_of"               => " de ",
    "unknown"               => "Desconocido",
    "back"                  => "Vuelta",
    "btn_search"            => "Buscar",
    "btn_add"               => "Añadir",
    "btn_modify"            => "Modificar",
    "btn_delete"            => "Supprimir",
    "periodicity_days"      => "Periodicidad - días",
    "periodicity_hours"     => "Periodicidad - Horas",
    "all_days"              => "Todos días",
    "all_hours"             => "Todas las horas",
    "monday"                => "Lunes",
    "tuesday"               => "Martes",
    "wednesday"             => "Miércoles",
    "thursday"              => "Jueves",
    "friday"                => "Viernes",
    "saturday"              => "Sábado",
    "sunday"                => "Domingo",
    
    
    // console d'administration
    "stats_members"         => "Inscritos : ",
    "stats_members_actives" => "Activos : ",
    "stats_region_unknown"  => "Regiones desconocidas : ",
    "admin_logs"            => "Boletines de logs",
    "admin_users"           => "Usuarios",
    "admin_groups"          => "Groupos",
    "admin_server"          => "Servidor",
    "logs_level"            => "Nivel",
    "logs_description"      => "Descripción",
    "logs_date"             => "Fecha",
    "logs_delete"           => "Suprimir los logs",
    "cron_id"               => "Id",
    "cron_file"             => "Fichero",
    "cron_description"      => "Descripción",
    "btn_addcron"           => "Crear la mancha",
    "cron_active"           => "Activa",
    "quit_console"          => "Salir de la consola",
    
    
    'decouvrir_8th'         => "<table><caption>Presentación del proyecto</caption>" .
                               "<tbody><tr><td><b>I) Nuestras origenes.</b></td><tr>" .
                               "<tr><td>El proyecto 8th Wonderland esta en construcción. Se inspira de la película y del concepto de la democracia directa en internet.<br/>" .
                               "Lejos de ser un sitio de fan, los objectivos son bien reales : favorecer la creación e el apoyo de iniciativas ciudadanas.<br/><br/></td></tr>" .
                               "<tr><td><b>II) Les objectifs</b></td><tr>" .
                               "<tr><td>8th Wonderland es un reagrupamiento de personas alrededor del mundo. Ella desea aportar o participar a un proyecto destinándose a construir soluciones frente a los problemas que encuentra la sociedad.<br/><br/></td></tr>" .
                               "<tr><td><b>III) El funcionamiento.</b></td><tr>" .
                               "<tr><td>La démocracia directa permite a los ciudadanos de controlar juntos las deciciones a través de mociones. Las mociones son redactadas, debatidas y votadas por los ciudadanos." .
                               "La Constitución es el corazón de 8th Wonderland, conteniendo sus leyes y sus principios." .
                               "Ella es modificable sólo por el voto del conjunto de los ciudadanos.<br/><br/></td></tr></tbody>" .
                               "<tfoot><tr><td style='text-align:center;'><span style='color:red;'>B</span>ienvenue !</td></tr></tfoot></table>",
    
    "select_country"        => "Conforme a la moción del 16/01/2012, debe en adelante indicar vuestro país y vuestra región de vivienda para ser integrado " .
                               "al grupo regional correspondiente.",
)
?>
