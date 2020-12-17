/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var links = {
    'Activities List': 'activities_list.php',
    'Activities Insert': 'activities_management.php',
}

populateSidebar(links);

/* ------POPULATE MAINTAINERS SKILLS COLUMN-------*/

var url = new URL(window.location.href);
var id = url.searchParams.get('id');
$('#activity-id').html(id);

var options = {
    url: API_END_POINT + '/activities/' + id + '?verbose=true',
    type: 'GET',
    headers: {
        'Authorization': localStorage.getItem('token'),
        'Content-Type': 'application/json'
    },
    async: false,
    statusCode: {
        401: function() {
            fireAlertError('The session has expired, you need to login again!', logout);
        }
    }
};

$.ajax(options).done(
    function(data) {
        var procedureDict = JSON.parse(sessionStorage.getItem('procedureDict'));
        var skills = procedureDict[data.procedure.procedureid].competencesrequired;
        var container = $('#detail-skills');
        var skill_row = $("#skill-row").html();

        var ids = [];
        var names = [];
        $.each(skills, function(key, value) {
            let row = skill_row;
            row = row.replace(/{skill}/ig, value);
            row = row.replace(/{skillid}/ig, key);
            container.append(row);
            ids.push(key);
            names.push(value);
        });
        sessionStorage.setItem('competences', JSON.stringify({ 'ids': ids, 'names': names }));
        sessionStorage.setItem('estimated', data.estimatedtime);
        sessionStorage.setItem('scheduledweek', data.scheduledweek);
    }
).fail(
    function() {
        fireAlertError('Error in loading competences');
    }
);

var currentWeek = getIntervalFromWeek(parseInt(sessionStorage.getItem('scheduledweek')));

/* ------POPULATE MAINTAINERS AVAIABILITY TABLE-------*/
var options = {
    url: API_END_POINT + '/maintainers?availfromday=' + currentWeek[0] + '&availtoday=' + currentWeek[1] + '&availfromhour=08:00&availtohour=17:00',
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
        var tbody = $('#maintainer-table');
        var log_row = $("#maintainer-avaibility-row").html();

        var url = new URL(window.location.href);
        var skills = JSON.parse(sessionStorage.getItem('competences'));
        var skillsid = skills.ids;

        let unavailabilities = {};
        $.each(data, function(index, obj) {
            let row = log_row;

            let skills_satisfied = obj.competences.filter(x => skillsid.includes(x.competence));

            let firstWeekDay = new Date(getIntervalFromWeek(parseInt(sessionStorage.getItem('scheduledweek')))[0]);
            unavailabilities[obj.userid] = {};
            for (var i = 0; i < 7; i++) {
                unavail = obj.unavailability[firstWeekDay.toISOString().split("T")[0]];
                if (unavail !== undefined)
                    unavailabilities[obj.userid][i] = unavail;
                firstWeekDay.setDate(firstWeekDay.getDate() + 1);
            }

            let weekPerc = [100, 100, 100, 100, 100, 100, 100]
            $.each(unavailabilities[obj.userid], function(key, value) {
                let avaibility = 36;
                value.forEach(element => {
                    let left = element['start'].split(":");
                    left = parseInt(left[0]) * 4 + parseInt(left[1]) / 15;
                    let right = element['end'].split(":");
                    right = parseInt(right[0]) * 4 + parseInt(right[1]) / 15;
                    avaibility -= (right - left);
                });
                weekPerc[key] = (parseInt((avaibility / 36) * 100));
            });

            colors = ['#D9534F', '#F0AD4E', '#FFF066', '#5CB85C'];

            row = row.replace(/{userid}/ig, obj.userid);
            row = row.replace(/{maintainer}/ig, (obj.name + " " + obj.surname));
            row = row.replace(/{skills}/ig, (skills_satisfied.length + "/" + skillsid.length));
            row = row.replace(/{monday}/ig, weekPerc[0] + '%');
            row = row.replace(/{monday_color}/ig, colors[Math.floor(Math.abs(weekPerc[0] - 1) / 25)]);
            row = row.replace(/{tuesday}/ig, weekPerc[1] + '%');
            row = row.replace(/{tuesday_color}/ig, colors[Math.floor(Math.abs(weekPerc[1] - 1) / 25)]);
            row = row.replace(/{wednesday}/ig, weekPerc[2] + '%');
            row = row.replace(/{wednesday_color}/ig, colors[Math.floor(Math.abs(weekPerc[2] - 1) / 25)]);
            row = row.replace(/{thursday}/ig, weekPerc[3] + '%');
            row = row.replace(/{thursday_color}/ig, colors[Math.floor(Math.abs(weekPerc[3] - 1) / 25)]);
            row = row.replace(/{friday}/ig, weekPerc[4] + '%');
            row = row.replace(/{friday_color}/ig, colors[Math.floor(Math.abs(weekPerc[4] - 1) / 25)]);
            row = row.replace(/{saturday}/ig, weekPerc[5] + '%');
            row = row.replace(/{saturday_color}/ig, colors[Math.floor(Math.abs(weekPerc[5] - 1) / 25)]);
            row = row.replace(/{sunday}/ig, weekPerc[6] + '%');
            row = row.replace(/{sunday_color}/ig, colors[Math.floor(Math.abs(weekPerc[6] - 1) / 25)]);
            tbody.append(row);
        });

        sessionStorage.setItem('unavailabilities', JSON.stringify(unavailabilities));

        $('#manitainer-table-master').dataTable({
            dom: '<"row mb-3" <"col" l><"col" f>>t<i><p>',
            order: [
                [1, 'desc']
            ],
            responsive: true
        });

    })
    .fail(function() {
        fireAlertError('Error in loading maintainers!', function() {
            window.location.assign('activities_list.php');
        });
    });