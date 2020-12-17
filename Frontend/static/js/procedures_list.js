/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var role = localStorage.getItem('role');
if (role == 'ADM') {
    var links = {
        'Users List': 'users_list.php',
        'User Registration': 'users_management.php',
        'Users Access Log': 'users_access_log.php',
        'Procedures List': 'procedures_list.php'
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

/* ----------PROCEDURES TABLE POPULATION----------*/
var options = {
    url: API_END_POINT + '/procedures?verbose=true',
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
        var tbody = $('#procedures-table');
        var activity_row = $("#procedure-information-row").html();

        $.each(data, function(index, obj) {
            let row = activity_row;
            row = row.replace(/{id}/ig, obj.procedureid);
            row = row.replace(/{procedure}/ig, obj.description);
            tbody.append(row);

            var listGroup = $('#procedure-competences-list-' + obj.procedureid);
            var listItem = $('#procedure-competence-item-row-' + obj.procedureid).html();
            $.each(obj.competencesrequired, function(index, obj) {
                let item = listItem;
                item = item.replace(/{competence}/ig, obj.name);
                listGroup.append(item);
            });
        });

        $('#procedures-table-master').dataTable({
            dom: '<"row mb-3" <"col" l><"col" f>>t<i><p>',
            ordering: false,
            responsive: true,
        });
    })
    .fail(function() {
        fireAlertError('Impossibile to show procedures!');
    });