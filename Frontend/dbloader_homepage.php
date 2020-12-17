<!DOCTYPE html>
<html lang="en">

<head>
    <title>DB Loader - Homepage</title>

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
    <script src="static/js/access_management.js"></script>
    <script src="static/js/utilities.js"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        if (localStorage.getItem('token') === null || localStorage.getItem('role') != 'DBL')
            window.location.assign('not_authorized.html');
    </script>

</head>

<body>

    <div class="d-flex" id="wrapper">

        <?php include_once 'sidebar.html' ?>

        <script type="text/javascript">
            var links = {
                'Homepage': 'dbloader_homepage.php',
                'Typologies List': 'typologies_list.php',
                'Materials List': 'materials_list.php',
                'Procedures List': 'procedures_list.php',
                'Competences List': 'competences_list.php',
                'Sites List': 'sites_list.php'
            }

            populateSidebar(links);
        </script>

        <div class="container-fluid m-0 p-0">

            <?php include_once 'navbar.html' ?>

            <div class="container-fluid p-5">

                <div class="row">
                    <div class="col-4 mb-4">
                        <div class="card">
                            <img src="static/imgs/typologies.jpeg" class="card-img-top" alt="Typologies" style="height: 250px">
                            <div class="card-body bg-light text-center">
                                <h5 class="card-title">Typologies</h5>
                                <a href="typologies_list.php" class="btn btn-primary stretched-link">Go to Typologies</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mb-4">
                        <div class="card">
                            <img src="static/imgs/materials.jpeg" class="card-img-top" alt="Materials" style="height: 250px">
                            <div class="card-body bg-light text-center">
                                <h5 class="card-title">Materials</h5>
                                <a href="materials_list.php" class="btn btn-primary stretched-link">Go to Materials</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mb-4">
                        <div class="card">
                            <img src="static/imgs/procedures.jpeg" class="card-img-top" alt="Procedures" style="height: 250px">
                            <div class="card-body bg-light text-center">
                                <h5 class="card-title">Procedures</h5>
                                <a href="procedures_list.php" class="btn btn-primary stretched-link">Go to Procedures</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mb-4">
                        <div class="card">
                            <img src="static/imgs/competences.jpeg" class="card-img-top" alt="Competences" style="height: 250px">
                            <div class="card-body bg-light text-center">
                                <h5 class="card-title">Competences</h5>
                                <a href="competences_list.php" class="btn btn-primary stretched-link">Go to Competences</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 mb-4">
                        <div class="card">
                            <img src="static/imgs/sities.jpeg" class="card-img-top" alt="Sities" style="height: 250px">
                            <div class="card-body bg-light text-center">
                                <h5 class="card-title">Sites</h5>
                                <a href="sites_list.php" class="btn btn-primary stretched-link">Go to Sites</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>

</html>