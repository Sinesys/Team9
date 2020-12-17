/* ---------------VALIDATE ACTIVITY MATERIAL FORM----------------*/
function materialValidate() {
    var condition = true;
    $('#material-form input').each(function() {
        if ($(this).val() == "") {
            fireAlertError('The fields cannot be blank!');
            condition = false;
            return false;
        }
    })

    if (!condition)
        return false;

    if (!$('#materialid').val().match(/^([0-9]|[a-zA-Z]|_){1,20}$/)) {
        fireAlertError('Material ID is not valid!');
        return false;
    }
    return true;
}

/* ---------------INSERT ACTIVITY MATERIAL---------------*/
function materialInsert() {

    if (!materialValidate())
        return;

    var json = {};
    $('#material-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/materials',
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
            fireAlertSuccess('Material inserted with success!', function() {
                window.location.assign('materials_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Error in inserting material!', function() {
                window.location.assign('materials_list.php');
            });
        });
}

/* ---------------UPDATE ACTIVITY MATERIAL----------------*/
function materialUpdate() {

    if (!materialValidate())
        return;

    var json = {};
    $('#material-form input').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/materials/' + json.materialid,
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
            fireAlertSuccess('Material updated with success!', function() {
                window.location.assign('materials_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Error in updating material info!', function() {
                window.location.reload();
            });
        });

}

/* ---------------DELETE ACTIVITY MATERIAL----------------*/
function materialDelete(material) {
    fireConfirm('Are you sure you want to delete this material?', function() {
        options = {
            url: API_END_POINT + '/materials/' + material,
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
                fireAlertSuccess('Material deleted with success!', function() {
                    window.location.assign('materials_list.php');
                });
            })
            .fail(function() {
                fireAlertError('Error in deleting material!', function() {
                    window.location.assign('materials_list.php');
                });
            });
    })
}