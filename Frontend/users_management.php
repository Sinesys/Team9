 <!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin - Users Management</title>

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

    <!-- SWEET ALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> 
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4.0.1/bootstrap-4.min.css">

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/users.js"></script>
    <script src="static/js/utilities.js"></script>
    <script src="static/js/fire_alert.js"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        if(localStorage.getItem('token') === null || localStorage.getItem('role') != 'ADM')
            window.location.assign('not_authorized.html');
    </script>

</head>

<body>

    <div class="d-flex" id="wrapper">

        <?php include_once 'sidebar.html' ?>

        <div class="container-fluid m-0 p-0">

            <?php include_once 'navbar.html' ?>

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
                                <input type="text" class="form-control" placeholder="Insert name" id="name" name="name" maxlength="20" onkeyup="isRegexMatch(this, /^[A-Za-z]{1,20}$/)">
                            </div>

                            <label for="surname">Surname:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Insert surname" id="surname" name="surname" maxlength="30" onkeyup="isRegexMatch(this, /^[A-Za-z]{1,30}$/)">
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
                                <input type="text" class="form-control" placeholder="Insert phone number" id="phonenumber" name="phonenumber" maxlength="15" onkeyup="isRegexMatch(this, /^[0-9]{1,15}$/)">
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
                                <div class="input-group mb-3" id="competences"></div>                                                     

                            </div>

                            <div class="input-group mt-5 d-flex justify-content-center">
                                <button type="button" class="btn btn-danger mr-3" onclick="window.location.assign('users_list.php')">Cancel</button>
                                <button type="button" id="send-update-button" class="btn btn-primary" onclick="userRegister()">Register</button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="competence-row">
        <div class="custom-control custom-checkbox mb-3 mr-4">
            <input type="checkbox" class="custom-control-input" id="competence{index}" name="competence{index}" value="{competenceid}">
            <label class="custom-control-label" for="competence{index}">{competence}</label>
        </div>
    </template>

    <script type="text/javascript" src="static/js/users_management.js"></script>

</body>

</html>