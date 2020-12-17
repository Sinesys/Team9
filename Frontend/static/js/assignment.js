function activityAssignment(maintainer, activity, weekDay) {
    var start = $('#assignment-start').val();
    var end = $('#assignment-end').val();

    var firstWeekDay = new Date(getIntervalFromWeek(parseInt(sessionStorage.getItem('scheduledweek')))[0]);
    firstWeekDay.setDate(firstWeekDay.getDate() + weekDay);

    var json = {
        'userid': maintainer,
        'day': firstWeekDay.toISOString().split("T")[0],
        'starttime': start,
        'endtime': end
    };

    var options = {
        url: API_END_POINT + '/assignactivity/' + activity,
        type: 'POST',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json),
        statusCode: {
            401: function() {
                fireAlertError('The session has expired, you need to login again!', logout);
            }
        }
    };

    $.ajax(options)
        .done(function() {
            fireAlertSuccess('Activity assigned with success!', function() {
                window.location.assign('activities_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Impossible to assign this activity!');
        });

}

/*------------------------------------------------------------------*/

function activityUnassignment(activity) {
    var options = {
        url: API_END_POINT + '/assignactivity/' + activity,
        type: 'DELETE',
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
        .done(function() {
            fireAlertSuccess('Activity unassigned with success!', function() {
                window.location.assign('activities_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Error in unassignig activity!');
        });
}