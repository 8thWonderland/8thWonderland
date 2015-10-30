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
    if(username !== 'guest') {
        user.username = username;
    }
    $("input[name=final-username]").val(username);
    displayUsernameForm();
};

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
    messages.append("<div class='message'><span class='author'>" + message.author + " : </span>" + message.content + '</div>');
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
        conn.onclose = function(evt) {
            appendLog($("<div><b>Connection closed.</b></div>"))
        };
        conn.onmessage = function(evt) {
            addMessage(JSON.parse(evt.data));
        };
    } else {
        appendLog($("<div><b>Your browser does not support WebSockets.</b></div>"));
    }
}

function connectionError(status, message) {
    if(status === 401) {
        user.token = null;
        displayUsernameForm();
    }
}