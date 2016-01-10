$(".message a").animate({"margin-left":"0%"}, 800);

function reloadMessages(data) {
    var list = $("#messages-list section > ul");
    list
        .children('li')
        .children('a')
        .animate({"margin-left":"-100%"}, 200, function() {
            $(this).remove();
        })
    ;
    $.each(data.messages, function(index, message){
        $(".paginated-list").attr('x-data-total-items', data.total_groups);
        var element =
            '<li id="message-' + message.id + '" class="message"><a href="' + website_root + 'message/show?message_id=' + message.id + '"> ' +
            message.title + '</a></li>'
        ;
        $(element).appendTo(list).children("a").animate({"margin-left":"0%"}, 800);
    });
}

function readMessage(messageId) {
    $.ajax({
        type: 'GET',
        url: website_root + 'message/read',
        data: "message_id=" + messageId,
        dataType: 'json',
        success: function(data) {
            markMessageAsRead(messageId);
            deployMessage(data.message);
        }
    });
}

function markMessageAsRead(messageId) {
    var messageItem = $("#message-" + messageId + " a");
    
    if(messageItem.attr('class') === 'message-unread') {
        messageItem.attr('class', '');
        updateUnreadMessagesCounter();
    }
}

function updateUnreadMessagesCounter() {
    var messageCounter = $("#messages span");
    var nbUnreadMessages = parseInt(messageCounter.text()) - 1;
    
    if(nbUnreadMessages < 1) {
        messageCounter.attr('class', 'badge');
    }
    messageCounter.text(nbUnreadMessages);
}

function deployMessage(message) {
    removeMessage();
    
    $("#message-area")
        .prepend(
            '<section id="message-details-' + message.id + '" class="message-details">' +
            '<header><h3>' + message.title + '</h3> ' +
            '<div onclick="removeMessage();return false;"><i class="fa fa-close fa-2x"></i></div></header>' +
            '<p>' + message.content + '</p>' + 
            '</section>'
        )
        .find('.message-details')
        .animate({width: "100%"}, 600)
    ;
}

function removeMessage() {
    $(".message-details").animate({width: "0%"}, 600, function() {
        $(this).remove();
    });
}

function createMessage() {
    $.ajax({
        type: "POST",
        url: website_root + 'message/create',
        data: JSON.stringify($("#message-creation-form form").serializeFormJSON()),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            displayMessageCreationAlert('success', response.message);
            setTimeout(function() {
                $("#message-creation-form").modal('hide');
            }, 4000);
        },
        error: function(response) {
            var errors = JSON.parse(response.responseText);
            var html = '';
            $.each(errors, function(index, error) {
                html += '<li>' + error.message + '</li>'
            });
            displayMessageCreationAlert('danger', html);
        }
    });
}

function displayMessageCreationAlert(type, content) {
    $("#message-creation-form .alert").slideUp('fast', function() {
        $(this).remove();
    })
    $("#message-creation-form .modal-body").prepend(
        '<div class="alert alert-' + type + '">' + content + '</div>'
    ).find('.alert').slideDown('fast');
}

$(".paginated-list").paginate();