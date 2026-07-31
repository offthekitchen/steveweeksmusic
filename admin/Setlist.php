<?php
/*
*******************************************************************
Setlist.php
This PHP file generates a setlist.
NOTES
Date        Change
-------------------------------------------------------------
2020-04-28	Created
2020-11-07	Added Popular Flag
2026-07-30	Migrated to new Datalayer PerformanceSongs repository
*******************************************************************
*/

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

//This include defines the relative path to the root directory from this sub-directory
include_once("root.inc.php");

//inlcude web site settings
include_once($ROOT . "/includes/websiteSettings.php");

//inlcude web site settings
include_once(SETTINGS_DIR . "/SteveWeeksMusicSettings.php");

//inlcude admin settings
include_once(ADMIN_DIR . "/includes/AdminSettings.php");

//inlcude Common Functions
include_once (ADMIN_INCLUDE_DIR . "/CommonFunctions.php");

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/PerformanceSongs.php");
include_once(DATALAYER_DIR . "/PerformanceSongsRepository.php");

//Require the Class for the calendar picker
require_once(CLASS_DIR . "/tc_calendar.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of PerformanceSongs records from the DB
$aPerformanceSongsRecords = [];

$sActiveMenuItem = PERFORMANCES_ACTIVE;	
$sPageName= "Setlist";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php
 	include (ADMIN_INCLUDE_DIR . "/HTMLHead.php");
?>

<body>

    <!-- DIV used for Image Preview Popup -->
    <div style="display: none; position: absolute; z-index: 110; left: 400; top: 100; width: 15; height: 15" id="preview_div"></div>

    <div class="container-fluid">
        <form name="Setist" action="Setlist.php" method="post">

            <?php

            //Instantiate needed objects
            $performanceSongsRepo = new \Datalayer\PerformanceSongsRepository();
            $thisPerformanceSongs = new \Datalayer\PerformanceSongs();
            $totalEstimatedTimeFormated = '00:00:00';
            $form = new Form();

            try {

            // ************************
            // *   Generate Setlist   *
            // ************************
            if (isset($_POST["btnSetlist"])) {
                buildPerformanceSongsObject($thisPerformanceSongs);

                $criteria = [
                    'learned' => true,
                ];

                if ($thisPerformanceSongs->clean) {
                    $criteria['clean'] = true;
                }
                if ($thisPerformanceSongs->popular) {
                    $criteria['popular'] = true;
                }
                if ($thisPerformanceSongs->original) {
                    $criteria['original'] = true;
                }
                if (($_POST['chkUke'] ?? '') !== 'UKE') {
                    $criteria['excludeUke'] = true;
                }
                if (($_POST['chkPiano'] ?? '') !== 'PIANO') {
                    $criteria['excludePiano'] = true;
                }

                $rating = $_POST['selRating'] ?? '';
                if ($rating !== '' && is_numeric($rating)) {
                    $criteria['lowestRating'] = (int) $rating;
                }

                $aPerformanceSongsRecords = $performanceSongsRepo->find($criteria);

                if (sizeof($aPerformanceSongsRecords) < 1) {
                    $form->sMessage = "No PerformanceSongs records found matching search criteria";
                    $form->nMessageType = MESSAGE_TYPE_WARNING;
                    $form->nFormMode = FORM_MODE_NEW;
                } else {
                    $form->sMessage = "See Setlist below";
                    $form->nMessageType = MESSAGE_TYPE_INFO;
                    $form->nFormMode = FORM_MODE_SELECT;

                    $totalEstimatedTimeFormated = calculateSongTimeTotal($aPerformanceSongsRecords);
                }
            }
            // *************
            // *   CLEAR   *
            // *************
            else if (isset($_POST["btnClear"])) {

                clearFormFields($form);

                $form->sMessage = "Search for records or Add new record";
                $form->nMessageType = MESSAGE_TYPE_INFO;
                $form->nFormMode = FORM_MODE_NEW;
            }
            // *************
            // *   CANCEL  *
            // *************
            else if (isset($_POST["btnCancel"])) {
                clearFormFields($form);
                $form->sMessage = "Search for records or Add new record";
                $form->nMessageType = MESSAGE_TYPE_INFO;
                $form->nFormMode = FORM_MODE_NEW;
            }

            // ***************
            // *  1st TIME   *
            // ***************
            else {
                $form->sMessage = "Search for records or Add new record";
                $form->nMessageType = MESSAGE_TYPE_INFO;
                $form->nFormMode = FORM_MODE_NEW;
            }

            } catch (\Throwable $e) {
                $form->sMessage = $e->getMessage();
                $form->nMessageType = MESSAGE_TYPE_ERROR;
                $form->nFormMode = FORM_MODE_NEW;
            }

            ?>

            <?php
            include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
            ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12 Title">
                            Setlist
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 Buttons">
                            <input type="submit" name="btnSetlist" class="formButton" value="Setlist">
                            <input type="submit" name="btnClear" class="formButton" value="Clear">
                            <?php
                            if (sizeof($aPerformanceSongsRecords) > 0) {
                                echo "<button onclick=\"printSetlist()\">Print</button>";
                                echo "<button onclick=\"exportCSV()\">Export to CSV</button>";
                            }
                            ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 FormMessage">
                            <?php
                            $form->renderFormMessage();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if ($form->nFormMode == FORM_MODE_SELECT) {
            ?>

                <div class="row">

                    <div class="col-xs-12">

                        <?php

                        $sResultStyleClass = RESULT_STYLE_CLASS_ALT;

                        //Header row for Results
                        echo "<div class='row result-header'>";
                        echo "	<div class='hidden-xs col-sm-2 result-header'>Title</div>";
                        echo "	<div class='hidden-xs col-sm-2 result-header'>Artist</div>";
                        echo "	<div class='hidden-xs col-sm-1 result-header'>Tuning</div>";
                        echo "	<div class='hidden-xs col-sm-1 result-header'>Capo</div>";
                        echo "	<div class='hidden-xs col-sm-1 result-header'>Rating</div>";
                        echo "	<div class='hidden-xs col-sm-1 result-header'>Effect</div>";
                        echo "	<div class='hidden-xs col-sm-2 result-header'>Time</div>";
                        echo "	<div class='visible-xs col-xs-12 result-header'>Performance Songs</div>";
                        echo "</div>";


                        foreach ($aPerformanceSongsRecords as $oPerformanceSongsRecord) {

                            $sTimeClass='';
                            if(($oPerformanceSongsRecord->estimatedTime ?? '') == '00:00:00'){
                                $sTimeClass = 'missing-time';
                            }

                            //Alternate the result style
                            if ($sResultStyleClass == RESULT_STYLE_CLASS) {
                                $sResultStyleClass = RESULT_STYLE_CLASS_ALT;
                            } else {
                                $sResultStyleClass = RESULT_STYLE_CLASS;
                            }
                            echo "<div class='row {$sResultStyleClass}'>";
                            echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->title}</A></div>";
                            echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->artist}</A></div>";
                            echo "	<div class='hidden-xs col-sm-1 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->tuning}</A></div>";
                            echo "	<div class='hidden-xs col-sm-1 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->capo}</A></div>";
                            echo "	<div class='hidden-xs col-sm-1 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->rating}</A></div>";
                            echo "	<div class='hidden-xs col-sm-1 {$sResultStyleClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->effect}</A></div>";
                            echo "	<div class='hidden-xs col-sm-2 {$sResultStyleClass} {$sTimeClass}'><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->estimatedTime}</A></div>";
                            echo "</div>";
                        }
                        echo "<div class='hidden-xs row'>";
                        echo "	<div class='col-xs-12' ><hr></div>";
                        echo "</div>";

                        echo "<div class='hidden-xs row'>";
                        echo "	<div class='col-sm-2' ></div>";
                        echo "	<div class='col-sm-2' ></div>";
                        echo "	<div class='col-sm-1' ></div>";
                        echo "	<div class='col-sm-1' ></div>";
                        echo "	<div class='col-sm-1' ></div>";
                        echo "	<div class='col-sm-1' ><b>TOTAL:</b></div>";
                        echo "	<div class='col-sm-2'><b>{$totalEstimatedTimeFormated}</b></A></div>";
                        echo "</div>";
                        ?>

                        <div class="row" style="height: 20px;"></div>
                    </div>

                    <!-- SETLIST FOR PRINTING -->
                    <div id="setlist-print">

                        <table style="margin: 20px; width: 100%;">
                            <?php

                            $sResultStyleClass = RESULT_STYLE_CLASS_ALT;

                            //Header row for Results
                            echo "<tr>";
                            echo "	<td><b>Title</b></th>";
                            echo "	<td><b>Artist</b></th>";
                            echo "	<td><b>Tuning</b></th>";
                            echo "	<td><b>Capo</b></th>";
                            echo "	<td><b>Effect</b></th>";
                            echo "</tr>";


                            foreach ($aPerformanceSongsRecords as $oPerformanceSongsRecord) {

                                //Alternate the result style
                                if ($sResultStyleClass == RESULT_STYLE_CLASS) {
                                    $sResultStyleClass = RESULT_STYLE_CLASS_ALT;
                                } else {
                                    $sResultStyleClass = RESULT_STYLE_CLASS;
                                }
                                echo "<tr class='{$sResultStyleClass}'>";
                                echo "	<td class='{$sResultStyleClass}><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->title}</A></td>";
                                echo "	<td class='{$sResultStyleClass}><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->artist}</A></td>";
                                echo "	<td class='{$sResultStyleClass}><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->tuning}</A></td>";
                                echo "	<td class='{$sResultStyleClass}><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->capo}</A></td>";
                                echo "	<td class='{$sResultStyleClass}><A HREF='./PerformanceSongsMaintenance.php?PERFORMANCE_SONGS_ID={$oPerformanceSongsRecord->id}'>{$oPerformanceSongsRecord->effect}</A></td>";
                                echo "</tr>";
                            }

                            echo "<tr>";
                            echo "	<td></td>";
                            echo "	<td></td>";
                            echo "	<td></td>";
                            echo "	<td></td>";
                            echo "	<td></td>";
                            echo "	<td><b>TOTAL:</b></td>";
                            echo "</tr>";
                            ?>
                        </table>
                    </div>
                    <div id="setlist-csv">
                        <?php
                        $LastRow = sizeof($aPerformanceSongsRecords) + 1;
                        echo "Song,Artist,Tuning,Capo,Effect\n";

                        foreach ($aPerformanceSongsRecords as $oPerformanceSongsRecord) {
                            echo "{$oPerformanceSongsRecord->title},{$oPerformanceSongsRecord->artist},{$oPerformanceSongsRecord->tuning},{$oPerformanceSongsRecord->capo},{$oPerformanceSongsRecord->effect}\n";
                        }

                        ?>
                    </div>
                </div>
            <?php
            } else {
            ?>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="col-xs-12 FieldGroup">
                            <div class="row">
                                <div class="col-xs-12 col-md-3">
                                    LOWEST RATING:
                                    <SELECT ID="selRating" NAME="selRating">
                                        <OPTION <?php if (is_null($_POST['selRating'] ?? null)) {
                                                    echo " SELECTED='SELECTED' ";
                                                } ?> VALUE="">All</OPTION>
                                        <OPTION <?php if (($_POST['selRating'] ?? '') == "1") {
                                                    echo " SELECTED='SELECTED' ";
                                                } ?> VALUE="1">1</OPTION>
                                        <OPTION <?php if (($_POST['selRating'] ?? '') == "2") {
                                                    echo " SELECTED='SELECTED' ";
                                                } ?> VALUE="2">2</OPTION>
                                        <OPTION <?php if (($_POST['selRating'] ?? '') == "3") {
                                                    echo " SELECTED='SELECTED' ";
                                                } ?> VALUE="3">3</OPTION>
                                        <OPTION <?php if (($_POST['selRating'] ?? '') == "4") {
                                                    echo " SELECTED='SELECTED' ";
                                                } ?> VALUE="4">4</OPTION>
                                        <OPTION <?php if (($_POST['selRating'] ?? '') == "5") {
                                                    echo " SELECTED='SELECTED' ";
                                                } ?> VALUE="5">5</OPTION>
                                    </SELECT>
                                </div>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-3">
                                            <div class="row">
                                                <div class="col-xs-3 col-sm-2">
                                                    <input type="checkbox" name="chkClean" class="result-checkbox" value="CLEAN" <?php if (!empty($_POST['chkClean'])) {
                                                                                                                                        echo " checked ";
                                                                                                                                    }; ?> />
                                                </div>
                                                <div class="col-xs-9 col-sm-10 result-checkbox-text">
                                                    CLEAN
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-3">
                                            <div class="row">
                                                <div class="col-xs-3 col-sm-2">
                                                    <input type="checkbox" name="chkUke" class="result-checkbox" value="UKE" <?php if (!empty($_POST['chkUke'])) {
                                                                                                                                    echo " checked ";
                                                                                                                                }; ?> />
                                                </div>
                                                <div class="col-xs-9 col-sm-10 result-checkbox-text">
                                                    INCLUDE UKE SONGS
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-3">
                                            <div class="row">
                                                <div class="col-xs-3 col-sm-2">
                                                    <input type="checkbox" name="chkPiano" class="result-checkbox" value="PIANO" <?php if (!empty($_POST['chkPiano'])) {
                                                                                                                                        echo " checked ";
                                                                                                                                    }; ?> />
                                                </div>
                                                <div class="col-xs-9 col-sm-10 result-checkbox-text">
                                                    INCLUDE PIANO SONGS
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-3">
                                            <div class="row">
                                                <div class="col-xs-3 col-sm-2">
                                                    <input type="checkbox" name="chkPopular" class="result-checkbox" value="POPULAR" <?php if (!empty($_POST['chkPopular'])) {
                                                                                                                                        echo " checked ";
                                                                                                                                    }; ?> />
                                                </div>
                                                <div class="col-xs-9 col-sm-10 result-checkbox-text">
                                                    ONLY POPULAR SONGS
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-3">
                                            <div class="row">
                                                <div class="col-xs-3 col-sm-2">
                                                    <input type="checkbox" name="chkOriginal" class="result-checkbox" value="ORIGINAL" <?php if (!empty($_POST['chkOriginal'])) {
                                                                                                                                        echo " checked ";
                                                                                                                                    }; ?> />
                                                </div>
                                                <div class="col-xs-9 col-sm-10 result-checkbox-text">
                                                    ONLY ORIGINAL SONGS
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <dic class="col-xs-12 FormMessage">
                                <?php
                                $form->renderFormMessage();
                                ?>
                        </div>
                    </div>
                </div>
    </div>

<?php
            }
