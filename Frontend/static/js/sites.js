/* ---------------VALIDATE ACTIVITY SITE FORM----------------*/
function siteValidate() {
    var condition = true;
    $('#site-form input').each(function() {
        if ($(this).val() == "") {
            fireAlertError('The fields cannot be blank!');
            condition = false;
            return false;
        }
    })

    if (!condition)
        return false;

    if (!$('#siteid').val().match(/^([0-9]|[a-zA-Z]|_){1,20}$/)) {
        fireAlertError('Site ID is not valid!');
        return false;
    }

    return true;
}

/* ---------------INSERT ACTIVITY SITE----------------*/
function siteInsert() {
    if (!siteValidate())
        return;

    var json = {};
    $('#site-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/sites',
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
            fireAlertSuccess('Site inserted with success!', function() {
                window.location.assign('sites_list.php');
            });
        }).fail(function() {
            fireAlertError('Error in inserting site!', function() {
                window.location.assign('sites_list.php');
            });
        });

}

/* ---------------UPDATE ACTIVITY SITE----------------*/
function siteUpdate() {

    if (!siteValidate())
        return;

    var json = {};
    $('#site-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/sites/' + json.siteid,
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
            fireAlertSuccess('Site updated with success!', function() {
                window.location.assign('sites_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Error in updating site info!', function() {
                window.location.reload();
            });
        });
}

/* ---------------DELETE ACTIVITY SITE----------------*/
function siteDelete(id) {
    fireConfirm('Are you sure you want to delete this site?', function() {
        options = {
            url: API_END_POINT + '/sites/' + id,
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
                fireAlertSuccess('Site deleted with success!', function() {
                    window.location.assign('sites_list.php');
                });
            }).fail(function() {
                fireAlertError('Error in deleting site!', function() {
                    window.location.assign('sites_list.php');
                });
            });
    })
}