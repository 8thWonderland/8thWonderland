var user = {
    token: '',
    username: ''
};
var server = {
    host: $("input[name=server_host]").val(),
    port: $("input[name=server_port]").val()
};

window.onload = function(e) {
    e.preventDefault();
    user.username = sessionStorage.getItem('chatroom-username');
    if (user.username !== null) {
        user.token = sessionStorage.getItem('chatroom-token');
        startChatroom();
        return;
    }
    var username = $("input[name=username]").val();
    $("input[name=final-username]").val(username);
    if(username !== 'guest') {
        authenticate();
        return;
    }
    displayUsernameForm();
};

window.onbeforeunload = function(e) {
    sessionStorage.removeItem('chatroom-name');
    sessionStorage.removeItem('chatroom-username');
    sessionStorage.removeItem('chatroom-token');
}

function authenticate() {
    user.username = $("input[name=final-username]").val();
    if(user.username === "") {
        connectionError(400, "You must provide an username");
        return false;
    }
    
    $.ajax({
        type: "POST",
        url: "http://" + server.host + ":" + server.port,
        dataType: 'json',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        data: "username=" + user.username,
        success: function(data) {
            addNotification("success", data.message);
            $("#profile-username").text(user.username);
            user.token = data.token;
            sessionStorage.setItem('chatroom-token', user.token);
            sessionStorage.setItem('chatroom-username', user.username);
            startChatroom(data);
        },
        error: function(xhr) {
            connectionError(xhr.status, JSON.parse(xhr.responseText));
        }
    });
}

function displayUsernameForm() {
    $("#username-form-container").css('z-index', 10).fadeTo('fast', 1);
}

function removeUsernameForm() {
    $("#username-form-container").fadeTo('fast', 0, function() {
        $(this).remove();
    });
}

function startChatroom() {
    $.ajax({
        type: "GET",
        url: "http://" + server.host + ":" + server.port,
        dataType: 'json',
        headers:  {
            "Authorization": "Token " + user.token
        },
        success: function(data) {
            addMessages(data.chatroom.messages);
            addUsers(data.chatroom.users);
            sessionStorage.setItem('chatroom-name', data.chatroom.name);
            removeUsernameForm();
            connectWebsocket();
            $("#message-writer").keypress(function (e) {
                if(e.which === 13) {
                    $("#form").submit();
                    e.preventDefault();
                }
            });
        },
        error: function(xhr) {
            connectionError(xhr.status, JSON.parse(xhr.responseText));
        }
    });
}

function addMessages(messages) {
    var nbMessages = messages.length;
    
    for(var i = 0; i < nbMessages; ++i) {
        addMessage(messages[i]);
    }
     $("#messages").animate({ scrollBottom: $("#messages").height() }, 1000);
}

function addMessage(message) {
    var messages = $("#messages");
    var d = messages[0];
    var doScroll = d.scrollTop === d.scrollHeight - d.clientHeight;
    var date = new Date(message.created_at);
    var date_string = 
        date.getFullYear() + '-' +
        ('0' + date.getMonth() + 1).slice(-2) + '-' +
        ('0' + date.getDate()).slice(-2) + ' ' +
        ('0' + date.getHours()).slice(-2) + ':' +
        ('0' + date.getMinutes()).slice(-2) + ':' +
        ('0' + date.getSeconds()).slice(-2)
    ;
    messages.append("<div class='message'><span class='author'>[" + date_string + "] " + message.author + " : </span>" + message.content + '</div>');
    if (doScroll) {
        d.scrollTop = d.scrollHeight - d.clientHeight;
    }
}

function connectWebsocket() {
    var conn;
    var msg = $("#message-writer");
    var log = $("#messages");
    function appendLog(msg) {
        var d = log[0];
        var doScroll = d.scrollTop === d.scrollHeight - d.clientHeight;
        msg.appendTo(log);
        if (doScroll) {
            d.scrollTop = d.scrollHeight - d.clientHeight;
        }
    }
    $("#form").submit(function() {
        if (!conn) {
            return false;
        }
        if (!msg.val()) {
            return false;
        }
        conn.send(JSON.stringify({
            chatroom: "main",
            author: user.username,
            token: user.token,
            content: msg.val()
        }));
        msg.val("");
        return false;
    });
    if (window["WebSocket"]) {
        conn = new WebSocket("ws://" + server.host + ":" + server.port + "/main");
        conn.onopen = function() {
            conn.send(JSON.stringify({
                type: "authentication",
                chatroom: "main",
                author: user.username,
                token: user.token
            }));
        };
        conn.onclose = function(evt) {
            removeUsers();
            addNotification("error", "The connection was closed");
            appendLog($("<div><b>Connection closed.</b></div>"))
        };
        conn.onmessage = function(evt) {
            var data = JSON.parse(evt.data);
            if(data.type === "message") {
                addMessage(data);
            }
            else if(data.type === "notification") {
                handleNotification(data);
            }
        };
    } else {
        appendLog($("<div><b>Your browser does not support WebSockets.</b></div>"));
    }
}

function connectionError(status, data) {
    if(status === 401) {
        user.token = null;
        addNotification("error", data.message);
        displayUsernameForm();
    }
}

function addNotification(type, message) {
    var backgroundColor, textColor;
    switch(type) {
        case "error":
            backgroundColor = "#F09A90";
            textColor = "#600A00";
            break;
        case "success":
            backgroundColor = "#9AF090";
            textColor = "#0A6000";
            break;
        case "info":
            backgroundColor = "#9A90F0";
            textColor = "#0A0060";
            break;
    }
    $("#notifications ul").append(
        "<li style='background-color:" + backgroundColor + ";color:" + textColor + "'>" + message + "</li>"
    ).children().last().delay(5000).fadeTo("slow", 0, function() {
        $(this).remove();
    });
}

function handleNotification(data) {
    var type;
    switch(data.extra_data.notification_type) {
        case "connection":
            type = "info";
            addUser(data.author);
            break;
        case "disconnection":
            type = "info";
            removeUser(data.author);
            break;
    }
    addNotification(type, data.content);
}

function addUsers(users) {
    $.each(users, function(i, user) {
        addUser(user.username);
    });
}

function addUser(username) {
    if(document.getElementById("user-" + user.username) === null) {
        $("#users-list ul").append("<li id='user-" + username + "'>" + username + "</li>");
    }
}

function removeUser(username) {
    $("#user-" + username).slideUp("normal", function(){
        $(this).remove();
    });
}

function removeUsers() {
    $("#users-list li").slideUp("normal", function() {
        $(this).remove();
    });
}