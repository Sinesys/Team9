<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin - Users List</title>

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

    <!-- DATATABLE -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    </style>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

    <!-- SWEET ALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4.0.1/bootstrap-4.min.css">

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/utilities.js"></script>
    <script src="static/js/users.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/fire_alert.js"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        if (localStorage.getItem('token') === null || localStorage.getItem('role') != 'ADM')
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
                    <div class="card-header d-flex justify-content-between">
                        <span class="h2">Users</span>
                        <button type="button" class="btn btn-primary" onclick="window.location.assign('users_management.php');">
                            <i class="fas fa-user-plus"></i> 
                            Add
                        </button>
                    </div>
                    <div class="card-body bg-light p-5">

                        <div class="table-responsive-xl">
                            <table class="table table-bordered table-striped" id="users-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Role</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Surname</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Phone Number</th>
                                        <th class="text-center">Birth Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="users-table-tbody"></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <template id="user-row-template">
        <tr>
            <td style="white-space: nowrap;width: 1%;">
                <button type="button" class="btn btn-primary" onclick="window.location.assign('users_management.php?update=true&id={userid}')">
                    <i class="far fa-edit"></i>
                </button>
            </td>
            <td>{userid}</td>
            <td>{role}</td>
            <td>{name}</td>
            <td>{surname}</td>
            <td>{email}</td>
            <td>{phonenumber}</td>
            <td>{birthdate}</td>
            <td style="white-space: nowrap;width: 1%;">
                <button type="button" class="btn btn-danger" onclick="userDelete('{userid}')">
                    <i class="fas fa-user-times"></i>
                </button>
            </td> 
        </tr>
    </template>

    <script type="text/javascript" src="static/js/users_list.js"></script>

</body>

</html>