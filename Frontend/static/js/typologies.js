/* ---------------VALIDATE ACTIVITY TYPOLOGY FORM----------------*/
function typologyValidate() {
    var condition = true;
    $('#typology-form input').each(function() {
        if ($(this).val() == "") {
            fireAlertError('The fields cannot be blank!');
            condition = false;
            return false;
        }
    })

    if (!condition)
        return false;

    if (!$('#typologyid').val().match(/^([0-9]|[a-zA-Z]|_){1,20}$/)) {
        fireAlertError('Typology ID is not valid!');
        return false;
    }
    return true;
}

/* ---------------INSERT ACTIVITY TYPOLOGY----------------*/
function typologyInsert() {

    if (!typologyValidate())
        return;

    var json = {};
    $('#typology-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/activitytypologies',
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
            fireAlertSuccess('Typology inserted with success!', function() {
                window.location.assign('typologies_list.php');
            });
        }).fail(function() {
            fireAlertError('Error in inserting typology!', function() {
                window.location.assign('typologies_list.php');
            });
        });

}

/* ---------------UPDATE ACTIVITY TYPOLOGY----------------*/
function typologyUpdate() {

    if (!typologyValidate())
        return;

    var json = {};
    $('#typology-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/activitytypologies/' + json.typologyid,
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
    $.ajax(options).done(function() {
        fireAlertSuccess('Typology updated with success!', function() {
            window.location.assign('typologies_list.php');
        });
    }).fail(function() {
        fireAlertError('Error in updating typology info!', function() {
            window.location.reload();
        });
    });
}

/* ---------------DELETE ACTIVITY TYPOLOGY----------------*/
function typologyDelete(typology) {
    fireConfirm('Are you sure you want to delete this typology?', function() {
        options = {
            url: API_END_POINT + '/activitytypologies/' + typology,
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
                fireAlertSuccess('Typology deleted with success!', function() {
                    window.location.assign('typologies_list.php');
                });
            }).fail(function() {
                fireAlertError('Error in deleting typology!', function() {
                    window.location.assign('typologies_list.php');
                });
            });
    });
}