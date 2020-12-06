/* -------------------- USERS UPDATE -------------------- */

function updateUser(form) {
    var json = {};

    $('#' + form + ' input:not([type="checkbox"]), #role').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    json['competences'] = [];
    var competence = $('#competences input:checked').each(function(index) {
        json['competences'][index] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/users/' + json.userid,
        type: 'PUT',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json)
    };
    $.ajax(options).done(userUpdateSuccess).fail(userUpdateFailure);
}

function userUpdateSuccess(data) {
    alert(data.message);
    window.location.assign('users_list.php');
}

function userUpdateFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Error in updating user info!");
    window.location.reload();
}


function deleteUser(id) {
    var ret = confirm('Are you sure you want to delete this user?');

    if (!ret)
        return

    var options = {
        url: API_END_POINT + '/users/' + id,
        type: 'DELETE',
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    };

    $.ajax(options).done(userDeleteSuccess).fail(userDeleteFailure);
}

function userDeleteSuccess() {
    alert("User deleted with success!");
    window.location.assign('users_list.php');
}

function userDeleteFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Error in deleting user!");
    window.location.assign("users_list.php");
}

function userInfoSuccess(data) {
    var role = data.role;
    delete data.role;

    var select = $('#role');
    select[0].selectedIndex = $('#' + role).index();

    showCompetences(select[0]);

    if (role == 'MNT') {
        var competences = data.competences;
        delete data.competences;
        $.each(competences, function(index, value) {
            $('input[value="' + value + '"]').attr('checked', true);
        })
    }

    $.each(data, function(k, v) {
        $('#' + k).val(v)
    });
}

function userInfoFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("ERROR!");
    window.history.back();
}

function usersListSuccess(data) {
    var tbody = $('#users-table');
    var user_row = $("#user-row").html();

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

    $('#users-table-master').dataTable({
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
}

function usersListFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Impossibile to show users");
}

function usersAccessLogSuccess(data) {
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
}

function usersAccessLogFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Impossibile to show the accesses log of the users!");
}