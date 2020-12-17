/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
role = localStorage.getItem('role');
if (role == 'ADM') {
    var links = {
        'Users List': 'users_list.php',
        'User Registration': 'users_management.php',
        'Users Access Log': 'users_access_log.php',
        'Procedures List': 'procedures_list.php'
    }
} else if (role == 'PLN') {
    var links = {
        'Activities List': 'activities_list.php',
        'Activities Insert': 'activities_management.php'
    }
} else if (role == 'DBL') {
    var links = {
        'Homepage': 'dbloader_homepage.php',
        'Typologies List': 'typologies_list.php',
        'Materials List': 'materials_list.php',
        'Procedures List': 'procedures_list.php',
        'Competences List': 'competences_list.php',
        'Procedures List': 'procedures_list.php',
        'Sites List': 'sites_list.php'
    }
}
populateSidebar(links);

var url = new URL(window.location.href);
var id = url.searchParams.get("id");

/* -----------GET THE USER INFORMATION------------
-----------in order to manage the logout----------*/
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
                $('input[value="' + value + '"]').attr('checked', true);
            })
        }

        $.each(data, function(k, v) {
            $('#' + k).val(v)
        });
    })
    .fail(function() {
        fireAlertError('Error in loading user info!', function() {
            window.history.back();
        });
    })