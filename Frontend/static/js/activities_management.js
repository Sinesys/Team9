/* -----------POPULATE THE SIDEBAR--------------
-----------according to the user role-----------*/
var links = {
    'Activities List': 'activities_list.php',
    'Activities Insert': 'activities_management.php',
}

populateSidebar(links);

var options = {
    url: API_END_POINT + '/materials',
    type: 'GET',
    async: false,
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

/* ------------------GET MATERIALS--------------------
-----in order to show them in the activity form-------*/
$.ajax(options)
    .done(function(data) {
        var select = $('#materials');
        var material_row = $('#activity-material-row').html();

        $.each(data, function(index, obj) {
            let row = material_row;
            row = row.replace(/{materialid}/ig, obj.materialid);
            row = row.replace(/{material}/ig, obj.name);
            select.append(row);
        });

    })
    .fail(function() {
        fireAlertError('Error in loading materials!');
    });

$('#materials').selectpicker();
$('button[data-id=materials]').css('background-color', 'white');
$('.dropdown.bootstrap-select.show-tick').css('flex', '1 1 auto').css('border', '1px solid #ced4da').css('border-radius', '.25rem');

var options = {
    url: API_END_POINT + '/procedures?verbose=true',
    type: 'GET',
    async: false,
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

/* ------------------GET PROCEDURES--------------------
-----in order to show them in the activity form-------*/
$.ajax(options)
    .done(function(data) {
        var select = $('#procedure');
        var procedure_row = $('#activity-procedure-row').html();

        var procedureDict = {};

        $.each(data, function(index, obj) {
            let row = procedure_row;
            row = row.replace(/{procedureid}/ig, obj.procedureid);
            select.append(row);

            let procedureInfo = {};
            procedureInfo['description'] = obj.description;
            procedureInfo['competencesrequired'] = {};
            $.each(obj.competencesrequired, function(index, competence) {
                procedureInfo['competencesrequired'][competence.competenceid] = competence.name;
            });
            procedureDict[obj.procedureid] = procedureInfo;
        });

        sessionStorage.setItem('procedureDict', JSON.stringify(procedureDict));
    })
    .fail(function() {
        fireAlertError('Error in loading procedures!');
    });

$('#activity-procedure-modal').on('hide.bs.modal', function(e) {
    $('#activity-procedure-competences').html('');
});

/* ------------------GET SITES--------------------
-----in order to show them in the activity form-------*/
var options = {
    url: API_END_POINT + '/sites',
    type: 'GET',
    async: false,
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

$.ajax(options)
    .done(function(data) {
        var select = $('#site');
        var material_row = $('#activity-site-row').html();

        $.each(data, function(index, obj) {
            let row = material_row;
            row = row.replace(/{siteid}/ig, obj.siteid);
            row = row.replace(/{site}/ig, obj.area + ' - ' + obj.department);
            select.append(row);
        });
    })
    .fail(function() {
        fireAlertError('Error in loading sites!');
    });

/* ------------GET ACTIVITY TYPOLOGIES----------------
-----in order to show them in the activity form-------*/
var options = {
    url: API_END_POINT + '/activitytypologies',
    type: 'GET',
    async: false,
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

$.ajax(options)
    .done(function(data) {
        var select = $('#typology');
        var material_row = $('#activity-typology-row').html();

        $.each(data, function(index, obj) {
            let row = material_row;
            row = row.replace(/{typologyid}/ig, obj.typologyid);
            row = row.replace(/{typology}/ig, obj.description);
            select.append(row);
        });
    })
    .fail(function() {
        fireAlertError('Error in loading typologies!');
    });

/* ------------------MANAGE THE WEEK------------------------
-----in order to show it correctly in the activity form-----*/
var weekNumber = getWeekInterval(currentDate());
$('#current-week').html(weekNumber);
$('#scheduledweek').val(weekNumber);
activityWeekFromTo(weekNumber);

/* ------------------MANAGE THE DATE------------------------
-----in order to show it correctly in the activity form-----*/
var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0');
var yyyy = today.getFullYear();

today = yyyy + '-' + mm + '-' + dd;

var date = $('#scheduledweek');
date.attr('min', getWeekInterval(today));
date.attr('value', getWeekInterval(today));

var url = new URL(window.location.href);
var update = url.searchParams.get("update");
var id = url.searchParams.get("id");

/* --------------REFILL THE ACTIVIY FORM-----------------
---in order to show the info and allow the modification---*/
if (update == 'true') {

    var options = {
        url: API_END_POINT + '/activities/' + id,
        type: 'GET',
        headers: {
            'Authorization': localStorage.getItem('token'),
            'Content-Type': 'application/json'
        },
        statusCode: {
            401: function() {
                fireAlertError('The session has expired, you need to login again!', logout);
            },
            400: function() {
                fireAlertError('Error in loading activity info');
            }
        }
    };

    $.ajax(options)
        .done(function(data) {
            var select = $('.filter-option-inner-inner').html('').css('color', 'black');

            $.each(data.materials, function(index, obj) {
                let name = $('#' + obj).html();
                $('#' + obj).attr('selected', true);
                select.append(name + ', ')
            });
            delete data.materials;

            var interruptible = $('#interruptible');
            interruptible[0].selectedIndex = $('#' + data.interruptible).index();
            delete data.interruptible;

            var procedure = $('#procedure');
            procedure[0].selectedIndex = $('#' + data.procedure).index();
            delete data.procedure;

            var site = $('#site');
            site[0].selectedIndex = $('#' + data.site).index();
            delete data.site;

            var typology = $('#typology');
            typology[0].selectedIndex = $('#' + data.typology).index();
            delete data.typology;

            $.each(data, function(k, v) {
                $("#" + k).val(v)
            })
        })
        .fail(function() {
            fireAlertError('Server error. Retry later!', function() {
                window.location.assign('index.html');
            });
        });

    $('#activityid').attr('readonly', true);
    $('.card-header span[class="h2"]').html('Update Activity');
    $('#send-update-button').attr('onclick', 'activityUpdate()').html('Update');
}