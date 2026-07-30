<?php
/*
*******************************************************************
SearchTermsMaintenance.php
This PHP file defines the Maintenance page for managing Search Terms.
NOTES
Date        Change
-------------------------------------------------------------
2017-03-20	Made responsive
2017-09-02	Improved Responsivity
2021-08-30	Updated for PHP 8
2026-07-30	Migrated to new Datalayer SearchTerms repository
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
include_once(ADMIN_INCLUDE_DIR . "/CommonFunctions.php");

//include new datalayer
include_once(DATALAYER_DIR . "/Connection.php");
include_once(DATALAYER_DIR . "/SearchTerms.php");
include_once(DATALAYER_DIR . "/SearchTermsRepository.php");

//include Form Class		
include(CLASS_DIR . "/class_Form.php");

//Array of SearchTerms records from the DB
$aSearchTermsRecords = [];

$sActiveMenuItem = MISC_ACTIVE;	
$sPageName = "Search Terms Maintenance";

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
        <form name="SearchTermsMaint" action="SearchTermsMaintenance.php" method="post">

            <?php
            //Instantiate needed objects
            $searchTermsRepo = new \Datalayer\SearchTermsRepository();
            $thisSearchTerms = new \Datalayer\SearchTerms();
            $form = new Form();

            //Get the ID query string parameter
            $nThisSearchTermsID = $_REQUEST['SEARCH_TERMS_ID'] ?? null;

            //If a website is passed, go ahead and search SearchTerms for that website
            if (isset($_REQUEST['WEBSITE']) && $_REQUEST['WEBSITE'] != "") {
                $_POST['selWebsite'] = $_REQUEST['WEBSITE'];
                $_POST['btnSearch'] = "Search";
            }

            try {
                //If an ID was passed to the page, retrieve that record for update		
                if (!is_null($nThisSearchTermsID) && $nThisSearchTermsID !== '') {

                    $entity = $searchTermsRepo->findById((int) $nThisSearchTermsID);

                    if ($entity) {
                        $thisSearchTerms = $entity;
                        $aSearchTermsRecords = [$entity];
                        loadSearchTerms($thisSearchTerms, $form);

                        $form->sMessage = "Update record.";
                        $form->nMessageType = MESSAGE_TYPE_INFO;
                        $form->nFormMode = FORM_MODE_EDIT;
                    } else {
                        $form->sMessage = "SearchTerms record not found.";
                        $form->nMessageType = MESSAGE_TYPE_WARNING;
                        $form->nFormMode = FORM_MODE_NEW;
                    }
                } else {
                    //Based on which button was selected, perform processing necessary 
                    //before the page is rendered
                    // **************
                    // *   ADD      *
                    // **************
                    if (isset($_POST["btnAdd"])) {
                        buildSearchTermsObject($thisSearchTerms);

                        if ($searchTermsRepo->insert($thisSearchTerms)) {
                            $thisSearchTerms = $searchTermsRepo->findById((int) $thisSearchTerms->id) ?? $thisSearchTerms;
                            loadSearchTerms($thisSearchTerms, $form);

                            $form->nMessageType = MESSAGE_TYPE_INFO;
                            $form->sMessage = "SearchTerms Added";
                            $form->nFormMode = FORM_MODE_EDIT;
                        } else {
                            $form->nMessageType = MESSAGE_TYPE_ERROR;
                            $form->sMessage = "ADD RECORD FAILED";
                            $form->nFormMode = FORM_MODE_EDIT;
                        }
                    }
                    // **************
                    // *   UPDATE   *
                    // **************
                    else if (isset($_POST["btnUpdate"])) {

                        buildSearchTermsObject($thisSearchTerms);

                        if ($searchTermsRepo->update($thisSearchTerms)) {
                            $thisSearchTerms = $searchTermsRepo->findById((int) $thisSearchTerms->id) ?? $thisSearchTerms;
                            loadSearchTerms($thisSearchTerms, $form);

                            $form->nMessageType = MESSAGE_TYPE_INFO;
                            $form->sMessage = "SearchTerms Updated";
                            $form->nFormMode = FORM_MODE_EDIT;
                        } else {
                            $form->nMessageType = MESSAGE_TYPE_ERROR;
                            $form->sMessage = "ERROR: Update Failed";
                            $form->nFormMode = FORM_MODE_EDIT;
                        }
                    }
                    // **************
                    // *   DELETE   *
                    // **************
                    else if (isset($_POST["btnDelete"])) {

                        buildSearchTermsObject($thisSearchTerms);

                        if (!empty($thisSearchTerms->id) && $searchTermsRepo->delete((int) $thisSearchTerms->id)) {
                            clearFormFields($form);

                            $form->sMessage = "SearchTerms Deleted";
                            $form->nMessageType = MESSAGE_TYPE_INFO;
                            $form->nFormMode = FORM_MODE_NEW;
                        } else {
                            $form->sMessage = "DELETE FAILED";
                            $form->nMessageType = MESSAGE_TYPE_ERROR;
                            $form->nFormMode = FORM_MODE_EDIT;
                        }
                    }
                    // **************
                    // *   SEARCH   *
                    // **************
                    else if (isset($_POST["btnSearch"])) {
                        buildSearchTermsObject($thisSearchTerms);

                        $aSearchTermsRecords = $searchTermsRepo->find([
                            'id' => $thisSearchTerms->id,
                            'website' => $thisSearchTerms->website,
                            'pageName' => $thisSearchTerms->pageName,
                            'pageUrl' => $thisSearchTerms->pageUrl,
                            'searchTerms' => $thisSearchTerms->searchTerms,
                        ]);

                        if (sizeof($aSearchTermsRecords) < 1) {
                            $form->sMessage = "No SearchTerms records found matching search criteria";
                            $form->nMessageType = MESSAGE_TYPE_WARNING;
                            $form->nFormMode = FORM_MODE_NEW;
                        } else if (sizeof($aSearchTermsRecords) == 1) {
                            $thisSearchTerms = $aSearchTermsRecords[0];
                            loadSearchTerms($thisSearchTerms, $form);

                            $form->sMessage = "One SearchTerms record found.";
                            $form->nMessageType = MESSAGE_TYPE_INFO;
                            $form->nFormMode = FORM_MODE_EDIT;
                        } else {
                            $form->sMessage = "Select SearchTerms record to edit from results list below.";
                            $form->nMessageType = MESSAGE_TYPE_INFO;
                            $form->nFormMode = FORM_MODE_SELECT;
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
                    // *************
                    // *   COPY  *
                    // *************
                    else if (isset($_POST["btnCopy"])) {
                        copyFormFields($form);
                        $_POST['hdnSearchTermsId'] = NULL;

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
                }
            } catch (Exception $e) {
                error_log('SearchTermsMaintenance error: ' . $e->getMessage());
                $form->sMessage = "ERROR: " . $e->getMessage();
                $form->nMessageType = MESSAGE_TYPE_ERROR;
                $form->nFormMode = FORM_MODE_NEW;
            }

            ?>
            <!-- Hidden Fields -->
            <input type="hidden" name="hdnSearchTermsId" value="<?php echo $_POST['hdnSearchTermsId'] ?>" />

            <div class="row">
                <?php
                include(ADMIN_INCLUDE_DIR . "/AdminHeader-Responsive.php");
                ?>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-12 Title">
                            Search Terms Maintenance
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 Buttons">
                            <?php
                            $form->renderButtons();
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
                        echo "	<div class='col-sm-12 col-sm-2 result-header'>Web Site</div>";
                        echo "	<div class='col-sm-12 col-sm-2 result-header'>Page Name</div>";
                        echo "	<div class='col-sm-12 col-sm-3 result-header'>Page Url</div>";
                        echo "	<div class='col-sm-12 col-sm-5 result-header'>Search Terms</div>";
                        echo "</div>";


                        foreach ($aSearchTermsRecords as $oSearchTermsRecord) {

                            //Alternate the result style
                            if ($sResultStyleClass == RESULT_STYLE_CLASS) {
                                $sResultStyleClass = RESULT_STYLE_CLASS_ALT;
                            } else {
                                $sResultStyleClass = RESULT_STYLE_CLASS;
                            }
                            echo "<div class='row {$sResultStyleClass}'>";
                            echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./SearchTermsMaintenance.php?SEARCH_TERMS_ID={$oSearchTermsRecord->id}'>";
                            switch ($oSearchTermsRecord->website) {
                                case 1:
                                    echo "SWM";
                                    break;
                                case 2:
                                    echo "TBM";
                                    break;
                            }

                            echo "</a></div>";

                            echo "	<div class='col-xs-12 col-sm-2 {$sResultStyleClass}'><A HREF='./SearchTermsMaintenance.php?SEARCH_TERMS_ID={$oSearchTermsRecord->id}'>{$oSearchTermsRecord->pageName}</a></div>";
                            echo "	<div class='col-xs-12 col-sm-3 {$sResultStyleClass}'><A HREF='./SearchTermsMaintenance.php?SEARCH_TERMS_ID={$oSearchTermsRecord->id}'>{$oSearchTermsRecord->pageUrl}</a></div>";
                            echo "	<div class='col-xs-12 col-sm-5 {$sResultStyleClass}'><A HREF='./SearchTermsMaintenance.php?SEARCH_TERMS_ID={$oSearchTermsRecord->id}'>{$oSearchTermsRecord->searchTerms}</a></div>";
                            echo "</div>";
                        }
                        ?>

                        <div class="row" style="height: 20px;"></div>
                    </div>

                <?php
            } else {
                ?>

                    <div class="row">
                        <div class="col-xs-12 col-md-6 FieldGroup">
                            <div class="row">

                                <div class="col-xs-12">
                                    <?php
                                    echo "WEB SITE:\n"; 
                                    echo "<SELECT NAME=\"selWebsite\" ID=\"selWebsite\">\n";
                                    echo "<OPTION VALUE=\"0\">NONE</OPTION>\n";
                                    echo "<OPTION VALUE=\"1\" ";
                                    //SWM Hard-coded
                                    if ($_POST['selWebsite'] == 1)
                                    {
                                        echo " SELECTED ";
                                    }
                                    echo ">Steve Weeks Music</OPTION>";

                                    //TBM Hard-coded
                                    echo "<OPTION VALUE=\"2\" ";
                                    if ($_POST['selWebsite'] == 2)
                                    {
                                        echo " SELECTED ";
                                    }
                                    echo ">Travel Bugs Music</OPTION>";
                                    echo "</SELECT>";

                                    ?>
                                </div>
                                <div class="col-xs-12">
                                    PAGE NAME: <input type="text" name="txtPageName" value="<?php echo $_POST['txtPageName']; ?>" size="40" />
                                </div>
                                <div class="col-xs-12">
                                    PAGE URL: <input type="text" name="txtPageUrl" value="<?php echo $_POST['txtPageUrl']; ?>" size="30" />
                                </div>
                                <div class="col-xs-12">
                                    SEARCH TERMS: <input type="text" name="txtSearchTerms" value="<?php echo $_POST['txtSearchTerms']; ?>" size="30" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xs-12">
                            <div class="row FormFieldNoEdit">
                                <div class="col-xs-3">
                                    ID: <?php echo $_POST['hdnSearchTermsId']; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 Buttons">
                                    <?php
                                    $form->renderButtons();
                                    ?>
                                </div>
                            </div>
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
 * buildSearchTermsObject
 * 
 * This function loads builds a SearchTerms object from the data typed into the 
 * form fields.
 ********************************************************************************
*/
    function buildSearchTermsObject(\Datalayer\SearchTerms $SearchTerms)
    {
        $id = $_POST['hdnSearchTermsId'] ?? null;
        $SearchTerms->id = (!empty($id) && is_numeric($id)) ? (int) $id : null;

        $SearchTerms->website = (int) ($_POST['selWebsite'] ?? 0);
        $SearchTerms->pageName = html_entity_decode($_POST['txtPageName'] ?? '', ENT_QUOTES);
        $SearchTerms->pageUrl = html_entity_decode($_POST['txtPageUrl'] ?? '', ENT_QUOTES);
        $SearchTerms->searchTerms = html_entity_decode($_POST['txtSearchTerms'] ?? '', ENT_QUOTES);
    }


    /*
 ********************************************************************************
 * loadSearchTerms
 * 
 * This function loads the form field array from a populated SearchTerms object
 * so that it will be displayed in the form fields
 ********************************************************************************
*/
    function loadSearchTerms(\Datalayer\SearchTerms $SearchTerms, $form)
    {
        if (!is_null($SearchTerms->id)) {
            $_POST['hdnSearchTermsId'] = $SearchTerms->id;

            $_POST['selWebsite'] = $SearchTerms->website;
            $_POST['txtPageName'] = htmlentities($SearchTerms->pageName ?? '', ENT_QUOTES);
            $_POST['txtPageUrl'] = $SearchTerms->pageUrl;
            $_POST['txtSearchTerms'] = $SearchTerms->searchTerms;
           
        } else {
            foreach ($_POST as $fieldName => $fieldValue) {
                $_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
            }
        }
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

    /*
 ********************************************************************************
 * copyFormFields
 * 
 * This function copies the data from the form back into the form field array
 ********************************************************************************
*/
    function copyFormFields($form)
    {
        foreach ($_POST as $fieldName => $fieldValue) {
            $_POST[$fieldName] = htmlentities(stripslashes($fieldValue));
        }
    }

    ?>

</body>
<script type="text/javascript">
        <!--
        function validate_form() {
            bValid = true;
            sErrorMessage = "";

            //Webite is a required Field
            if (document.SearchTermsMaint.selWebsite.value == "0") {
                sErrorMessage += "Website Required\n";
                bValid = false;
            }

            //Page Name is a required Field
            if (document.SearchTermsMaint.txtPageName.value == "") {
                sErrorMessage += "Page Name Name Required\n";
                bValid = false;
            }

            //Page Url is a required Field
            if (document.SearchTermsMaint.txtPageUrl.value == "") {
                sErrorMessage += "Page URL Name Required\n";
                bValid = false;
            }


            //Search Terms is a required Field
            if (document.SearchTermsMaint.txtSearchTerms.value == "") {
                sErrorMessage += "Search Terms Required\n";
                bValid = false;
            }

            //If any errors, alert
            if (!bValid) {
                alert(sErrorMessage);
            }

            return bValid;
        }
        //
        -->
    </script>
</html>
