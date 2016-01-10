var filtered_group_type = '';

$(".group a").animate({"margin-left":"0%"}, 800);

$.fn.filterGroups = function(group_type_id) {
    var filter = '';
    // Disable the filter
    if(filtered_group_type === group_type_id) {
        filtered_group_type = null;
        $(this).attr('class', '');
    // Enable the filter
    } else {
        filtered_group_type = group_type_id;
        filter = "?type_id=" + group_type_id;
        $("#filters a.active").attr('class', '');
        $(this).attr('class', 'active');
    }
    $.ajax({
        type: "GET",
        url: website_root + "/group/list" + filter,
        dataType: "json",
        success: function(data) {
            reloadGroups(data);
            $(".paginated-list")
                .attr('x-data-total-items', data.total_groups)
                .attr('x-data-route', website_root + "/group/list" + filter)
                .paginate()
            ;
        }
    });
};

function reloadGroups(data) {
    var list = $("#groups-list section > ul");
    list
        .children('li')
        .children('a')
        .animate({"margin-left":"-100%"}, 200, function() {
            $(this).remove();
        })
    ;
    $.each(data.groups, function(index, group){
        $(".paginated-list").attr('x-data-total-items', data.total_groups);
        var icon = (group.type_id === "1") ? "map-o" : "balance-scale";
        var element =
            '<li id="group-' + group.id + '" class="group"><a href="' + website_root + 'group/show?group_id=' + group.id + '"> ' +
            '<span><i class="fa fa-' + icon + '"></i></span>' + group.name + '</li>'
        ;
        $(element).appendTo(list).children("a").animate({"margin-left":"0%"}, 800);
    });
}

function joinGroup(groupId) {
    $.ajax({
        type: "POST",
        url: website_root + "group/join",
        data: JSON.stringify({group_id: groupId}),
        contentType: 'application/json',
        dataType: "json",
        success: function(data) {
            $("#join-button").fadeTo('normal', 0, function() {
                $(this).remove();
            });
            $("#nb_members span").html(data.nb_members);
        }
    });
}
if(jQuery().paginate) {
    $(".paginated-list").paginate();
}