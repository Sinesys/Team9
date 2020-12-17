/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var links = {
    'Users List': 'users_list.php',
    'User Registration': 'users_management.php',
    'Users Access Log': 'users_access_log.php',
    'Procedures List': 'procedures_list.php'
}

populateSidebar(links);

/* ---------------ACCESS LOG REQUEST----------------*/
var options = {
    url: API_END_POINT + '/accesslog',
    type: 'GET',
    headers: {
        'Authorization': localStorage.getItem('token'),
        'Content-Type': 'application/json'
    },
    statusCode: {
        401: function() {
            fireAlertError('The session has expired, you need to login again!', logout);
        }
    }
};

$.ajax(options)
    .done(function(data) {
        var tbody = $('#users-log-table');
        var log_row = $("#log-row").html();

        $.each(data, function(index, obj) {
            let row = log_row;
            row = row.replace(/{userid}/ig, obj.userid);
            row = row.replace(/{accesstime}/ig, obj.accesstime);
            tbody.append(row);
        });

        $('#users-log-table-master').dataTable({
            responsive: true,
            order: [
                [1, 'desc']
            ],
            dom: '<"row mb-3" <"col" l> <"col d-flex justify-content-center" B> <"col" f>>t<i><p>',
            buttons: [
                'excelHtml5',
                'pdfHtml5'
            ]
        });

        $('.buttons-pdf').append('&nbsp;<i class="far fa-file-pdf"></i>').addClass('btn btn-danger ml-2');
        $('.buttons-excel').append('&nbsp;<i class="far fa-file-excel"></i>').addClass('btn btn-success');
    })
    .fail(function() {
        fireAlertError('Impossibile to show the accesses log of the users!');
    });