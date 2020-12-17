/* ---------------VALIDATE USER FORM----------------*/
function userValidate(update) {
    var condition = true;

    if (update)
        var inputs = $('#registration-form input:not([type="checkbox"], [name=password])');
    else
        var inputs = $('#registration-form input:not([type="checkbox"])');

    inputs.each(function() {
        if ($(this).val() == "") {
            fireAlertError('The fields cannot be blank!');
            condition = false;
            return false;
        }
    })

    if (!condition)
        return false;

    if (!isRegexMatch($('#name')[0], /^[A-Za-z]{1,20}$/) || !isRegexMatch($('#surname')[0], /^[A-Za-z]{1,30}$/) || !isRegexMatch($('#userid')[0], /^([0-9]|[a-zA-Z]|_){1,20}$/) || !isRegexMatch($('#phonenumber')[0], /^[0-9]{1,15}$/)) {
        fireAlertError('One or more fields are not valid!');
        return false;
    }

    return true;
}

/* ---------------CREATE JSON WITH USER INFOS----------------*/
function userInfo() {
    var json = {};
    $('#registration-form input:not([type="checkbox"]), #role').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    json['competences'] = [];
    var competence = $('#competences input:checked').each(function(index) {
        json['competences'][index] = $(this).val();
    });

    return json;
}
/* ---------------REGISTER USER----------------*/
function userRegister() {

    if (!userValidate(false))
        return;

    var options = {
        url: API_END_POINT + '/users',
        type: 'POST',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(userInfo()),
        statusCode: {
            401: function() {
                fireAlertError('The session has expired, you need to login again!', logout);
            }
        }
    };

    $.ajax(options)
        .done(function() {
            fireAlertSuccess('User registered with success!', function() {
                window.location.assign("users_list.php");
            });
        })
        .fail(function() {
            fireAlertError('Impossibile to register the user!');
        });
}

/* ---------------UPDATE USER----------------*/
function userUpdate() {

    if (!userValidate(true))
        return;

    var info = userInfo();

    var options = {
        url: API_END_POINT + '/users/' + info.userid,
        type: 'PUT',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(info),
        statusCode: {
            401: function() {
                fireAlertError('The session has expired, you need to login again!', logout);
            }
        }
    };

    $.ajax(options)
        .done(function() {
            fireAlertSuccess('User updated with success!', function() {
                window.location.assign('users_list.php');
            });
        })
        .fail(function() {
            fireAlertError('Error in updating user info!');
        });
}

/* ---------------DELETE USER----------------*/
function userDelete(id) {
    var ret = fireConfirm('Are you sure you want to delete this user?', function() {
        var options = {
            url: API_END_POINT + '/users/' + id,
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

        $.ajax(options).done(function() {
            fireAlertSuccess('User deleted with success!', function() {
                window.location.assign('users_list.php');
            });
        }).fail(function() {
            fireAlertError('Error in deleting user!', function() {
                window.location.assign("users_list.php");
            });
        });
    });
}