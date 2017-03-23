<?php
include_once "class\Log.class.php";
include_once "conf\database.php";
include_once "class\detentionTimeCalc.php";

use detention\DetentionTimeCalc;

$offenceTypeArray = DetentionTimeCalc::toOffenceTypeArray();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Mankeshwar : Detention Time Calculator</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <form class="form-horizontal" role="form" id="dtForm">
                <h2>Detention Time Calculator</h2>
                <div class="form-group">
                    <label for="firstName" class="col-sm-3 control-label">Student Name</label>
                    <div class="col-sm-9">
                        <input type="text" id="studentName" name="studentName" placeholder="Student Name" class="form-control" value="" autofocus>

                    </div>
                </div>
                <div class="form-group">
                    <label for="country" class="col-sm-3 control-label">Offence Types</label>
                    <div class="col-sm-9">
                        <select id="offenseTypes" name="offenseTypes" class="form-control" multiple="multiple">
                            <?php
                            if (!empty($offenceTypeArray)) {
                                foreach ($offenceTypeArray as $key => $value) {
                                    echo "<option value={$key}>{$value}</option>";
                                }
                            }
                            //}
                            ?>
                        </select>
                    </div>
                </div> 

                <div class="form-group">
                    <label class="control-label col-sm-3">Good Time/Bad Time</label>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" id="goodTime" name="timeMode" value="goodTime">Good Time
                                </label> 
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" id="badTime" name="timeMode" value="badTime">Bad Time
                                </label>
                            </div>

                        </div>
                    </div>
                </div> 
                <div class="form-group">
                    <label class="control-label col-sm-3">Calculation mode (Concurrent/Consecutive)</label>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" id="clcMode" name="clcMode" value="concurrent">Concurrent
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="radio-inline">
                                    <input type="radio" id="clcMode" name="clcMode" value="Consecutive">Consecutive
                                </label>
                            </div>

                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="button" id="submit" class="btn btn-primary btn-block">Calculate</button>
                    </div>
                </div>
            </form> 
            <div id="resultPanel" class="alert alert-info ">

            </div>

        </div> <!-- ./container -->
        <script src="js/script.js"></script>
    </body>
</html>