<!DOCTYPE html>
<html lang="en">

<head>
    <title>Typologies List</title>

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css"></style>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

    <!-- SWEET ALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> 
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4.0.1/bootstrap-4.min.css">

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/fire_alert.js"></script>
    <script src="static/js/typologies.js"></script>
    <script src="static/js/utilities.js"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        if(localStorage.getItem('token') === null || localStorage.getItem('role') != 'DBL')
            window.location.assign('not_authorized.html');
    </script>

</head>

<body>

    <div class="d-flex" id="wrapper">

        <?php include_once 'sidebar.html' ?>

        <div class="container-fluid m-0 p-0">

            <?php include_once 'navbar.html' ?>

            <div class="container-fluid p-5">

                <div class="card mx-auto">
                    <div class="card-header d-flex justify-content-between">
                        <span class="h2">Typologies</span>
                        <button type="button" name="add-typology" id="insert-tipology" class="btn btn-primary" data-toggle="modal" data-target="#typologies-modal">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                    <div class="card-body bg-light p-5">

                        <div class="table-responsive-xl">
                            <table class="table table-bordered" id="typologies-table-master">
                                <thead>
                                    <tr>
                                        <th style="text-align:center; vertical-align: middle;"></th>
                                        <th style="text-align:center; vertical-align: middle;">ID</th>
                                        <th style="text-align:center; vertical-align: middle;">Typology</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="typologies-table"></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <?php include_once 'typologies_management_modal.html' ?>

    <template id="typology-information-row">
        <tr>
            <td style="vertical-align: middle; white-space: nowrap; width: 1%;">
                <button type="button" name="{id}" id="modify-{id}" class="btn btn-primary mr-3" data-toggle="modal" data-target="#typologies-modal">
                    <i class="far fa-edit"></i>
                </button>
            </td>
            <td style="text-align:center; vertical-align: middle;">{id}</td> 
            <td style="text-align:center; vertical-align: middle;">{typology}</td>                                        
            <td style="vertical-align: middle; white-space: nowrap;width: 1%;">
                <button type="button" class="btn btn-danger" onclick="typologyDelete('{id}')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>                                        
        </tr>
    </template>  

    <script type="text/javascript" src="static/js/typologies_list.js"></script>

</body>

</html>