?>

</form>
</div>
<?php


/*
 ********************************************************************************
 * buildPerformanceSongsObject
 * 
 * This function loads builds a PerformanceSongs object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
function buildPerformanceSongsObject(\Datalayer\PerformanceSongs $PerformanceSongs)
{
    $PerformanceSongs->learned = true;
    $PerformanceSongs->clean = (($_POST['chkClean'] ?? '') == "CLEAN");
    $PerformanceSongs->popular = (($_POST['chkPopular'] ?? '') == "POPULAR");
    $PerformanceSongs->original = (($_POST['chkOriginal'] ?? '') == "ORIGINAL");
}

/*
 ********************************************************************************
 * clearFormFields
 * 
 * This function clears the form fields
 ********************************************************************************
*/
function clearFormFields($form)
{
    $_POST = array();
}


?>

</body>
<script src="./javascript/FileSaver.js"></script>
    <script type="text/javascript">
        function exportCSV() {

            let data = document.getElementById('setlist-csv').innerHTML
            data = data.trim()

            let blob = new Blob([data], {
                type: "text/csv;charset=utf-8"
            });

            saveAs(blob, "setlist.csv");
        }

        function validate_form() {
            bValid = true;
            sErrorMessage = "";

            //If any errors, alert
            if (!bValid) {
                alert(sErrorMessage);
            }

            return bValid;
        }

        function printSetlist() {
            var mywindow = window.open('', 'PRINT', 'height=400,width=600');

            mywindow.document.write('<html><head><title>' + document.title + '</title>');
            mywindow.document.write('</head><body >');
            mywindow.document.write('<h1>' + document.title + '</h1>');
            mywindow.document.write(document.getElementById('setlist-print').innerHTML);
            mywindow.document.write('</body></html>');

            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10*/

            mywindow.print();
            mywindow.close();

            return true;
        }
    </script>
    
<STYLE>
    #setlist-print,
    #setlist-csv {
        display: none;
        text-align: left;
    }

    #setlist td {
        font-size: 2rem;
    }

    th {
        text-align: left;
    }
</STYLE>

</html>
