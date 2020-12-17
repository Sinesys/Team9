/* ---------------VALIDATE ACTIVITY COMPETENCE FORM----------------*/
function competenceValidate() {
    var condition = true;
    $('#competence-form input').each(function() {
        if ($(this).val() == "") {
            fireAlertError('The fields cannot be blank!');
            condition = false;
            return false;
        }
    })

    if (!condition)
        return false;

    if (!$('#competenceid').val().match(/^([0-9]|[a-zA-Z]|_){1,20}$/)) {
        fireAlertError('Competence ID is not valid!');
        return false;
    }
    return true;
}

/* ---------------INSERT ACTIVITY COMPETENCE----------------*/
function competenceInsert() {

    if (!competenceValidate())
        return;

    var json = {};
    $('#competence-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/competences',
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
            fireAlertSuccess('Competence inserted with success!', function() {
                window.location.assign('competences_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Error in inserting competence!', function() {
                window.location.assign('competences_list.php');
            });
        });

}

/* ---------------UPDATE ACTIVITY COMPETENCE----------------*/
function competenceUpdate() {

    if (!competenceValidate())
        return;

    var json = {};
    $('#competence-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/competences/' + json.competenceid,
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
    $.ajax(options)
        .done(function() {
            fireAlertSuccess('Competence updated with success!', function() {
                window.location.assign('competences_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Error in updating competence info!', function() {
                window.location.reload();
            });
        });
}

/* ---------------DELETE ACTIVITY COMPETENCE----------------*/
function competenceDelete(id) {
    fireConfirm('Are you sure you want to delete this competence?', function() {
        options = {
            url: API_END_POINT + '/competences/' + id,
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
                fireAlertSuccess('Competence deleted with success!', function() {
                    window.location.assign('competences_list.php');
                });
            })
            .fail(function() {
                fireAlertError('Error in deleting competence!', function() {
                    window.location.assign('competences_list.php');
                });
            });
    })
}