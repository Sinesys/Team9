/* ---------------VALIDATE ACTIVITY FORM----------------*/
function activityValidate() {
    var code = $('#activityid');
    if (code.val() == '') {
        fireAlertError('The id field cannot be blank!');
        return false;
    }

    var week = parseInt($('#scheduledweek').val());
    if (week < 1 || week > 52) {
        fireAlertError('Insert a week number between 1 and 52!');
        return false;
    }

    var estimatedtime = parseInt($('#estimatedtime').val());
    if (estimatedtime < 15) {
        fireAlertError('Insert an estimated time greater or equal to 15!');
        return false;

    } else if (estimatedtime % 15 != 0) {
        fireAlertError('Insert an estimated time multiple of 15!');
        return false;
    }

    if (parseInt($('#procedure').val()) == -1) {
        fireAlertError('Select a procedure!');
        return false;
    }

    if (parseInt($('#site').val()) == -1) {
        fireAlertError('Select a site!');
        return false;
    }

    if (parseInt($('#typology').val()) == -1) {
        fireAlertError('Select a typology!');
        return false;
    }

    if (!code.val().match(/^([0-9]|[a-zA-Z]|_){1,20}$/)) {
        fireAlertError('Activity ID is not valid!');
        return false;
    }
    return true;
}

/* ---------------INSERT ACTIVITY---------------*/
function activityInsert() {

    if (!activityValidate())
        return;

    var json = {};
    $('#activity-insert-form input:not(input[role=textbox]), textarea, select').each(function() {
        if ($(this).attr('name') == 'interruptible')
            json['interruptible'] = $(this).val() == 'true' ? true : false;
        else
            json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/activities',
        type: 'POST',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json),
        statusCode: {
            401: function() {
                fireAlertError('The session has expired, you need to login again!', logout);
            },
            400: function() {
                fireAlertError('Error in inserting activity!');
            }
        }
    };

    $.ajax(options)
        .done(function() {
            fireAlertSuccess('Activity inserted with success!', function() {
                window.location.assign('activities_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Server error. Retry later!', function() {
                window.location.assign('index.html');
            });
        });
}

/* ---------------UPDATE ACTIVITY----------------*/
function activityUpdate() {

    if (!activityValidate())
        return;

    var json = {};
    var week = parseInt($('#scheduledweek').val());
    if (week < 1 || week > 52) {
        fireAlertError('Insert a week number between 1 and 52!');
        return;
    }

    $('#activity-insert-form input:not(input[role=textbox]), textarea, select').each(function() {
        if ($(this).attr('name') == 'interruptible')
            json['interruptible'] = $(this).val() == 'true' ? true : false;
        else
            json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/activities/' + json.activityid,
        type: 'PUT',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json),
        statusCode: {
            401: function() {
                fireAlertError('The session has expired, you need to login again!', logout);
            },
            400: function() {
                fireAlertError('Error in updating activity info!');
            }
        }
    }
    $.ajax(options)
        .done(function() {
            fireAlertSuccess('Activity updated with success!', function() {
                window.location.assign('activities_list.php');
            });
        }).fail(function() {
            fireAlertError('Server error. Retry later!', function() {
                window.location.assign('index.html');
            });
        });
}

/* ---------------DELETE ACTIVITY----------------*/
function activityDelete(activity) {
    fireConfirm('Are you sure you want to delete this activity?', function() {
        options = {
            url: API_END_POINT + '/activities/' + activity,
            type: 'DELETE',
            headers: {
                'Authorization': localStorage.getItem('token')
            },
            statusCode: {
                401: function() {
                    fireAlertError('The session has expired, you need to login again!', logout);
                },
                400: function() {
                    fireAlertError('Error in deleting activity!');
                }
            }
        };

        $.ajax(options)
            .done(function() {
                fireAlertSuccess('Activity deleted with success!', function() {
                    window.location.assign('activities_list.php');
                });
            })
            .fail(function() {
                fireAlertError('Server error. Retry later!', function() {
                    window.location.assign('index.html');
                });
            });
    })
}

function getSmpFile(procedure) {
    fetch(API_END_POINT + '/procedures/' + procedure + '/SMP', {
            method: 'GET',
            headers: {
                'Authorization': localStorage.getItem('token'),
            }
        })
        .then(resp => {
            if (!resp.ok)
                throw Error(response.statusText);
            return resp.blob()
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'smpfile.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(() => fireAlertError("No SMP file exists for this procedure!"));
}

function viewActivityProcedureInfo(value) {
    if (value != '-1') {
        $('#activity-procedure-modal').modal('show');
        var procedures = JSON.parse(sessionStorage.getItem('procedureDict'));
        $('#activity-procedure-procedureid').val(value);
        $('#activity-procedure-description').val(procedures[value].description);
        var body = $('#activity-procedure-competences');
        var procedure_competence_row = $('#activity-procedure-competence-row').html();
        $.each(procedures[value].competencesrequired, function(key, value) {
            let row = procedure_competence_row;
            row = row.replace(/{activity-procedure-competence}/, value);
            row = row.replace(/{index}/ig, key);
            body.append(row);
        });
    }
}