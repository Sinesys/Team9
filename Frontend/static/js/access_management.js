/* ---------------LOGIN REQUEST----------------*/
function login() {
    var username = $('#id');
    var password = $('#password');
    if (!username.val().match(/^([0-9]|[a-zA-Z]|_){1,20}$/) || password.val() == '') {
        fireAlertError('Invalid fields!');
        return;
    }

    var json = {};
    json[username.attr('name')] = username.val();
    json[password.attr('name')] = password.val();

    var options = {
        url: API_END_POINT + '/login',
        type: 'POST',
        async: false,
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json),
    };

    $.ajax(options)
        .done(function(data) {
            localStorage.setItem('token', data.auth_token);
            localStorage.setItem('role', data.role);
            var user = $('#id').val();
            localStorage.setItem('user', user);

            var json = {
                'token': data.auth_token,
                'role': data.role,
                'user': user
            };

            window.location.assign(retrievePage(data.role));
        })
        .fail(function(response) {
            if (response.status == 403)
                fireAlertError('The credentials are not correct!');
            else
                fireAlertError('Server error. Retry later!', function() {
                    window.location.assign('index.html');
                });
        });
}

/* ---------------LOGOUT----------------*/
function logout() {
    localStorage.clear();
    sessionStorage.clear();
    window.location.assign('index.html');
}