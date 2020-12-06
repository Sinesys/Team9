function validateLogin(field1, field2) {
    var input1 = $('#' + field1);
    var input2 = $('#' + field2);
    if (input1.val() == '' || input2.val() == '') {
        alert('The fields cannot be blank');
        return;
    }

    sendLoginJSON(input1, input2);
}

function sendLoginJSON(input1, input2) {
    var json = {};
    json[input1.attr('name')] = input1.val();
    json[input2.attr('name')] = input2.val();

    var options = {
        url: API_END_POINT + '/login',
        type: 'POST',
        async: false,
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json)
    };

    $.ajax(options).done(loginSuccess).fail(loginFailure);
}

function loginSuccess(data) {
    localStorage.setItem('token', data.auth_token);
    localStorage.setItem('role', data.role);
    var user = $('#id').val();
    localStorage.setItem('user', user);

    var json = {
        'token': data.auth_token,
        'role': data.role,
        'user': user
    };

    if (data.role == 'ADM')
        window.location.assign('users_list.php');
    else if (data.role == 'PLN')
        window.location.assign('activities_list.php');
    else if (data.role == 'MNT')
        window.location.assign('maintainer.php');

}

function loginFailure() {
    alert('Impossible to login!');
    window.location.assign('index.html');
}

function logoutUser() {
    localStorage.clear();
    window.location.assign('index.html');
}