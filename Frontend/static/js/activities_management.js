function validateInsertActivity(form) {
    var code = $('#activityid');
    if (code.val() == '') {
        alert("The id field can't be blank");
        return;
    }

    var week = parseInt($('#scheduledweek').val());
    if (week < 1 || week > 52) {
        alert("Insert a week number between 1 and 52");
        return;
    }

    if (!isRegexMatch(code[0], /^([0-9]|[a-zA-Z]|_){1,20}$/)) {
        alert('Activity ID is not valid!');
        return;
    }

    sendActivityJSON(form);
}

function sendActivityJSON(form) {
    var json = {};

    $('#' + form + ' input, textarea').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/activities',
        type: 'POST',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json)
    };

    $.ajax(options).done(activityInsertSuccess).fail(activityInsertFailure);
}

function activityInsertSuccess() {
    alert("Activity inserted with success!");
    window.location.assign('activities_list.php');
}

function activityInsertFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }

    alert("Error in inserting activity!");
    window.location.assign('activities_list.php');
}

function updateActivity(form) {
    var json = {};

    var week = parseInt($('#scheduledweek').val());
    if (week < 1 || week > 52) {
        alert("Insert a week number between 1 and 52");
        return;
    }

    $('#' + form + ' input, textarea').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/activities/' + json.activityid,
        type: 'PUT',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json)
    };

    $.ajax(options).done(activityUpdateSuccess).fail(activityUpdateFailure);
}

function activityUpdateSuccess(data) {
    alert(data.message);
    window.location.assign('activities_list.php');
}

function activityUpdateFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Error in updating activity info!");
    window.location.reload();
}

function deleteActivity(id) {
    var ret = confirm('Are you sure you want to delete this activity?');

    if (ret) {

        options = {
            url: API_END_POINT + '/activities/' + id,
            type: 'DELETE',
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        };

        $.ajax(options).done(activityDeleteSuccess).fail(activityDeleteFailure);
    }
}

function activityDeleteSuccess() {
    alert("Activity deleted with success!");
    window.location.assign('activities_list.php');
}

function activityDeleteFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Error in deleting activity!");
    window.location.assign('activities_list.php');
}

function activitiesListSuccess(data) {
    var tbody = $('#activities-table');
    var activity_row = $("#activity-information-row").html();

    $.each(data, function(index, obj) {
        let row = activity_row;
        row = row.replace(/{activityid}/ig, obj.activityid);
        row = row.replace(/{description}/ig, obj.description);
        row = row.replace(/{scheduledweek}/ig, obj.scheduledweek);
        row = row.replace(/{assignedto}/ig, obj.assignedto);
        tbody.append(row);
    });

    $('#activities-table-master').dataTable({
        dom: '<"row mb-3" <"col" l><"col" f>>t<i><p>',
        order: [
            [3, 'asc']
        ],
        responsive: true,
        "drawCallback": function(settings) {
            var api = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var last = null;

            api.column(3, { page: 'current' }).data().each(function(group, i) {
                if (last !== group) {
                    $(rows).eq(i).before('<tr class="group" style="background-color: Gainsboro;"><td class="text-center" colspan="6"><span class="h5">WEEK ' + group + '</span></td></tr>');
                    last = group;
                }
                $(rows).eq(i).attr("rel", group);
            });
        }
    });
}

function activitiesListFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Impossibile to show activities!");
}

function activityInfoSuccess(data) {
    $.each(data, function(k, v) {
        $("#" + k).val(v)
    })
}

function activityInfoFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("ERROR!");
    window.location.assign('activities_list.php');
}

function activityDetailSuccess(data) {
    data.area = 'Area1';
    data.typology = 'Typology1';
    data.estimated = 90;
    var competences = ['soft skills', 'hard skills', 'knowledge of the machinery', 'ability to read technical diagrams', 'speed of intervention'];

    $.each(data, function(k, v) {
        $("#detail-" + k).html(v)
    })

    $.each(competences, function(index, value) {
        $('#detail-competences').append('<li class="list-group-item">' + value + '</li>');
    })
    $('#detail-button-edit').attr('onclick', 'window.location.assign("activities_management.php?update=true&id=' + data.activityid + '")');
    if (data.assignedto == null)
        $('#detail-button-assign').attr('onclick', 'window.location.assign("activities_assignment.php?id=' + data.activityid + '&estimatedtime=' + data.estimated + '&skills[]=' + competences + '")').html('<i class="fas fa-handshake"></i>').addClass('btn-success').removeClass('btn-danger');
    else
        $('#detail-button-assign').attr('onclick', 'unassignActivity("' + data.activityid + '")').html('Unassign').addClass('btn-danger').removeClass('btn-success');
}

function activityDetailFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("ERROR!");
    window.location.assign('activities_list.php');
}