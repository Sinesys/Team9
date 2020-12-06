<!DOCTYPE html>
<html lang="en">

<head>
    <title>Users - Acces Log</title>

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

    <!-- DATATABLE -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css"></style>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

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
                    <div class="card-header d-flex justify-content-between">
                        <span class="h2">Users Log</span>
                    </div>
                    <div class="card-body bg-light p-5">

                        <div class="table-responsive-xl">
                            <table class="table table-bordered table-striped" id="users-log-table-master">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Access Time</th>
                                    </tr>
                                </thead>
                                <tbody id="users-log-table">
                                    <template id="log-row">
                                        <tr>
                                            <td>{userid}</td>
                                            <td>{accesstime}</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
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


        var options = {
            url: API_END_POINT + '/accesslog',
            type: 'GET',
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        }
        
        $.ajax(options).done(usersAccessLogSuccess).fail(usersAccessLogFailure);

    </script>   

</body>



</html>