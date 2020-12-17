/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var links = {
    'Homepage': 'dbloader_homepage.php',
    'Typologies List': 'typologies_list.php',
    'Materials List': 'materials_list.php',
    'Procedures List': 'procedures_list.php',
    'Competences List': 'competences_list.php',
    'Sites List': 'sites_list.php'
}

populateSidebar(links);

/* ----------MATERIALS TABLE POPULATION----------*/
var options = {
    url: API_END_POINT + '/materials',
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
        var tbody = $('#materials-table');
        var activity_row = $("#material-information-row").html();

        $.each(data, function(index, obj) {
            let row = activity_row;
            row = row.replace(/{id}/ig, obj.materialid);
            row = row.replace(/{material}/ig, obj.name);
            tbody.append(row);
        });

        $('#materials-table-master').dataTable({
            dom: '<"row mb-3" <"col" l><"col" f>>t<i><p>',
            order: [
                [1, 'asc']
            ],
            responsive: true,
        });
    })
    .fail(function() {
        fireAlertError('Impossibile to show materials!');
    });