 <!DOCTYPE html>
<html lang="en">

<head>
    <title>Users - Management</title>

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

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/registration_management.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/users_management.js"></script>
    <script src="static/js/activities_management.js"></script>
    <script src="static/js/competences_management.js"></script>
    <script src="static/js/functionalities.js"></script>
    <script src="static/js/validations.js"></script>

    <!-- FONT AWESOME ICONS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        if(localStorage.getItem('token') === null){
            alert('You are not authenticated, please login');
            window.location.assign('index.html');
        }
        else if(localStorage.getItem('role') != 'ADM')
            window.location.assign('not_authorized.html');
    </script>

</head>

<body>

    <div class="d-flex" id="wrapper">

        <?php include_once 'sidebar.html' ?>

        <script type="text/javascript">
            
            var links = {
                'Users List' : 'users_list.php',
                'User Registration' : 'users_management.php',
                'Users Access Log' : 'users_access_log.php'
            }
            
            populateSidebar(links);
            
        </script>

        <div class="container-fluid m-0 p-0">

            <?php include_once 'navbar.php' ?>

            <div class="container-fluid p-5">

                <div class="card">
                    <div class="card-header">
                        <span class="h2">Register</span>
                    </div>
                    <div class="card-body bg-light p-5">

                        <div id="registration-form">

                            <label for="userid">Code:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fab fa-slack-hash"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Insert univoc code" id="userid" name="userid" maxlength="20" onkeyup="isRegexMatch(this, /^([0-9]|[a-zA-Z]|_){1,20}$/)">
                            </div>

                            <label for="password">Password:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                                </div>
                                <input type="password" class="form-control" placeholder="Insert password" id="password" name="password" maxlength="50">
                            </div>

                            <label for="name">Name:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Insert name" id="name" name="name" maxlength="20" onkeyup="isRegexMatch(this, /^[A-Za-z]+$/)">
                            </div>

                            <label for="surname">Surname:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Insert surname" id="surname" name="surname" maxlength="30" onkeyup="isRegexMatch(this, /^[A-Za-z\']+$/)">
                            </div>

                            <label for="birthdate">Birth Date:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                </div>
                                <input type="date" class="form-control" placeholder="Insert birth date:" id="birthdate" name="birthdate">
                            </div>

                            <label for="phonenumber">Phone Number:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Insert phone number" id="phonenumber" name="phonenumber" maxlength="15" onkeyup="isRegexMatch(this, /^[0-9]+$/)">
                            </div>

                            <label for="email">Email: </label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                                </div>
                                <input type="email" class="form-control" placeholder="Insert email" id="email" name="email">
                            </div>

                            <label for="role">Role:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-wrench"></i></span>
                                </div>
                                <select id="role" name="role" class="custom-select" onchange="showCompetences(this)">
                                    <option id="MNT" value="MNT">Maintainer</option>
                                    <option id="PLN" value="PLN">Planner</option>
                                    <option id="ADM" value="ADM">Admin</option>
                                    <option id="DBL" value="DBL">DB Loader</option>
                                </select>
                            </div>

                            <div id="competences-wrapper">

                                <label for="competences">Competences: </label>
                                <div class="input-group mb-3" id="competences">
                                    <template id="competence-row">
                                        <div class="custom-control custom-checkbox mb-3 mr-4">
                                            <input type="checkbox" class="custom-control-input" id="competence{index}" name="competence{index}" value="{competence}">
                                            <label class="custom-control-label" for="competence{index}">{competence}</label>
                                        </div>
                                    </template>
                                </div>                                                     

                            </div>

                            <div class="input-group mt-5 d-flex justify-content-center">
                                <button type="button" class="btn btn-danger mr-3" onclick="window.location.assign('users_list.php')">Cancel</button>
                                <button type="button" id="send-update-button" class="btn btn-primary" onclick="validateRegistration('registration-form')">Register</button>
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

        validateBirthDate();

        var options = {
            url: API_END_POINT + '/competences',
            type: 'GET',
            async: false,
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }
        $.ajax(options).done(competencesSuccess).fail(competencesFailure);

        var url = new URL(window.location.href);
        var update = url.searchParams.get("update");
        var id = url.searchParams.get("id");

        if (update == 'true') {

            var options = {
                url: API_END_POINT + '/users/' + id,
                type: 'GET',
                headers: {
                    'Authorization': localStorage.getItem('token')
                }
            }
    
            $.ajax(options).done(userInfoSuccess).fail(userInfoFailure);
            $('#userid').attr('readonly', true);
            $('#password').attr('placeholder', 'Leave blank if you don\'t want to change the password');
            $('#role').attr('disabled', true);
            $('.card-header span[class="h2"]').html('Update User');
            $('#send-update-button').attr('onclick', 'updateUser("registration-form")').html('Update');
        }

    </script>

</body>

</html>