function maintainerAvaibilitySuccess(data) {
    var tbody = $('#maintainer-table');
    var log_row = $("#maintainer-avaibility-row").html();

    var url = new URL(window.location.href);
    var skills = url.searchParams.get("skills[]").split(",");

    $.each(data, function(index, obj) {
        let row = log_row;
        let skills_satisfied = obj.competences.filter(x => skills.includes(x));

        obj.monday = '100%';
        obj.tuesday = '50%';
        obj.wednesday = '30%';
        obj.thursday = '20%';
        obj.friday = '0%';
        obj.saturday = '70%';
        obj.sunday = '100%';

        row = row.replace(/{userid}/ig, obj.userid);
        row = row.replace(/{maintainer}/ig, (obj.name + " " + obj.surname));
        row = row.replace(/{skills}/ig, (skills_satisfied.length + "/" + skills.length));
        row = row.replace(/{monday}/ig, obj.monday);
        row = row.replace(/{tuesday}/ig, obj.tuesday);
        row = row.replace(/{wednesday}/ig, obj.wednesday);
        row = row.replace(/{thursday}/ig, obj.thursday);
        row = row.replace(/{friday}/ig, obj.friday);
        row = row.replace(/{saturday}/ig, obj.saturday);
        row = row.replace(/{sunday}/ig, obj.sunday);
        tbody.append(row);
    });

    $('#manitainer-table-master').dataTable({
        dom: '<"row mb-3" <"col" l><"col" f>>t<i><p>',
        order: [
            [1, 'desc']
        ],
        responsive: true
    });

}

function maintainerAvaibilityFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert('Error in loading maintainers!');
    window.location.assign('activities_list.php');
}

function skillsSatisfiedSuccess(data) {
    var url = new URL(window.location.href);
    var skills = url.searchParams.get("skills[]").split(",");

    var skills_satisfied = data.competences.filter(x => skills.includes(x));

    $.each(skills_satisfied, function(index, obj) {
        $('input[name="' + obj + '"]').prop('checked', true);
    });
}

function skillsSatisfiedFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert('Impossible to load skills!');
    window.location.assign('activities_assignment.php');
}

function validateAssignmentAvailability(maintainer, activity) {
    var start = $('#assignment-start').val();
    var end = $('#assignment-end').val();

    var json = {
        'userid': maintainer
    };

    var options = {
        url: API_END_POINT + '/assignactivity/' + activity,
        type: 'POST',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json)
    };

    $.ajax(options).done(AssignmentSuccess).fail(AssignmentFailure);

}

function AssignmentSuccess() {
    alert('Correct assign activity');
    window.location.assign('activities_list.php');
}

function AssignmentFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Impossible to assign this activity");
}

function unassignActivity(activity) {
    var options = {
        url: API_END_POINT + '/assignactivity/' + activity,
        type: 'DELETE',
        headers: {
            'Authorization': localStorage.getItem('token')
        }
    };

    $.ajax(options).done(activityUnassignSuccess).fail(activityUnassignFailure);
}

function activityUnassignSuccess() {
    alert('Activity correctly unassigned!');
    window.location.assign('activities_list.php');
}

function activityUnassignFailure() {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert('Error in unassignig activity!');
}