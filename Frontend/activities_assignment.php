<!DOCTYPE html>
<html lang="en">

<head>
    <title>Activities - Assignment</title>

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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    </style>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

    <!-- FONT AWESOME ICONS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <!-- SWEET ALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4.0.1/bootstrap-4.min.css">

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/utilities.js"></script>
    <script src="static/js/fire_alert.js"></script>
    <script src="static/js/assignment.js"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <!-- RANGE SLIDER -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>

    <script type="text/javascript">
        if (localStorage.getItem('token') === null || localStorage.getItem('role') != 'PLN')
            window.location.assign('not_authorized.html');
    </script>

</head>

<body>

    <div class="d-flex" id="wrapper">

        <?php include_once 'sidebar.html' ?>
        
        <div class="container-fluid m-0 p-0">

            <?php include_once 'navbar.html' ?>

            <div class="container-fluid p-5">

                <div class="mx-auto card" style="width:90%">
                    <div class="card-header d-flex justify-content-between">
                        <span class="h2">Maintainers</span>
                        <span class="h3" id="activity-id"></sapn>
                    </div>
                    <div class="card-body bg-light p-5">

                        <div class="table-responsive-xl">
                            <table class="table table-bordered" id="manitainer-table-master">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle; width:20%">Maintainer</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Skills</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Avail. Monday</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Avail. Tuesday</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Avail. Wednesday</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Avail. Thursday</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Avail. Friday</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Avail. Saturday</th>
                                        <th class="text-center" style="vertical-align: middle; width:10%">Avail. Sunday</th>
                                    </tr>
                                </thead>
                                <tbody id="maintainer-table"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <template id="maintainer-avaibility-row">
        <tr>
            <td class="text-center" style="vertical-align: middle;">{maintainer}</td>
            <td class="text-center" style="vertical-align: middle;">
                <button class="btn btn-primary" data-toggle="modal" data-target="#skill-info" name="{userid}">{skills}</button>
            </td>
            <td class="text-center clickable-cell position-relative" style="vertical-align: middle; background-color: {monday_color}">
                <a class="stretched-link" data-toggle="modal" data-target="#day-info" name="{userid}" id="cell-monday">{monday}</a>
            </td>
            <td class="text-center clickable-cell position-relative" style="vertical-align: middle; background-color: {tuesday_color}">
                <a class="stretched-link" data-toggle="modal" data-target="#day-info" name="{userid}" id="cell-tuesday">{tuesday}</a>
            </td>
            <td class="text-center clickable-cell position-relative" style="vertical-align: middle; background-color: {wednesday_color}">
                <a class="stretched-link" data-toggle="modal" data-target="#day-info" name="{userid}" id="cell-wednesday">{wednesday}</a>
            </td>
            <td class="text-center clickable-cell position-relative" style="vertical-align: middle; background-color: {thursday_color}">
                <a class="stretched-link" data-toggle="modal" data-target="#day-info" name="{userid}" id="cell-thursday">{thursday}</a>
            </td>
            <td class="text-center clickable-cell position-relative" style="vertical-align: middle; background-color: {friday_color}">
                <a class="stretched-link" data-toggle="modal" data-target="#day-info" name="{userid}" id="cell-friday">{friday}</a>
            </td>
            <td class="text-center clickable-cell position-relative" style="vertical-align: middle; background-color: {saturday_color}">
                <a class="stretched-link" data-toggle="modal" data-target="#day-info" name="{userid}" id="cell-saturday">{saturday}</a>
            </td>
            <td class="text-center clickable-cell position-relative" style="vertical-align: middle; background-color: {sunday_color}">
                <a class="stretched-link" data-toggle="modal" data-target="#day-info" name="{userid}" id="cell-sunday">{sunday}</a>
            </td>
        </tr>
    </template>

    <?php include_once 'activity_skill_info_modal.html'; ?>
    <?php include_once 'activity_assignment_modal.html'; ?>

    <script type="text/javascript" src="static/js/activities_assignment.js"></script>

</body>

</html>