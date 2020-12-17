/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var links = {
    'Users List': 'users_list.php',
    'User Registration': 'users_management.php',
    'Users Access Log': 'users_access_log.php',
    'Procedures List': 'procedures_list.php'
}

populateSidebar(links);

/* ------------GET MAINTAINERS COMPETENCES---------------
-----in order to show them in the registration form-------*/
var options = {
    url: API_END_POINT + '/competences',
    type: 'GET',
    async: false,
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
        var competences = $('#competences');
        var competence_row = $("#competence-row").html();

        $.each(data, function(index, obj) {
            let row = competence_row;
            row = row.replace(/{index}/ig, index);
            row = row.replace(/{competence}/ig, obj.name);
            row = row.replace(/{competenceid}/ig, obj.competenceid);
            competences.append(row);
        });
    }).fail(function() {
        fireAlertError('Impossible to load competences for the maintainers!', function() {
            window.location.assign("users_list.php");
        });
    });

var url = new URL(window.location.href);
var update = url.searchParams.get("update");
var id = url.searchParams.get("id");

/* ------------REFILL THE REGISTRATION FORM---------------
---in order to show the info and allow the modification---*/

if (update == 'true') {

    var options = {
        url: API_END_POINT + '/users/' + id,
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
            var role = data.role;
            delete data.role;

            var select = $('#role');
            select[0].selectedIndex = $('#' + role).index();

            showCompetences(select[0]);

            if (role == 'MNT') {
                var competences = data.competences;
                delete data.competences;
                $.each(competences, function(index, value) {
                    $('input[value="' + value.competence + '"]').attr('checked', true);
                })
            }

            $.each(data, function(k, v) {
                $('#' + k).val(v)
            });
        }).fail(function() {
            fireAlertError('Error in loading user info!', function() {
                window.history.back();
            });
        });

    $('#userid').attr('readonly', true);
    $('#password').attr('placeholder', 'Leave blank if you don\'t want to change the password');
    $('#role').attr('disabled', true);
    $('.card-header span[class="h2"]').html('Update User');
    $('#send-update-button').attr('onclick', 'userUpdate("registration-form")').html('Update');
} else {
    /* ---------------VALIDATE BIRTHDATE----------------*/
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    var max = String(parseInt(yyyy) - 18) + '-' + mm + '-' + dd;
    var min = String(parseInt(yyyy) - 100) + '-' + mm + '-' + dd;

    var birthDate = $('#birthdate')
    birthDate.attr('min', min);
    birthDate.attr('max', max);
    birthDate.attr('value', max);
}