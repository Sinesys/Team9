function competencesSuccess(data) {
    var competences = $('#competences');
    var competence_row = $("#competence-row").html();

    $.each(data, function(index, obj) {
        let row = competence_row;
        row = row.replace(/{index}/ig, index);
        row = row.replace(/{competence}/ig, obj);
        competences.append(row);
    });
}

function competencesFailure(data) {
    if (data.status == 401) {
        alert('The session has expired, you need to login again!');
        logoutUser();
    }
    alert("Impossible to load competences for the maintainers!");
    window.location.assign("users_list.php");
}

function showCompetences(select) {
    var competences = $('#competences-wrapper');
    if (select.value == 'MNT')
        competences.attr("hidden", false);
    else
        competences.attr("hidden", true);
}