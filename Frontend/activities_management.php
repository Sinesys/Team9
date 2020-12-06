<!DOCTYPE html>
<html lang="en">

<head>
    <title>Activity - Management</title>

    <!-- METADATA -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ICON -->
    <link href="static/imgs/icon.png" rel="icon">

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- FONT AWESOME ICONS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/registration_management.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/users_management.js"></script>
    <script src="static/js/activities_management.js"></script>
    <script src="static/js/competences_management.js"></script>
    <script src="static/js/functionalities.js"></script>
    <script src="static/js/validations.js"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        if(localStorage.getItem('token') === null){
            alert('You are not authenticated, please login');
            window.location.assign('index.html');
        }
        else if(localStorage.getItem('role') != 'PLN')
            window.location.assign('not_authorized.html');
    </script>

</head>

<body>

    <div class="d-flex" id="wrapper">

        <?php include_once 'sidebar.html' ?>

        <script type="text/javascript">
            var links = {
                    'Activities List' : 'activities_list.php',
                    'Activities Insert' : 'activities_management.php',
            }
                    
            populateSidebar(links);
            
        </script>

        <div class="container-fluid m-0 p-0">

            <?php include_once 'navbar.php' ?>

            <div class="container-fluid p-5">

                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span class="h2">Insert Activity</span>
                        <div class="h5 my-auto">
                            Current week:
                            <span id="current-week"></span>
                        </div>
                    </div>
                    <div class="card-body bg-light p-5">

                        <div id="activity-insert-form">

                            <label for="activityid">ID activity:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fab fa-slack-hash"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Insert univoc code" id="activityid" name="activityid" maxlength="20" onkeyup="isRegexMatch(this, /^([0-9]|[a-zA-Z]){1,20}$/)">
                            </div>

                            <label for="description">Description:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-font"></i></span>
                                </div>
                                <textarea type="text" class="form-control" rows="5" placeholder="Insert description" id="description" name="description"></textarea>
                            </div>

                            <label for="scheduledweek">Week:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                </div>
                                <input type="number" class="form-control" placeholder="Insert scheduled week" id="scheduledweek" name="scheduledweek" min="1" max="52" onkeyup="activityWeekFromTo(this.value)">
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <label>From: <label>
                                    <span id="week-from"></span>
                                </div>
                                <div class="col-4">
                                    <label>To: <label>
                                    <span id="week-to"></span>
                                </div>
                            </div>

                            <div>
                                
                            </div>
                            
                            <div class="input-group mt-5 d-flex justify-content-center">
                                <button type="button" class="btn btn-danger mr-3" onclick="window.location.assign('activities_list.php')">Cancel</button>
                                <button type="button" id="send-update-button" class="btn btn-primary" onclick="validateInsertActivity('activity-insert-form')">Add</button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        $("#sidebar-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });

        var weekNumber = getWeekInterval(currentDate());
        $('#current-week').html(weekNumber);
        $('#scheduledweek').val(weekNumber);
        activityWeekFromTo(weekNumber);

        validateScheduledActivityDate();

        var url = new URL(window.location.href);
        var update = url.searchParams.get("update");
        var id = url.searchParams.get("id");

        if (update == 'true') {

            var options = {
                url: API_END_POINT + '/activities/' + id,
                type: 'GET',
                headers: {
                    'Authorization': localStorage.getItem('token')
                }
            }
    
            $.ajax(options).done(activityInfoSuccess).fail(activityInfoFailure);

            $('#activityid').attr('readonly', true);
            $('.card-header span[class="h2"]').html('Update Activity');
            $('#send-update-button').attr('onclick', 'updateActivity("activity-insert-form")').html('Update');
        }

    </script>

</body>

</html>