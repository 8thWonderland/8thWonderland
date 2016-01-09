$("#motion-theme").on('change', function() {
    var duration = $("option:selected", this).attr('x-data-duration');
    var motionEndDateBox = $("#motion-end-date span");
    
    if(duration === undefined) {
        motionEndDateBox.html(motionEndDateBox.attr('x-data-default-message'));
        return false;
    }
    var date = new Date();
    date.setDate(date.getDate() + parseInt(duration));
    motionEndDateBox.html(
        date.getFullYear() + '-' +
        ('0' + date.getMonth() + 1).slice(-2) + '-' +
        ('0' + date.getDate()).slice(-2) + ' ' +
        ('0' + date.getHours()).slice(-2) + ':' +
        ('0' + date.getMinutes()).slice(-2) + ':' +
        ('0' + date.getSeconds()).slice(-2)
    );
});

$("#motion-details form").on('submit', function(e) {
    $.ajax({
        type: "POST",
        url: website_root + "motion/create",
        data: JSON.stringify($(this).serializeFormJSON()),
        contentType: 'application/json',
        dataType: 'json',
        success: function(motion) {
            window.location.href = website_root + "motion/show?motion_id=" + motion.id;
        },
        error: function(error) {
            console.log(error);
        }
    });
    return false;
});

function vote_motion(motion_id, vote) {
    var data = {
        "motion_id" : motion_id,
        "vote" : vote
    };
    
    $.ajax({
        type: "POST",
        url: website_root + "motion/vote",
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: function(response) {
            $("#vote-controls").slideUp('normal', function(){
                $(this).remove();
                var title = $("#vote-module h3");
                title.html(title.attr('x-data-vote-success-message'));
            });
        },
        error: function(error) {
            console.log(error);
        }
    });
}

$(".motion a").animate({"margin-left":"0%"}, 800);

function reloadMotions(data) {
    var list = $("#motions-archives section > ul");
    list
        .children('li')
        .children('a')
        .animate({"margin-left":"-100%"}, 200, function() {
            $(this).remove();
        })
    ;
    $.each(data.motions, function(index, motion){
        $(".paginated-list").attr('x-data-total-items', data.total_groups);
        var element =
            '<li id="' + motion.id + '" class="motion">' +
            '<a href="' + website_root + 'motion/show?motion_id=' + motion.id + '>' +
            '<span class="motion-title">' + motion.title + '</span>' +
            '<span class="motion-score">' +
            '<div class="progress"><div class="progress-bar progress-bar-' + ((motion.is_approved) ? 'success' : 'danger') + '" ' +
            'role="progressbar" aria-valuenow="' + motion.score + '" aria-valuemin="0" ' +
            'aria-valuemax="100" style="width: ' + motion.score + '">' +
            motion.score + '%</div></div></span>' +
            '<span class="motion-end-date">' + motion.ended_at + '</span>' +
            '</a></li>'
        ;
        $(element).appendTo(list).children("a").animate({"margin-left":"0%"}, 800);
    });
}

$(".paginated-list").paginate();