<!DOCTYPE html>
<html lang="en">

<head>
    <title>Planner - Activity Management</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

    <!-- FONT AWESOME ICONS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    
    <!-- SWEET ALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> 
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@4.0.1/bootstrap-4.min.css">

    <!-- CUSTOM JS -->
    <script src="static/js/global.js"></script>
    <script src="static/js/access_management.js"></script>
    <script src="static/js/activities.js"></script>
    <script src="static/js/fire_alert.js"></script>
    <script src="static/js/utilities.js"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="static/css/custom.css">
    <link href="static/css/simple-sidebar.css" rel="stylesheet">

    <script type="text/javascript">
        if(localStorage.getItem('token') === null || localStorage.getItem('role') != 'PLN')
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
                                <input type="text" class="form-control" placeholder="Insert univoc code" id="activityid" name="activityid" maxlength="20">
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
                                <input type="number" class="form-control" placeholder="Insert scheduled week" id="scheduledweek" name="scheduledweek" min="1" max="52" onchange="activityWeekFromTo(this.value)" onkeyup="activityWeekFromTo(this.value)">
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

                            <label for="estimatedtime">Estimated time (min):</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                </div>
                                <input type="number" class="form-control" placeholder="Insert estimated time" id="estimatedtime" name="estimatedtime" min="15" max="525" step="15" value="15">
                            </div>

                            <label for="interruptible">Interruptible</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                                </div>
                                <select id="interruptible" name="interruptible" class="custom-select">
                                    <option id="true" value="true">ON</option>
                                    <option id="false" value="false">OFF</option>
                                </select>
                            </div>

                            <label for="materials">Materials:</label>                
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-wrench"></i></span>
                                </div>
                                <select id="materials" name="materials" class="selectpicker" multiple data-live-search="true">                
                                </select>  
                                <template id="activity-material-row">
                                    <option id="{materialid}" value="{materialid}">{material}</option>
                                </template> 
                            </div>                               

                            <label for="procedure">Procedure:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clipboard-list"></i></span>
                                </div>
                                <select id="procedure" name="procedure" class="custom-select" onchange="viewActivityProcedureInfo(this.value)">
                                    <option value="-1">--- SELECT PROCEDURE ---</option>                                    
                                </select>
                                <template id="activity-procedure-row">
                                    <option id="{procedureid}" value="{procedureid}">{procedureid}</option>
                                </template> 
                            </div>

                            <label for="site">Site:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                </div>
                                <select id="site" name="site" class="custom-select">
                                    <option value="-1">--- SELECT SITE ---</option>                                    
                                </select>
                                <template id="activity-site-row">
                                    <option id="{siteid}" value="{siteid}">{site}</option>
                                </template> 
                            </div>

                            <label for="typology">Typology:</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-plug"></i></span>
                                </div>
                                <select id="typology" name="typology" class="custom-select">
                                    <option value="-1">--- SELECT TYPOLOGY ---</option>                                    
                                </select>
                                <template id="activity-typology-row">
                                    <option id="{typologyid}" value="{typologyid}">{typology}</option>
                                </template> 
                            </div>

                            <div>
                                
                            </div>
                            
                            <div class="input-group mt-5 d-flex justify-content-center">
                                <button type="button" class="btn btn-danger mr-3" onclick="window.location.assign('activities_list.php')">Cancel</button>
                                <button type="button" id="send-update-button" class="btn btn-primary" onclick="activityInsert('activity-insert-form')">Add</button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>    

    <?php include_once 'activities_procedures_modal.html' ?>

    <script type="text/javascript" src="static/js/activities_management.js"></script>

</body>

</html>