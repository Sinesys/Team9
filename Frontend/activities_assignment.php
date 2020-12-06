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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/registration_management.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/users_management.js"></script>
    <script src="static/js/activities_management.js"></script>
    <script src="static/js/competences_management.js"></script>
    <script src="static/js/functionalities.js"></script>
    <script src="static/js/validations.js"></script>
    <script src="static/js/assignment_management.js"></script>


    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <!-- RANGE SLIDER -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"></script>

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
            'Activities List': 'activities_list.php',
            'Activities Insert': 'activities_management.php',
        }

        populateSidebar(links);
        </script>

        <div class="container-fluid m-0 p-0">

            <?php include_once 'navbar.php' ?>

            <div class="container-fluid p-5">

                <div class="mx-auto card" style="width:90%">
                    <div class="card-header d-flex justify-content-between">
                        <span class="h2">Maintainers</span>
                    </div>
                    <div class="card-body bg-light p-5">

                        <div class="table-responsive-xl">
                            <table class="table table-bordered" id="manitainer-table-master">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:20%">Maintainer</th>
                                        <th class="text-center" style="width:10%">Skills</th>
                                        <th class="text-center" style="width:10%">Avail. Monday</th>
                                        <th class="text-center" style="width:10%">Avail. Tuesday</th>
                                        <th class="text-center" style="width:10%">Avail. Wednesday</th>
                                        <th class="text-center" style="width:10%">Avail. Thursday</th>
                                        <th class="text-center" style="width:10%">Avail. Friday</th>
                                        <th class="text-center" style="width:10%">Avail. Saturday</th>
                                        <th class="text-center" style="width:10%">Avail. Sunday</th>
                                    </tr>
                                </thead>
                                <tbody id="maintainer-table">
                                    <template id="maintainer-avaibility-row">
                                        <tr>
                                            <td class="text-center">{maintainer}</td>
                                            <td class="text-center"><button class="btn" data-toggle="modal" data-target="#skill-info"
                                                class="text-center"     rel="{userid}">{skills}</button></td>
                                            <td class="text-center"><button class="btn monday" data-toggle="modal" data-target="#day-info" rel="{userid}">{monday}</button></td>
                                            <td class="text-center"><button class="btn tuesday" data-toggle="modal" data-target="#day-info" rel="{userid}">{tuesday}</button></td>
                                            <td class="text-center"><button class="btn wednesday" data-toggle="modal" data-target="#day-info" rel="{userid}">{wednesday}</button></td>
                                            <td class="text-center"><button class="btn thursday" data-toggle="modal" data-target="#day-info" rel="{userid}">{thursday}</button></td>
                                            <td class="text-center"><button class="btn friday" data-toggle="modal" data-target="#day-info" rel="{userid}">{friday}</button></td>
                                            <td class="text-center"><button class="btn saturday" data-toggle="modal" data-target="#day-info" rel="{userid}">{saturday}</button></td>
                                            <td class="text-center"><button class="btn sunday" data-toggle="modal" data-target="#day-info" rel="{userid}">{sunday}</button></td>
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

    <?php include_once 'activity_skill_info_modal.html'; ?>
    <?php include_once 'activity_assignment_modal.html'; ?>


    <script type="text/javascript">    
        var options = {
            url: API_END_POINT + '/maintainers',
            type: 'GET',
            headers: {
                'Authorization': localStorage.getItem('token')
            }
        };

        $.ajax(options).done(maintainerAvaibilitySuccess).fail(maintainerAvaibilityFailure);

        var url = new URL(window.location.href);
        var skills = url.searchParams.get("skills[]").split(",");

        var container = $('#detail-skills');
        var skill_row = $("#skill-row").html();

        $.each(skills, function(index, obj) {
            let row = skill_row;
            row = row.replace(/{skill}/ig, obj);
            container.append(row);
        });
    </script>

</body>

</html>