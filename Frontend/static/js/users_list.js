/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var links = {
    'Users List': 'users_list.php',
    'User Registration': 'users_management.php',
    'Users Access Log': 'users_access_log.php',
    'Procedures List': 'procedures_list.php'
}

populateSidebar(links);

/* ---------------USERS TABLE POPULATION----------------*/

var options = {
    url: API_END_POINT + '/users',
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
        var tbody = $('#users-table-tbody');
        var user_row = $("#user-row-template").html();

        $.each(data, function(index, obj) {
            let row = user_row;
            row = row.replace(/{userid}/ig, obj.userid);
            row = row.replace(/{role}/ig, obj.role);
            row = row.replace(/{name}/ig, obj.name);
            row = row.replace(/{surname}/ig, obj.surname);
            row = row.replace(/{email}/ig, obj.email);
            row = row.replace(/{phonenumber}/ig, obj.phonenumber);
            row = row.replace(/{birthdate}/ig, obj.birthdate);
            tbody.append(row);
        });

        $('#users-table').dataTable({
            responsive: true,
            order: [
                [1, 'asc']
            ],
            "columns": [
                { "className": 'dt-center' },
                null, null, null, null, null, null, null,
                { "className": 'dt-center' }
            ]
        });
    })
    .fail(function() {
        fireAlertError('Impossibile to show users!');
    });