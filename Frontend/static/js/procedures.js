/* ---------------VALIDATE ACTIVITY PROCEDURE FORM----------------*/
function procedureValidate() {
    var condition = true;
    $('#procedure-form input:not([type=file])').each(function() {
        if ($(this).val() == "") {
            fireAlertError('The fields cannot be blank!');
            condition = false;
            return;
        }
    })

    if (!condition)
        return false;

    if (!$('#procedureid').val().match(/^([0-9]|[a-zA-Z]|_){1,20}$/)) {
        fireAlertError('Procedure ID is not valid!');
        return false;
    }

    return true;
}

/* ---------------INSERT ACTIVITY PROCEDURE----------------*/
function procedureInsert() {

    if (!procedureValidate())
        return;

    var json = {};
    $('#procedure-form input:not([type="checkbox"], [type=file])').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    json['competencesrequired'] = [];
    $('#procedure-form input:checked').each(function(index) {
        json['competencesrequired'][index] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/procedures',
        type: 'POST',
        async: 'true',
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

    var file = $("#smpFile")[0].files[0];
    ajaxCall = $.ajax(options)
        .done(function() {

            fireAlertSuccess('Procedure insert with success!', function() { window.location.assign("procedures_list.php"); });

            if (file == undefined)
                return

            var formData = new FormData();
            formData.append('file', file);
            var options = {
                url: API_END_POINT + '/procedures/' + json.procedureid + '/SMP',
                type: 'POST',
                async: 'true',
                processData: false,
                contentType: false,
                headers: {
                    'Authorization': localStorage.getItem('token'),
                },
                data: formData
            };
            $.ajax(options).fail(function() {
                fireAlertError('Error in uploading the file!', function() {
                    window.location.assign("procedures_list.php");
                })
            });
        })
        .fail(function() {
            fireAlertError('Impossibile to insert the procedure!');
        });
}

/* ---------------UPDATE ACTIVITY PROCEDURE----------------*/
function procedureUpdate() {

    if (!procedureValidate())
        return;

    var json = {};
    $('#procedure-form input:not([type="checkbox"], [type=file])').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    json['competencesrequired'] = [];
    var competence = $('#procedure-form input:checked').each(function(index) {
        json['competencesrequired'][index] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/procedures/' + json.procedureid,
        type: 'PUT',
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
    }
    var inputFile = $("#smpFile");
    if (inputFile !== undefined)
        inputFile = inputFile[0].files[0];

    $.ajax(options)
        .done(function() {
            fireAlertSuccess('Procedure updated with success!', function() {
                window.location.assign('procedures_list.php');
            });

            if (inputFile == undefined)
                return

            var formData = new FormData();
            formData.append('file', inputFile);
            var options = {
                url: API_END_POINT + '/procedures/' + json.procedureid + '/SMP',
                type: 'POST',
                async: 'true',
                processData: false,
                contentType: false,
                headers: {
                    'Authorization': localStorage.getItem('token'),
                },
                data: formData
            };
            $.ajax(options).fail(function() {
                fireAlertError('Error in uploading the file!', function() {
                    window.location.assign("procedures_list.php");
                })
            });
        })
        .fail(function() {
            fireAlertError('Error in updating procedure info!', function() {
                window.location.reload();
            });
        });

}

/* ---------------DELETE ACTIVITY PROCEDURE----------------*/
function procedureDelete(id) {
    fireConfirm('Are you sure you want to delete this procedure?', function() {
        options = {
            url: API_END_POINT + '/procedures/' + id,
            type: 'DELETE',
            headers: {
                'Authorization': localStorage.getItem('token')
            },
            statusCode: {
                401: function() {
                    fireAlertError('The session has expired, you need to login again!', logout);
                }
            }
        };

        $.ajax(options)
            .done(function() {
                fireAlertSuccess('Procedure deleted with success!', function() {
                    window.location.assign('procedures_list.php');
                });
            })
            .fail(function() {
                fireAlertError('Error in deleting procedure!', function() {
                    window.location.assign('procedures_list.php');
                });
            });
    })
}

function smpFileDelete(procedure) {
    var options = {
        url: API_END_POINT + '/procedures/' + procedure + '/SMP',
        type: 'DELETE',
        headers: {
            'Authorization': localStorage.getItem('token'),
        }
    };
    $.ajax(options)
        .done(function() {
            fireAlertSuccess('SMP delete with success!', function() {
                window.location.assign("procedures_list.php");
            })
        })
        .fail(function() {
            fireAlertError('Error in deleting the file!', function() {
                window.location.assign("procedures_list.php");
            })
        });
}