function validateBirthDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    var max = String(parseInt(yyyy) - 18) + '-' + mm + '-' + dd;
    var min = String(parseInt(yyyy) - 100) + '-' + mm + '-' + dd;

    var birthDate = $('#birthdate')
    birthDate.attr('min', min);
    birthDate.attr('max', max);
    birthDate.attr('value', max);
}

function validateScheduledActivityDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    var date = $('#scheduledweek');
    date.attr('min', today);
    date.attr('value', today);
}

function isRegexMatch(field, regex) {
    if (!field.value.match(regex) && field.value != '') {
        if (!field.className.includes('error'))
            field.className += ' error';
        return false;
    } else {
        field.classList.remove('error');
        return true;
    }
}