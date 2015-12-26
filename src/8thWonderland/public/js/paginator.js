$.fn.paginate = function() {
    var paginatedList = $(this);
    var paginator = paginatedList.children('.paginator');
    paginator.html('');
    
    var page = parseInt(paginatedList.attr('x-data-page'));
    var range = paginatedList.attr('x-data-range');
    var totalElements = paginatedList.attr('x-data-total-items');
    
    var first = 1;
    var previous = page - 1;
    var next = page + 1;
    var last = Math.ceil(totalElements / range);
    
    paginator.append(generatePaginatorContent(first, previous, page, next, last));
};

function generatePaginatorContent(first, previous, page, next, last) {
    var paginatorContent = '<ul>';
    var displayCurrent = true;
    
    if(first === last) {
        paginatorContent += '<li><a disabled>' + first + '</a></li>';
        return paginatorContent + '</ul>';
    }
    if(first === page) {
        displayCurrent = false;
        paginatorContent += '<li><a disabled class="active" alt="current">' + page + '</a></li>';
    } else {
        paginatorContent += '<li><a onclick="$(this).getPage(' + first + '); return false;" alt="first">' + first + '</a></li>';
    }
    if(first < previous) {
        paginatorContent += '<li><a onclick="$(this).getPage(' + previous + '); return false;" alt="previous">' + previous + '</a></li>';
    }
    if(displayCurrent === true) {
        paginatorContent += '<li><a disabled class="active" alt="current">' + page + '</a></li>';
    }
    if(page < last) {
        paginatorContent += '<li><a onclick="$(this).getPage(' + next + '); return false;" alt="next">' + next + '</a></li>';
    }
    if(next < last) {
        paginatorContent += '<li><a onclick="$(this).getPage(' + last + '); return false;" alt="last">' + last + '</a></li>';
    }
    return paginatorContent + '</ul>';
}

$.fn.getPage = function(page) {
    var paginatedList = $(this).closest('.paginated-list');
    
    var range = paginatedList.attr('x-data-range');
    var rangeUnit = paginatedList.attr('x-data-range-unit');
    //var totalElements = paginatedList.attr('x-data-total-items');
    var route = paginatedList.attr('x-data-route');
    var callback = paginatedList.attr('x-data-callback');
    
    $.ajax({
        type: 'GET',
        url: route,
        dataType: "json",
        headers: {
            "Range":  (page - 1) * range + '-' + page * range,
            "Range-Unit": rangeUnit
        },
        success: function(data) {
            window[callback](data);
            paginatedList.attr('x-data-page', page);
            paginatedList.paginate();
        }
    });
    
       
            
    
};