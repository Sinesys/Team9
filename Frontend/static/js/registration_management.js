function validateRegistration(form) {
    var condition = true;
    $('#' + form + ' input:not([type="checkbox"])').each(function() {
        if ($(this).val() == "") {
            alert('The fields cannot be blank');
            condition = false;
            return false;
        }
    })

    if (!condition)
        return;

    if (!isRegexMatch($('#name')[0], /^[A-Za-z]+$/) || !isRegexMatch($('#surname')[0], /^[A-Za-z\']+$/) || !isRegexMatch($('#userid')[0], /^([0-9]|[a-zA-Z]|_){1,20}$/) || !isRegexMatch($('#phonenumber')[0], /^[0-9]+$/)) {
        alert('One or more fields are not valid!');
        return;
    }

    sendRegistrationJSON(form);
}

function sendRegistrationJSON(form) {
    var json = {};
    $('#' + form + ' input:not([type="checkbox"]), #role').each(function() {
        json[$(this).attr('name')] = $(this).val();
    });

    json['competences'] = [];
    var competence = $('#competences input:checked').each(function(index) {
        json['competences'][index] = $(this).val();
    });

    var options = {
        url: API_END_POINT + '/users',
        type: 'POST',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(json)
    };

    $.ajax(options).done(registrationSuccess).fail(registrationFailure);
}

function registrationSuccess() {
    alert('User registered with success!');
    window.location.assign("users_list.php");
}

function registrationFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert('Impossibile to register the user!');
    location.reload();
}