/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var links = {
    'Activities List': 'activities_list.php',
    'Activities Insert': 'activities_management.php',
}

populateSidebar(links);

var options = {
    url: API_END_POINT + '/procedures?verbose=true',
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

/* ----------ACTIVITIES TABLE POPULATION----------*/
$.ajax(options)
    .done(function(data) {
        var procedureDict = {};

        $.each(data, function(index, obj) {
            let procedureInfo = {};
            procedureInfo['description'] = obj.description;
            procedureInfo['competencesrequired'] = {};
            $.each(obj.competencesrequired, function(index, competence) {
                procedureInfo['competencesrequired'][competence.competenceid] = competence.name;
            });
            procedureDict[obj.procedureid] = procedureInfo;
        });

        sessionStorage.setItem('procedureDict', JSON.stringify(procedureDict));

    })
    .fail(function() {
        fireAlertError('Error in loading procedures!');
    });

var options = {
    url: API_END_POINT + '/activities?verbose=true',
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
        var tbody = $('#activities-table');
        var activity_row = $("#activity-information-row").html();

        $.each(data, function(index, obj) {
            let row = activity_row;
            row = row.replace(/{activityid}/ig, obj.activityid);
            row = row.replace(/{scheduledweek}/ig, obj.scheduledweek);
            row = row.replace(/{assignedto}/ig, obj.assignedto == null ? 'Not assigned' : obj.assignedto.maintainer);
            row = row.replace(/{scheduledweek}/ig, obj.scheduledweek);
            row = row.replace(/{scheduledday}/ig, obj.assignedto == null ? 'Not scheduled' : obj.assignedto.day);
            row = row.replace(/{site}/ig, obj.site == null ? 'Not assigned' : obj.site.area + ' - ' + obj.site.department);
            row = row.replace(/{typology}/ig, obj.typology == null ? 'Not assigned' : obj.typology.description);
            row = row.replace(/{estimated}/ig, obj.estimatedtime == null ? 'Not estimated' : obj.estimatedtime + ' min');
            tbody.append(row);
        });

        $('#activities-table-master').dataTable({
            dom: '<"row mb-3" <"col" l><"col" f>>t<i><p>',
            order: [
                [2, 'desc']
            ],
            responsive: true,
            "drawCallback": function(settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(2, { page: 'current' }).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before('<tr class="group" style="background-color: Gainsboro;"><td class="text-center" colspan="10"><span class="h5">WEEK ' + group + '</span></td></tr>');
                        last = group;
                    }
                    $(rows).eq(i).attr("rel", group);
                });
            }
        });
    })
    .fail(function() {
        fireAlertError('Server error. Retry later!', function() {
            window.location.assign('index.html');
        });
    });