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

function retrievePage(role) {
    if (role == 'ADM')
        return 'users_list.php';
    else if (role == 'PLN')
        return 'activities_list.php';
    else if (role == 'MNT')
        return 'maintainer.php';
    else if (role == 'DBL')
        return 'dbloader_homepage.php';
}

function populateSidebar(data) {
    var links_container = $('#sidebar-links');
    var link_row = $("#sidebar-link-row").html();

    $.each(data, function(k, v) {
        let row = link_row;
        row = row.replace(/{url}/ig, v);
        row = row.replace(/{page}/ig, k);
        links_container.append(row);
    });
}

function showCompetences(select) {
    var competences = $('#competences-wrapper');
    if (select.value == 'MNT')
        competences.attr("hidden", false);
    else
        competences.attr("hidden", true);
}

function currentDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    return mm + '/' + dd + '/' + yyyy;
}

function getWeekInterval(date) {
    var date = new Date(date);
    var firstday = new Date(date.setDate(date.getDate() - ((6 + date.getDay()) % 7))).toISOString().split("T")[0];
    var lastday = new Date(date.setDate(date.getDate() + 6)).toISOString().split("T")[0];
    var dayNum = date.getUTCDay() || 7;
    date.setUTCDate(date.getUTCDate() + 4 - dayNum);
    var yearStart = new Date(Date.UTC(date.getUTCFullYear(), 0, 1));
    week = Math.ceil((((date - yearStart) / 86400000) + 1) / 7);
    return week;
}

function getIntervalFromWeek(w) {
    var y = (new Date(Date.now())).getFullYear()
    var simple = new Date(y, 0, 1 + (w - 1) * 7);
    var dow = simple.getDay();
    var weekStart = simple;

    if (dow <= 4)
        weekStart.setDate(simple.getDate() - simple.getDay() + 2);
    else
        weekStart.setDate(simple.getDate() + 9 - simple.getDay());

    var strWeekStart = weekStart.toISOString().split("T")[0];
    weekStart.setDate(weekStart.getDate() + 6)
    var strWeekEnd = weekStart.toISOString().split("T")[0];
    return [strWeekStart, strWeekEnd];
}

function activityWeekFromTo(value) {
    console.log('inside')
    var ret = getIntervalFromWeek(value);
    $('#week-from').html(ret[0]);
    $('#week-to').html(ret[1]);
}

function minutesToTime(minutes) {
    let h = Math.floor(minutes / 60);
    let m = minutes % 60;
    h = h < 10 ? '0' + h : h;
    m = m < 10 ? '0' + m : m;
    return h + ':' + m;
}