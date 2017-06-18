function search(limit) {

    var u = new Url;
    var data = {'limit': limit};
    var inputVal = $('#search-input').val();
    
    u.clearQuery();

    if (inputVal.length > 0) {
        data.search = inputVal;
    }
    $.extend(u.query, data);

    window.location.href = u;
}

function changeUrl(data, clearUrl) {

    var u = new Url;
    clearUrl = clearUrl || false;

    if (clearUrl) {
        u.clearQuery();
    }

    $.extend(u.query, data);
    window.location.href = u;
}