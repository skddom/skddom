var url_to_audit, recieved_data;
var fetch_timeout_seconds = 20; // skip source if reply wasn't recieved in 20 sec.
var fetch_timeout_id; // private
var stop = false;

function audit_stop() {
    if (fetch_timeout_id) clearTimeout(fetch_timeout_id);
    stop = true;
    document.getElementById('please_wait_div').style.display = 'none';
    document.getElementById('loading_done').style.display = '';
}

function audit_start(url) {
    url_to_audit = (url ? url : document.getElementById('url_to_audit').value);

    if (!url_to_audit.match(/[\w\-\.]+\.[a-z]{2,}/i)) {
        alert(NETCAT_MODULE_AUDITOR_WRONG_URL);
        try {
            document.getElementById('url_to_audit').focus();
        }
        catch(e) {}
        return;
    }

    document.getElementById('audit_results').innerHTML = '';

    recieved_data = [];
    document.getElementById('please_wait_div').style.display = '';
    document.getElementById('loading_done').style.display = 'none';
    request_audit_data();
}

function request_audit_data() {
    if (stop) {
        stop=false;
        return;
    }

    var dst = document.getElementById('audit_results');

    if (fetch_timeout_id) clearTimeout(fetch_timeout_id);

    for (var group in data_to_fetch) {
        for (var i in data_to_fetch[group]) {
            var what = data_to_fetch[group][i][0];

            if (!recieved_data[url_to_audit+':'+what]) {
                // if group doesn't exist, create it
                if (!document.getElementById('h'+group)) {
                    dst.innerHTML += "<h4 id='h"+group+"'>"+group_labels[group]+"</h4>";
                }

                // add to 'checked'
                recieved_data[url_to_audit+':'+what] = 1;

                document.getElementById('audit_iframe').contentWindow.location.href =
                "get_data.php?url=" + escape(url_to_audit) + "&what="+what;

                fetch_timeout_id = setTimeout('request_audit_data()', fetch_timeout_seconds * 1000);

                return;
            }
        }
    }
    document.getElementById('please_wait_div').style.display = 'none';
    document.getElementById('loading_done').style.display = '';
}

function print_audit_data(system, data) {
    document.getElementById('audit_results').innerHTML +=
    "<div><b>"+data.name+":</b> "+
    (data.ok && data.href ? "<a target=_blank href='"+data.href+"'>"+data.value+"</a>"
        : (data.value ? data.value : 0)) +
    "</div>";
    request_audit_data();
}