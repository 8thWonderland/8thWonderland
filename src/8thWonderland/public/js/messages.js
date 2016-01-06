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

$(".paginated-list").paginate();