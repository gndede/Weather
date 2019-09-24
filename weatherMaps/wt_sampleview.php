<?php
/* * *****************************************************************************************************************************************
 * File Name: wt_sampleview.php
 * Project: Silicore
 * Description: This file allows the user to view QC sample details.
 * Notes:
 * =========================================================================================================================================
 * Change Log ([MM/DD/YYYY]|[Developer]|[Task Ticket] - [Description]
 * =========================================================================================================================================
 * 04/04/2018|gndede|KACE:20158 - Initial creation
 * 04/25/2018|gndede|KACE:20305 - Added the field Near Size.
 * 05/26/2018|gndede|KACE:20305 - Aded Near Size calculation code.
 * 05/09/2018|gndede|KACE:20156 - Added nuance to the calculation of the "PAN" Final Percentage.
 * 05/19/2018|gndede|KACE:21266 - Fixed a bug related to saving K Values.
 * 
 * **************************************************************************************************************************************** */

//==================================================================== BEGIN PHP

//include other files
require_once('../../Includes/QC/wt_qcfunctions.php'); //contains QC database query functions
require_once('../../Includes/security.php'); //contains user database query functions

//if there is not a sample ID set, then redirect the user to the samples page
$sampleId = "";
if(isset($_GET['sampleId']))
{
  $sampleId = test_input($_GET['sampleId']);
}
$sampleObject = NULL;
if(strlen($sampleId)==0)
{
  echo "<script type=\"text/javascript\">window.location = \"../../Controls/QC/wt_samples.php?void=A&view=verbose&completionStatus=1\";</script>"; //using JS, because output is already sent in header.php
}

else if(empty((array)(getSampleById($sampleId))))
  {
    echo "<script type=\"text/javascript\">window.location = \"../../Controls/QC/wt_samples.php?void=A&view=verbose&completionStatus=1\";</script>"; //using JS, because output is already sent in header.php
  }
else
  {
  $sampleObject = getSampleById($sampleId);
  
  if(isset($sampleObject->vars['plantId']) == false)
  {
    $sampleObject->vars['plantId'] = 0;
  }
  if(isset($sampleObject->vars['location']) == false)
  {
    $sampleObject->vars['location'] = 0;
  }
  if(isset($sampleObject->vars['specificLocation']) == false)
  {
    $sampleObject->vars['specificLocation'] = 0;
  }
}
?>

<link type="text/css" rel="stylesheet" href="../../Includes/qcstyles.css">
<link type="text/css" rel="stylesheet" href="../../Includes/jquery-ui-1.12.1.custom/datetimepicker/jquery.datetimepicker.css"> 
<script src="../../Includes/jquery-ui-1.12.1.custom/datetimepicker/jquery.datetimepicker.full.js"></script>

<div class="tab">
  <button class="tablinks active" onclick="openTab(event, 'qc_general')">General</button>
  <button class="tablinks" onclick="openTab(event, 'qc_characteristics')">Characteristics</button>
  <button class="tablinks" onclick="openTab(event, 'qc_plant_settings')">Plant Settings</button>
</div>

<form id="qcForm" name="qcForm" action="../../Includes/QC/sampleeditprocess.php" onsubmit="return validateForm()" method="post">

<input type="hidden" name="userId" id="userId" value="<?php echo $user_id; ?>">
<input type="hidden" name="sampleId" value="<?php echo $sampleId; ?>">

Location: <span id="sampleLocationSpan"></span>; 
DateTime: <span id="dateTimeSpan"></span>

<div id="qc_general" class="tabcontent">
  <h3>General</h3>
  <span style="color: red;">**Important: Complete this section first.**</span><br/><br/>
  
  <div class="SampleDetailColumnWrapper">
      
  <hr>
  
    <div class="leftcolumnsampledetail">
    
      <div class="form-group">
        <label for="siteId">Site:</label>
        <select name='siteId' id='siteId' disabled>
        <option value=""></option>
        <?php
        $siteObjectArray = getSites(); //get a list of site options
        foreach ($siteObjectArray as $siteObject) 
        {
          if($siteObject->vars["id"] == $sampleObject->vars['siteId'])
          {
            echo "<option value='" . $siteObject->vars["id"] . "' selected='selected'>" . $siteObject->vars["description"] . "</option>";
          }
          else 
          {
            echo "<option value='" . $siteObject->vars["id"] . "'>" . $siteObject->vars["description"] . "</option>";
          }
        }
        ?>
        <select>
        <br/>
      </div>

      <div class="plantSelect" id="plantSelect"></div>

      <div class="locationSelect" id="locationSelect"></div>

      <div class="specificLocationSelect" id="specificLocationSelect"></div>

      <div class="form-group">
        <label for="dt">DateTime:</label> 
        <input type="text" id="dt" name="dt" value="<?php echo $sampleObject->vars['dt']; ?>" readonly/>
        <br/>
      </div>

      <div class="form-group">
        <label for="testTypeId">Test Type:</label> 
        <select id="testTypeId" name="testTypeId" disabled>
        <option value=""></option>
        <?php
        $testTypeObjectArray = getTestTypes(); //get a list of testType options
        foreach ($testTypeObjectArray as $testTypeObject) 
        {
          if($testTypeObject->vars["id"] == $sampleObject->vars['testType'])
          {
            echo "<option value='" . $testTypeObject->vars["id"] . "' selected='selected'>" . $testTypeObject->vars["description"] . "</option>";
          }
          else 
          {
            echo "<option value='" . $testTypeObject->vars["id"] . "'>" . $testTypeObject->vars["description"] . "</option>";
          }
        }
        ?>
        </select>
        <br/>
      </div>

      <div class="form-group">
        <label for="compositeTypeId">Composite Type:</label> 
        <select id="compositeTypeId" name="compositeTypeId" disabled>
        <option value=""></option>
        <?php
        $compositeTypeObjectArray = getCompositeTypes(); //get a list of testType options
        foreach ($compositeTypeObjectArray as $compositeTypeObject) 
        {
          if($compositeTypeObject->vars["id"] == $sampleObject->vars['compositeType'])
          {
            echo "<option value='" . $compositeTypeObject->vars["id"] . "' selected='selected'>" . $compositeTypeObject->vars["description"] . "</option>";
          }
          else 
          {
            echo "<option value='" . $compositeTypeObject->vars["id"] . "'>" . $compositeTypeObject->vars["description"] . "</option>";
          }
        }
        ?>
        <select>
        <br/>
      </div>
      
      <div class="form-group">
        <label for="drillholeNo">Drillhole No.:</label> 
        <input type="text" id="drillholeNo" name="drillholeNo" placeholder="DHXXXX-YY" value="<?php echo $sampleObject->vars['drillholeNo']; ?>" disabled>
        <br/>
      </div>

      <div class="form-group">
        <label for="depthFrom">Depth From:</label> 
        <input type="text" id="depthFrom"  name="depthFrom" value="<?php echo $sampleObject->vars['depthFrom']; ?>" disabled>
        <br/>  
      </div>

      <div class="form-group">
        <label for="depthTo">Depth To:</label> 
        <input type="text" id="depthTo"  name="depthTo" value="<?php echo $sampleObject->vars['depthTo']; ?>" disabled>
        <br/>  
      </div>
      
      <div class="form-group">
        <label for="orePercent">Ore Percent:</label> 
        <input type="text" id="orePercent" name="orePercent" value="" readonly>
        <br/>
      </div>
      
      <div class="form-group">
        <label for="labTech">Lab Tech:</label> 
        <select id="labTech" name="labTech" disabled>
        <option value=""></option>
        <?php
        $userObjectArray = getLabTechs(); //get a list of users, requires security.php
        foreach ($userObjectArray as $userObject) 
        {
          if($userObject->vars["id"] == $sampleObject->vars['labTech'])
          {
            echo "<option value='" . $userObject->vars["id"] . "' selected='selected'>" . $userObject->vars["display_name"] . "</option>";
          }
          else 
          {
            echo "<option value='" . $userObject->vars["id"] . "'>" . $userObject->vars["display_name"] . "</option>";
          }
        }
        ?>
        </select>
        <br/>
      </div>

      <div class="form-group">
        <label for="sampler">Sampler:</label> 
        <select id="sampler" name="sampler" disabled>
        <option value=""></option>
        <?php
        $userObjectArray = getSamplers(); //get a list of users, requires security.php
        foreach ($userObjectArray as $userObject) 
        {
          if($userObject->vars["id"] == $sampleObject->vars['sampler'])
          {
            echo "<option value='" . $userObject->vars["id"] . "' selected='selected'>" . $userObject->vars["display_name"] . "</option>";
          }
          else 
          {
            echo "<option value='" . $userObject->vars["id"] . "'>" . $userObject->vars["display_name"] . "</option>";
          }
        }
        ?>
        </select>
        <br/>
      </div>

      <div class="form-group">
        <label for="operator">Operator:</label> 
        <select id="operator" name="operator" disabled>
        <option value=""></option>
        <?php
        $userObjectArray = getOperators(); //get a list of users, requires security.php
        foreach ($userObjectArray as $userObject) 
        {
          if($userObject->vars["id"] == $sampleObject->vars['operator'])
          {
            echo "<option value='" . $userObject->vars["id"] . "' selected='selected'>" . $userObject->vars["display_name"] . "</option>";
          }
          else 
          {
            echo "<option value='" . $userObject->vars["id"] . "'>" . $userObject->vars["display_name"] . "</option>";
          }
        }
        ?>
        </select>
        <br/>
      </div>
      
      <div class="sieveStackSelect" id="sieveStackSelect"></div>
      
    </div>
  
    <div class="rightcolumnsampledetail">
    
      <div class="form-group" id="beginningWetWeightWrapper">
        <label for="beginningWetWeight" id="beginningWetWeightLabel">Beginning Wet Weight (g):</label> 
        <input type="number" step="0.1" max="99999" id="beginningWetWeight" name="beginningWetWeight" value="<?php echo $sampleObject->vars['beginningWetWeight']; ?>" onchange="calculateMoistureRate()" disabled>
        <br/>
      </div>

      <div class="form-group" id="preWashDryWeightWrapper">
        <label for="preWashDryWeight" id="preWashDryWeightLabel">Pre-Wash Dry Weight (g):</label> 
        <input type="number" step="0.1" max="99999" id="preWashDryWeight" name="preWashDryWeight" value="<?php echo $sampleObject->vars['preWashDryWeight']; ?>" onchange="calculateMoistureRate(); calculateOrePercent(); updateOversizePercent();" disabled>
        <br/>
      </div>

      <div class="form-group" id="postWashDryWeightWrapper">
        <label for="postWashDryWeight" id="postWashDryWeightLabel">Post-Wash Dry Weight (g):</label> 
        <input type="number" step="0.1" max="99999" id="postWashDryWeight" name="postWashDryWeight" value="<?php echo $sampleObject->vars['postWashDryWeight']; ?>" onchange="calculateMoistureRate(); calculateOrePercent();" disabled>
        <br/>
      </div>

      <div class="form-group">
        <label for="description">Sample No./Description:</label> 
        <input type="text" id="description"  name="description" placeholder="XXXX-XXXX" value="<?php 
          if(isset($sampleObject->vars['description']))
          { 
            if($sampleObject->vars['description'] != "")
            {
              echo $sampleObject->vars['description'];       
            }
            else
            {
              echo $sampleObject->vars['id']; 
            }
          }
          else
          {
            echo $sampleObject->vars['id']; 
          }
          ?>" disabled>
        <br/>  
      </div>

      <div class="form-group">
        <label for="oversizeWeight">Oversize Weight:</label> 
        <input type="number" step="0.01" min="0" id="oversizeWeight" name="oversizeWeight" value="<?php echo $sampleObject->vars['oversizeWeight']; ?>" onChange="updateOversizePercent(); setLostValues(); calculateRates();" disabled>
        <br/>
      </div>

      <div class="form-group">
        <label for="oversizePercent">Oversize Percent:</label> 
        <input type="text" id="oversizePercent" name="oversizePercent" disabled>
        <br/>
      </div>

      <div class="form-group">
        <label for="splitSampleWeight">Split Sample Weight (g):</label> 
        <input type="number" step="0.1" max="99999" id="splitSampleWeight" name="splitSampleWeight" value="<?php echo $sampleObject->vars['splitSampleWeight']; ?>" onchange="calculateMoistureRate()" disabled>
        <br/>
      </div>

      <div class="form-group">
        <label for="moistureRate">Moisture Rate (%):</label> 
        <input type="text" id="moistureRate" name="moistureRate" value="<?php echo $sampleObject->vars['moistureRate']; ?>" disabled>
        <br/>
      </div>

      <div class="form-group">
        <label for="gramsLost">Grams Lost in Wash:</label> 
        <input type="text" id="gramsLost" name="gramsLost" disabled>
      </div>
      <br/>

      <div class="form-group">
        <label for="percentLost" id="percentLostLabel">Percent Lost in Wash:</label> 
        <input type="text" id="percentLost" name="percentLost" disabled>
      </div>
      <br/>

      <div class="form-group">
        <label for="newSplit">New Split Weight:</label> 
        <input type="text" id="newSplit" name="newSplit" disabled>
      </div>
      <br/>

      <div class="form-group">
        <label for="notes">Notes:</label> 
        <input type="text" id="notes" name="notes" value="<?php echo $sampleObject->vars['notes']; ?>" disabled>
        <br/>
      </div>
      
      <div class="form-group">
        <label for="isCOA">is COA:</label> 
        <?php
        if($sampleObject->vars['isCOA'] == 1)
        {
          echo("<input type='checkbox' id='isCOA' name='isCOA' type='isCOA' value='1' checked>");
        }
        else
        {
          echo("<input type='checkbox' id='isCOA' name='isCOA' type='isCOA' value='1'");
        }
        ?>    
        <br/>
      </div>
    
    </div>  
  
  </div>
  
  <div class="sieveAreaSampleDetail">

    <div id="qc_sieves">
      <div id="sieveSelect"></div>
    </div>

    <button class="qcButtons" type="button" onclick="location.href = '../../Controls/QC/wt_samples.php?void=A&view=summary';">Back</button>
    
  </div>
  
  
</div>

<div id="qc_characteristics" class="tabcontent">
  <h3>Characteristics</h4>
  <h4>Turbidity</h3>
  
  <div class="form-group">
    <label for="turbidity">Turbidity (NTU):</label> 
    <input type="number" id="turbidity" name="turbidity" value="<?php echo $sampleObject->vars['turbidity']; ?>" disabled>
    <br/>
  </div>
  
  <h4>Crush</h4>
  
  <div class="form-group">
    <label for="kValue">K Value:</label> 
    <select name="kValue" id="kValue" onChange="showCurrentKValueGroup();">
    <?php
    $kValueObjectArray = getKValues(); //get a list of testType options
    foreach ($kValueObjectArray as $kValueObject) 
    {
      if($kValueObject->vars["id"] == $sampleObject->vars['kValue'])
      {
        echo "<option value='" . $kValueObject->vars["id"] . "' selected='selected'>" . $kValueObject->vars["description"] . "</option>";
      }
      else 
      {
        echo "<option value='" . $kValueObject->vars["id"] . "'>" . $kValueObject->vars["description"] . "</option>";
      }
    }
    ?>
    <select>
    <br/>
  </div>
  
  <?php
  $kValueObjectArray = getKValues(); //get a list of testType options
  $counter = 0;
  foreach ($kValueObjectArray as $kValueObject) 
  {
    //read the Pan values from the database
    $kValueObjectPan1 = NULL;
    $kValueObjectPan2 = NULL;
    $kValueObjectPan3 = NULL;
    
    $kValuePan1 = "";
    $kValuePan2 = "";
    $kValuePan3 = "";
    
    $kValueObjectPan1 = getKValueRecord($sampleId, $kValueObject->vars["id"], "kPan1");
    $kValueObjectPan2 = getKValueRecord($sampleId, $kValueObject->vars["id"], "kPan2");
    $kValueObjectPan3 = getKValueRecord($sampleId, $kValueObject->vars["id"], "kPan3");
    
    if($kValueObjectPan1 != NULL)
    {
      $kValuePan1 = $kValueObjectPan1->vars['value'];
    }
    
    if($kValueObjectPan2 != NULL)
    {
      $kValuePan2 = $kValueObjectPan2->vars['value'];
    }
    
    if($kValueObjectPan3 != NULL)
    {
      $kValuePan3 = $kValueObjectPan3->vars['value'];
    }
    
    echo('<div class="kValueGroup">');
      //echo('K Value Group: ' . $kValueObject->vars["id"] . '<br/>');
      echo('<div class="form-group">');
        echo('<label for="kPan1[' . $counter . ']">PAN 1 (g):</label> ');
        echo('<input type="number" step="0.0001" max="9999999" class="kPan1" id="kPan1[' . $counter . ']" name="kPan1[]" value="' . $kValuePan1 . '" onchange="updateKPan1Calculation();" disabled>');
        echo('<span class="kPan1Calculation" id="kPan1Calculation[]"></span>');
        echo('<br/>');
      echo('</div>');

      echo('<div class="form-group">');
        echo('<label for="kPan2[' . $counter . ']">PAN 2 (g):</label> ');
        echo('<input type="number" step="0.0001" max="9999999" class="kPan2" id="kPan2[' . $counter . ']" name="kPan2[]" value="' . $kValuePan2 . '"  onchange="updateKPan2Calculation(;" disabled>');
        echo('<span class="kPan2Calculation" id="kPan2Calculation[]"></span>');
        echo('<br/>');
      echo('</div>');

      echo('<div class="form-group">');
        echo('<label for="kPan3[' . $counter . ']">PAN 3 (g):</label> ');
        echo('<input type="number" step="0.0001" max="9999999" class="kPan3" id="kPan3[' . $counter . ']" name="kPan3[]" value="' . $kValuePan3 . '"  onchange="updateKPan3Calculation();" disabled>');
        echo('<span class="kPan3Calculation" id="kPan3Calculation[]"></span>');
        echo('<br/>');
      echo('</div>');
    echo('</div>');
    $counter++;
  }
  ?>
  
  <div class="form-group">
    <label>Average:</label><input type="text" id="kPanAverage" disabled>
    <br/>
  </div>
  
  <div class="form-group">
    <label>Status:</label><input type="text" id="kPanAverageStatus" disabled>
    <br/>
  </div>
    
  <h4>Roundness & Sphericity</h4>
  <div class="form-group">
    <label for="roundness">Roundness:</label> 
    <input type="number" step="0.1" max="99999" id="roundness" name="roundness" value="<?php echo $sampleObject->vars['roundness']; ?>" disabled>
    <br/>
  </div>
  
  <div class="form-group">
    <label for="sphericity">Sphericity:</label> 
    <input type="number" step="0.1" max="99999" id="sphericity" name="sphericity" value="<?php echo $sampleObject->vars['sphericity']; ?>" disabled>
    <br/>
  </div>
  
  <button class="qcButtons" type="button" onclick="location.href = '../../Controls/QC/wt_samples.php?void=A&view=summary';">Back</button>
</div>

<div id="qc_plant_settings" class="tabcontent">
  <h3>Plant Settings</h3>
  <div id="plantSettings">Select a Plant to view the Plant Settings.</div> <!-- Plant Settings input fields wiSelect a Plant to view the Plant Settingsll be loaded asynchronously based on the setting in the Plant select box. -->
 
  <button class="qcButtons" type="button" onclick="location.href = '../../Controls/QC/wt_samples.php?void=A&view=summary';">Back</button>
</div>
    
<br/>


</form>

<script>
//call JQuery to render the datepicker tool
$(function() 
{
 $("#dt").datetimepicker(
  {
    format: 'Y-m-d H:i',
    //dateFormat: 'yy-mm-dd',
    onSelect: function(datetext)
    {
      var d = new Date(); // for now
      
      var h = d.getHours();
      h = (h < 10) ? ("0" + h) : h ;
    
      var m = d.getMinutes();
      m = (m < 10) ? ("0" + m) : m ;
      
      var s = d.getSeconds();
      s = (s < 10) ? ("0" + s) : s ;
      
      datetext = datetext + " " + h + ":" + m + ":" + s;
      $('#dt').val(datetext);
    }
  });
});
</script>
<script>
//load the options when the window loads
document.getElementsByTagName('body')[0].onload = initialLoad();

var wetWeightsEnabled = 1; //global variable

function initialLoad()
{  
  function runFirst(callback) 
  {
    loadPlantSelect();
    callback();
  }
  
  function runSecond(callback) 
  {
    loadSieveStackSelect();
    callback();
  }
  
  function runThird(callback) 
  {
    showCurrentKValueGroup();
    callback();
  }
  
  function runFourth(callback) 
  {
    setTimeout(function(){ calculateTotalFinalWeight(); }, 1000); 
    //dev note: added setTimeout, because this was running before the other data was ready
    
    callback();
  }
  
  function runFifth(callback) 
  {
    updateLocationSpan();
    callback();
  }
  
  function runSixth(callback) 
  {
    updateDateTimeSpan();
    callback();
  }
  
  runFirst(function() 
  {
    console.log('Finished loadPlantSelect function.');
    
    runSecond(function() 
    {
      console.log('Finished loadSieveStackSelect function.');
      
      runThird(function() 
      {
        console.log('Finished showCurrentKValueGroup function.');
        
        runFourth(function() 
        {
          console.log('Finished calculateTotalFinalWeight function.');
                    
          runFifth(function() 
          {
            console.log('Finished updateLocationSpan function.');
            
            runSixth(function() 
            {
              console.log('Finished updateDateTimeSpan function.');
              
            });
          });
        });
      });
    });
  });

  //Note: getSieves, getPlantSettings, and loadLocationSelect are called by loadPlantSelect
  //Note: loadSpecificLocationSelect is called by loadLocationSelect

}

//calculate and update the Oversize Percentage field
function updateOversizePercent()
{
  //console.log("DEBUG: updateOversizePercent function called!");
  
  var postWash = null;
  var preWash = null;
  var oversizeWeight = null;
  var totalFinalWeight = null;
  var startWeight1ReadOnly = null;
  var oversizePercent = "";
  
  //get the value of Post Wash
  postWash = document.getElementById("postWashDryWeight").value;
  //console.log("DEBUG: postWash = "+postWash);
  
  //get the value of Pre Wash
  preWash = document.getElementById("preWashDryWeight").value;
  //console.log("DEBUG: preWash = "+preWash);
  
  //get the value of Oversize Weight
  oversizeWeight = document.getElementById("oversizeWeight").value;
  //console.log("DEBUG: oversizeWeight = "+oversizeWeight);
  
  //get the value of totalFinalWeight
  try
  {
    totalFinalWeight = document.getElementById("totalFinalWeight").value;
  }
  catch(err)
  {
    console.log("error: " + err);
    totalFinalWeight = 0;
  }
  //console.log("DEBUG: totalFinalWeight = "+totalFinalWeight);
  
  //calculate Oversize Percent as oversizeWeight / preWash
  if(oversizeWeight == "")
  {
    oversizePercent = "";
  }
  else if(preWash == "")
  {
    oversizePercent = "";
  }
  else
  {
    oversizePercent = (oversizeWeight / preWash).toFixed(4);
  }
  
  //set the value of the oversizePercent input
  if(totalFinalWeight == "NaN")
  {
    oversizePercent = "";
    document.getElementById("oversizePercent").value = oversizePercent;
  }
  else
  {
    document.getElementById("oversizePercent").value = (oversizePercent * 100) + "%";
  }
}

//check if the selected Sample Location is "Core Sample"
function checkIfCoreSample()
{
  try
  {
    var sel = document.getElementById("locationId");
    var text= sel.options[sel.selectedIndex].text;
    //console.log("text = " + text);
    if(text == "Core Sample")
    {
      return true;
    }
    else if(text == "Core Samples")
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  catch(err)
  {
    console.log("error: " + err);
    return false;
  }
}

//show or hide fields related to core samples
function updateCoreSampleFieldVisibility()
{
  //console.log("DEBUG: updateCoreSampleFieldVisibility function called!");
  
  //get the name of the currently selected Location 
  var locationIDSelect = document.getElementById("locationId");
  var currentLocationNameSelected = null;
  if(locationIDSelect != null)
  {  
    currentLocationNameSelected = locationIDSelect.options[locationIDSelect.selectedIndex].text;
  }
  
  //console.log("DEBUG: currentLocationNameSelected = " + currentLocationNameSelected);
  if(currentLocationNameSelected != null)
  {  
    //update the Core Sample fields based on the Test Type ID. Core Sample == 7
    if(currentLocationNameSelected == "Core Sample")
    {      
      //console.log("DEBUG: currentLocationNameSelected = Core Sample");
        
      //show the core sample fields
      document.getElementById("drillholeNo").parentElement.style.display = "block";
      document.getElementById("depthFrom").parentElement.style.display = "block";
      document.getElementById("depthTo").parentElement.style.display = "block";      
      document.getElementById("oversizeWeight").parentElement.style.display = "block";
      document.getElementById("oversizePercent").parentElement.style.display = "block";
      document.getElementById("orePercent").parentElement.style.display = "block";
      
      document.getElementById("percentLostLabel").innerHTML = "Slimes Percent:";
    }
    else
    {
      //hide the core sample fields
      document.getElementById("drillholeNo").parentElement.style.display = "none";
      document.getElementById("depthFrom").parentElement.style.display = "none";
      document.getElementById("depthTo").parentElement.style.display = "none";
      document.getElementById("oversizeWeight").parentElement.style.display = "none";
      document.getElementById("oversizePercent").parentElement.style.display = "none";
      document.getElementById("orePercent").parentElement.style.display = "none";
      
      document.getElementById("percentLostLabel").innerHTML = "Percent Lost in Wash:";
    }
  }
  else
  {
    //hide the core sample fields
    document.getElementById("drillholeNo").parentElement.style.display = "none";
    document.getElementById("depthFrom").parentElement.style.display = "none";
    document.getElementById("depthTo").parentElement.style.display = "none"; 
    document.getElementById("oversizeWeight").parentElement.style.display = "none";
    document.getElementById("oversizePercent").parentElement.style.display = "none";
    document.getElementById("orePercent").parentElement.style.display = "none";
    
    document.getElementById("percentLostLabel").innerHTML = "Percent Lost in Wash:";
  }
}

//hide the K Value Groups which are not selected
function showCurrentKValueGroup()
{
  //console.log("DEBUG: showCurrentKValueGroup function called!");
  
  var elementIteratorArray = null;
  var adjustedCurrentKValueSelected = null;
  var i = 0;
  
  //get the count of the number of K Value divs from the PHP
  var countOfKValueDivs = <?php echo count($kValueObjectArray); ?>;
  //console.log("DEBUG: countOfKValueDivs =" + countOfKValueDivs);
  
  //get the value of the currently selected K Value
  var currentKValueSelected = document.getElementById("kValue").value;
  //console.log("DEBUG: currentKValueSelected =" + currentKValueSelected);
  
  //adjust the value to a count (assumes that the values start at 4 and are sequential, which was correct when this was written)
  if(currentKValueSelected == "")
  {
    adjustedCurrentKValueSelected = 0;
  }
  else
  {
    adjustedCurrentKValueSelected = currentKValueSelected - 1;
  }
  //console.log("DEBUG: adjustedCurrentKValueSelected =" + adjustedCurrentKValueSelected);
  
  elementIteratorArray = document.getElementsByClassName("kValueGroup");
  
  //iterate through all K Value options
  
  for (i = 0; i < countOfKValueDivs; i++) 
  {
    //console.log("DEBUG: i = " + i);
    
    //hide the div
    elementIteratorArray[i].style.display = "none";
  
    //if this is the div we are looking for
    if(i == adjustedCurrentKValueSelected)
    {
      //console.log("DEBUG: i = currentKValueSelected!");
      
      //unhide that div
      elementIteratorArray[i].style.display = "block";
    }
  }
  
  updateKPan1Calculation();
  updateKPan2Calculation();
  updateKPan3Calculation();
  updateKPanAverage(); //update the values in the Average and Status, since we are now looking at a different K Value group
}
 
 
//populate the select box for Sieve Stack
function loadSieveStackSelect()
{
  //console.log("DEBUG: loadSieveStackSelect function called!");
  
  //get the value from the select box
  var siteID = document.getElementById("siteId").value;

  if(siteID.length > 0)
  {
    //update the values
    var xmlhttp2 = new XMLHttpRequest();
    xmlhttp2.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {
        document.getElementById("sieveStackSelect").innerHTML = this.responseText; 
        //console.log("DEBUG: this.responseText = "+this.responseText);
        getSieves();
      }
    }
    
    //get the content
    <?php
    if($sampleObject->vars['sieveMethod'] == NULL)
    {
        $sampleObject->vars['sieveMethod'] = 0;
    }
    ?>
    xmlhttp2.open("GET", "../../Includes/QC/wt_sievestacksselectbysitereadonly.php?siteID="+siteID+"&sieveStackID="+<?php echo $sampleObject->vars['sieveMethod']; ?>, true);
    xmlhttp2.send();
      
  }
    
}


//populate the select box for Plant
function loadPlantSelect()
{
  //console.log("DEBUG: loadPlantSelect function called!");
  
  //get the value from the select box
  var siteId = document.getElementById("siteId").value;

  if(siteId.length > 0)
  {
    //update the values
    var xmlhttp2 = new XMLHttpRequest();
    xmlhttp2.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {
        document.getElementById("plantSelect").innerHTML = this.responseText; 
        loadLocationSelect();
        getSieves();
        getPlantSettings();  
        calculateOrePercent();        
      }
    }
    
    //get the content
    xmlhttp2.open("GET", "../../Includes/QC/wt_plantsselectbysitereadonly.php?siteId="+siteId+"&plantId="+<?php echo $sampleObject->vars['plantId']; ?>, true);
    xmlhttp2.send();
      
  }
    
}
  
//populate the select box for Location
function loadLocationSelect()
{
  //console.log("DEBUG: loadLocationSelect() function called!");
  
  //get the value from the select box
  var plantId = document.getElementById("plantId").value;

  if(plantId.length > 0)
  {
    //update the values
    var xmlhttp2 = new XMLHttpRequest();
    xmlhttp2.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {      
        document.getElementById("locationSelect").innerHTML = this.responseText; 
        loadSpecificLocationSelect();
        getPlantSettings();
        updateCoreSampleFieldVisibility();
        
        updateLocationSpan();
        updateDateTimeSpan();
      }
    }
    
    //get the content
    xmlhttp2.open("GET", "../../Includes/QC/wt_locationsselectbyplantreadonly.php?plantId="+plantId+"&locationId="+<?php echo $sampleObject->vars['location']; ?>, true);
    xmlhttp2.send();
  
  }
  
}

//populate the select box for Specific Location
function loadSpecificLocationSelect()
{
  enableDisableWetWeights();
  
  //console.log("DEBUG: loadSpecificLocationSelect() function called!");
  
  //get the value from the select box
  var locationId = document.getElementById("locationId").value;
  //console.log("locationId = "+locationId);

  if(locationId.length > 0)
  {
    //update the values
    var xmlhttp2 = new XMLHttpRequest();
    xmlhttp2.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {      
        //console.log("this.responseText = "+this.responseText);
        document.getElementById("specificLocationSelect").innerHTML = this.responseText; 
      }
    }
    
    //get the content
    xmlhttp2.open("GET", "../../Includes/QC/wt_specificlocationsselectbylocationreadonly.php?locationId="+locationId+"&specificLocationId="+<?php echo $sampleObject->vars['specificLocation']; ?>, true);
    xmlhttp2.send();
  
  }
  
}
  
//functionality for the tab navigation
function openTab(evt, tabName) 
{
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) 
    {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) 
    {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

//update the sieves table when the sieve method changes
function getSieves()
{

  //get the Sieve Stack value from the select box
  if(document.getElementById("sieveStackId") != null)
  {
    var sieveStackId = document.getElementById("sieveStackId").value;
  }

  if(sieveStackId != null)
  {
    //update the values
    var xmlhttp2 = new XMLHttpRequest();
    xmlhttp2.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {
        document.getElementById("sieveSelect").innerHTML = this.responseText; //display the wt_sievesget.php output inside sampleedit.php
        
        calculateRates(); //load the javascript calculations
        updateKPan1Calculation(); //update KPan calculation
        updateKPan2Calculation(); //update KPan calculation
        updateKPan3Calculation(); //update KPan calculation
        updateKPanAverage(); //update KPan Average calculation
        calculateTotalFinalWeight();
        
        updateOversizePercent();
        
        //update empty sieve range values to NA
        changeBlankSieveRangesToNA();
      }
    }

    //console.log("DEBUG: getting sieves. sieveStackId = " + sieveStackId);

    //get the content
    xmlhttp2.open("GET", "../../Includes/QC/wt_sievesgetreadonly.php?stackId="+sieveStackId+"&sampleId="+<?php echo $sampleId; ?>, true);
    xmlhttp2.send();
  
  }
}

//update the plant settings input fields when the plant changes
function getPlantSettings()
{
  //console.log("DEBUG: getPlantSettings() function called!");
  
  //get the value from the select box
  var plantId = document.getElementById("plantId").value;
  //console.log("DEBUG: plantId = "+plantId);

  if(plantId.length > 0)
  {
    //update the values
    var xmlhttp2 = new XMLHttpRequest();
    xmlhttp2.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {
        document.getElementById("plantSettings").innerHTML = this.responseText; 
      }
    }
    
    //get the content
    xmlhttp2.open("GET", "../../Includes/QC/wt_plantsettingsinputfields.php?plantId="+plantId+"&sampleId="+<?php echo $sampleId; ?>, true);
    xmlhttp2.send();
  
  }
  
}

//utility function to reduce code size, checks if select box is empty
function checkIfSelected(argValue, argName)
{
  if(argValue == "")
  {
    alert(argName + " must be selected.")
    return false;
  }
  else
  {
    return true;    
  }
}

//utility function to reduce code size, checks if text input is empty
function checkIfEntered(argValue, argName)
{
  if(argValue == "")
  {
    alert(argName + " must be completed.")
    return false;
  }
  else
  {
    return true;    
  }
}

//This function validates if a value is a number. 
//It alerts the user with the name of the field if not.
function checkIfNumber(argValue, argName)
{
  if(isNaN(argValue))
  {
    alert(argName + " must be a number.")
    return false;
  }
  else
  {
    return true;    
  }
}

//This function validates if a value is greater than a maximum. 
//It alerts the user with the name of the field if so.
function checkIfLessThan(argValue, argName, argMaximum)
{
  if(argValue > argMaximum)
  {
    alert(argName + " must be less than " + argMaximum + ".")
    return false;
  }
  else
  {
    return true;    
  }
}

//this function performs validation on the user inputs
function validateForm() 
{
  var x;
  var returnValue = true;
  var itemPercent = null;
  var negativeWeightFound = false;
  var sieveCount = null;
  var splitSampleWeightValue = null;
  var postWashDryWeightValue = null;
  var preWashDryWeightValue = null;
  var beginningWetWeightValue = null;
  
  
  //check that none of the sieve percentages are negative
  sieveCount = document.getElementById('sieveCount').value;
  
  for (i = 1; i < sieveCount; i++) //iterate through all sieves
  {        
    if(parseFloat(document.getElementById('finalWeight' + i).value) < 0)
    {
      negativeWeightFound = true;
    }
  }
  if(negativeWeightFound == true)
  {
    return confirm("Negative Sieve Final Weights were found. Are you Sure?")    
  }
  
  
  if(wetWeightsEnabled == 1) //only if wet weights are enabled
  {
    //Check that Split Sample Weight ! > Post Wash Dry Weight.
    splitSampleWeightValue = document.getElementById('splitSampleWeight').value;
    postWashDryWeightValue = document.getElementById('postWashDryWeight').value;
    //console.log("DEBUG: splitSampleWeightValue = " + splitSampleWeightValue);
    //console.log("DEBUG: postWashDryWeightValue = " + postWashDryWeightValue);
    if(parseFloat(splitSampleWeightValue) > parseFloat(postWashDryWeightValue))
    {
      alert("Split Sample Weight cannot be greater than Post Wash Dry Weight!")
      return false;
    }

    //Check that Post Wash Dry Weight ! > Pre Wash Dry Weight.
    preWashDryWeightValue = document.getElementById('preWashDryWeight').value;
    if(parseFloat(postWashDryWeightValue) > parseFloat(preWashDryWeightValue))
    {
      alert("Post Wash Dry Weight cannot be greater than Pre Wash Dry Weight!")
      return false;
    }

    //Check that Pre Wash Dry Weight ! > Beginning Wet Weight.
    beginningWetWeightValue = document.getElementById('beginningWetWeight').value;
    if(parseFloat(preWashDryWeightValue) > parseFloat(beginningWetWeightValue))
    {
      alert("Pre Wash Dry Weight cannot be greater than Beginning Wet Weight!")
      return false;
    }
  }
  
  //DEV NOTE: Disabled per KACE # 17284
  /*
  var testTypeName = document.forms["qcForm"]["testTypeId"].options[document.forms["qcForm"]["testTypeId"].selectedIndex].text;
  
  x = document.forms["qcForm"]["siteId"].value;
  returnValue = checkIfSelected(x, "Site");
  if(returnValue == false) { return false }
  
  x = document.forms["qcForm"]["plantId"].value;
  returnValue = checkIfSelected(x, "Plant");
  if(returnValue == false) { return false }
   
  x = document.forms["qcForm"]["locationId"].value;
  returnValue = checkIfSelected(x, "Location");
  if(returnValue == false) { return false }
  
  x = document.forms["qcForm"]["dt"].value;
  returnValue = checkIfSelected(x, "Date / Time");
  if(returnValue == false) { return false }
  
  x = document.forms["qcForm"]["testTypeId"].value;
  returnValue = checkIfSelected(x, "Test Type");
  if(returnValue == false) { return false }
  
  if(testTypeName != "Inhibited")
  {
    x = document.forms["qcForm"]["compositeTypeId"].value;
    returnValue = checkIfSelected(x, "Composite Type");
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["sieveStackId"].value;
    returnValue = checkIfSelected(x, "Sieve Stack");
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["labTech"].value;
    returnValue = checkIfSelected(x, "Lab Tech");
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["sampler"].value;
    returnValue = checkIfSelected(x, "Sampler");
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["operator"].value;
    returnValue = checkIfSelected(x, "Operator");
    if(returnValue == false) { return false }
*/
    /* //DEV NOTE: This validation functionality was disabled, because code was added which changes the visibility of these items
    x = document.forms["qcForm"]["beginningWetWeight"].value;
    returnValue = checkIfEntered(x, "Beginning Wet Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Beginning Wet Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Beginning Wet Weight", 99999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["preWashDryWeight"].value;
    returnValue = checkIfEntered(x, "Pre Wash Dry Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Pre Wash Dry Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Pre Wash Dry Weight", 99999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["postWashDryWeight"].value;
    returnValue = checkIfEntered(x, "Post Wash Dry Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Post Wash Dry Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Post Wash Dry Weight", 99999);
    if(returnValue == false) { return false }
    */
   /*
    x = document.forms["qcForm"]["splitSampleWeight"].value;
    returnValue = checkIfEntered(x, "Split Sample Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Split Sample Weight");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Split Sample Weight", 99999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["moistureRate"].value;
    returnValue = checkIfEntered(x, "Moisture");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Moisture");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Moisture", 999999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["turbidity"].value;
    returnValue = checkIfEntered(x, "Turbidity");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Turbidity");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Turbidity", 999999999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["kValue"].value;
    returnValue = checkIfSelected(x, "K Value");
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["kPan1"].value;
    returnValue = checkIfEntered(x, "Pan 1");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Pan 1");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Pan 1", 9999999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["kPan2"].value;
    returnValue = checkIfEntered(x, "Pan 2");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Pan 2");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Pan 2", 9999999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["kPan3"].value;
    returnValue = checkIfEntered(x, "Pan 3");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Pan 3");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Pan 3", 9999999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["roundness"].value;
    returnValue = checkIfEntered(x, "Roundness");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Roundness");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Roundness", 99999);
    if(returnValue == false) { return false }

    x = document.forms["qcForm"]["sphericity"].value;
    returnValue = checkIfEntered(x, "Sphericity");
    if(returnValue == false) { return false }
    returnValue = checkIfNumber(x, "Sphericity");
    if(returnValue == false) { return false }
    returnValue = checkIfLessThan(x, "Sphericity", 99999);
    if(returnValue == false) { return false }
    
    //validate the Sieve weights
    var sieveValues = document.getElementsByClassName("sieveRequired");
    for(var i = 0; i < sieveValues.length; i++)
    {
      if(sieveValues.item(i).value.length == 0)
      {
        alert("Sieve values need to be entered.");
        return false
      }
      if(sieveValues.item(i).value == 0)
      {
        alert("Sieve values cannot be 0.");
        return false
      }
    }
    
  }
  */
  return returnValue;
}

//calculate the average of the KPan values
function updateKPanAverage()
{
  var elementIteratorArray1 = null;
  var elementIteratorArray2 = null;
  var elementIteratorArray3 = null;
  var adjustedCurrentKValueSelected = null;
  var i = 0;
  
  //get the count of the number of K Value divs from the PHP
  var countOfKValueDivs = <?php echo count($kValueObjectArray); ?>;
  //console.log("DEBUG: countOfKValueDivs =" + countOfKValueDivs);
  
  //get the value of the currently selected K Value
  var currentKValueSelected = document.getElementById("kValue").value;
  //console.log("DEBUG: currentKValueSelected =" + currentKValueSelected);
    
  //adjust the value to a count (assumes that the values start at 4 and are sequential, which was correct when this was written)
  if(currentKValueSelected == "")
  {
    adjustedCurrentKValueSelected = 0;
  }
  else
  {
    adjustedCurrentKValueSelected = currentKValueSelected - 1;
  }
  //console.log("DEBUG: adjustedCurrentKValueSelected =" + adjustedCurrentKValueSelected);
  
  elementIteratorArray1 = document.getElementsByClassName("kPan1Calculation"); 
  elementIteratorArray2 = document.getElementsByClassName("kPan2Calculation"); 
  elementIteratorArray3 = document.getElementsByClassName("kPan3Calculation"); 
  
  var kPanAverage = (parseFloat(elementIteratorArray1[adjustedCurrentKValueSelected].innerHTML) + parseFloat(elementIteratorArray2[adjustedCurrentKValueSelected].innerHTML) + parseFloat(elementIteratorArray3[adjustedCurrentKValueSelected].innerHTML)) / 3;
  kPanAverage = Math.round(kPanAverage * 100) / 100; //round to 2 decimal places
  //console.log("DEBUG: kPanAverage =" + kPanAverage);
  
  if(kPanAverage <= 10)
  {
    document.getElementById("kPanAverageStatus").value = "This Crush test passes.";
  }
  else
  {
    document.getElementById("kPanAverageStatus").value = "This Crush test fails.";
  }
  document.getElementById("kPanAverage").value = kPanAverage + "%";
}

//perform the K Pan Calculations
function updateKPan1Calculation()
{
  var elementIteratorArray = null;
  var adjustedCurrentKValueSelected = null;
  var i = 0;
  
  //get the count of the number of K Value divs from the PHP
  var countOfKValueDivs = <?php echo count($kValueObjectArray); ?>;
  //console.log("DEBUG: countOfKValueDivs =" + countOfKValueDivs);
  
  //get the value of the currently selected K Value
  var currentKValueSelected = document.getElementById("kValue").value;
  //console.log("DEBUG: currentKValueSelected =" + currentKValueSelected);
    
  //adjust the value to a count (assumes that the values start at 4 and are sequential, which was correct when this was written)
  if(currentKValueSelected == "")
  {
    adjustedCurrentKValueSelected = 0;
  }
  else
  {
    adjustedCurrentKValueSelected = currentKValueSelected - 1;
  }
  //console.log("DEBUG: adjustedCurrentKValueSelected =" + adjustedCurrentKValueSelected);
  
  elementIteratorArray = document.getElementsByClassName("kPan1"); 
    
  var kPanInput = elementIteratorArray[adjustedCurrentKValueSelected].value;
  //console.log("DEBUG: kPanInput =" + kPanInput);
  
  var outputString = ((kPanInput / 40) * 100).toFixed(2) + "%";
  //console.log("DEBUG: outputString =" + outputString);
  
  elementIteratorArray = document.getElementsByClassName("kPan1Calculation");
  
  elementIteratorArray[adjustedCurrentKValueSelected].innerHTML = outputString;
  
  updateKPanAverage(); 
}

//perform the K Pan Calculations
function updateKPan2Calculation()
{
  var elementIteratorArray = null;
  var adjustedCurrentKValueSelected = null;
  var i = 0;
  
  //get the count of the number of K Value divs from the PHP
  var countOfKValueDivs = <?php echo count($kValueObjectArray); ?>;
  //console.log("DEBUG: countOfKValueDivs =" + countOfKValueDivs);
  
  //get the value of the currently selected K Value
  var currentKValueSelected = document.getElementById("kValue").value;
  //console.log("DEBUG: currentKValueSelected =" + currentKValueSelected);
    
  //adjust the value to a count (assumes that the values start at 4 and are sequential, which was correct when this was written)
  if(currentKValueSelected == "")
  {
    adjustedCurrentKValueSelected = 0;
  }
  else
  {
    adjustedCurrentKValueSelected = currentKValueSelected - 1;
  }
  //console.log("DEBUG: adjustedCurrentKValueSelected =" + adjustedCurrentKValueSelected);
  
  elementIteratorArray = document.getElementsByClassName("kPan2"); 
    
  var kPanInput = elementIteratorArray[adjustedCurrentKValueSelected].value;
  //console.log("DEBUG: kPanInput =" + kPanInput);
  
  var outputString = ((kPanInput / 40) * 100).toFixed(2) + "%";
  //console.log("DEBUG: outputString =" + outputString);
  
  elementIteratorArray = document.getElementsByClassName("kPan2Calculation");
  
  elementIteratorArray[adjustedCurrentKValueSelected].innerHTML = outputString;
  
  updateKPanAverage(); 
}

//perform the K Pan Calculations
function updateKPan3Calculation()
{
  var elementIteratorArray = null;
  var adjustedCurrentKValueSelected = null;
  var i = 0;
  
  //get the count of the number of K Value divs from the PHP
  var countOfKValueDivs = <?php echo count($kValueObjectArray); ?>;
  //console.log("DEBUG: countOfKValueDivs =" + countOfKValueDivs);
  
  //get the value of the currently selected K Value
  var currentKValueSelected = document.getElementById("kValue").value;
  //console.log("DEBUG: currentKValueSelected =" + currentKValueSelected);
    
  //adjust the value to a count (assumes that the values start at 4 and are sequential, which was correct when this was written)
  if(currentKValueSelected == "")
  {
    adjustedCurrentKValueSelected = 0;
  }
  else
  {
    adjustedCurrentKValueSelected = currentKValueSelected - 1;
  }
  //console.log("DEBUG: adjustedCurrentKValueSelected =" + adjustedCurrentKValueSelected);
  
  elementIteratorArray = document.getElementsByClassName("kPan3"); 
    
  var kPanInput = elementIteratorArray[adjustedCurrentKValueSelected].value;
  //console.log("DEBUG: kPanInput =" + kPanInput);
  
  var outputString = ((kPanInput / 40) * 100).toFixed(2) + "%";
  //console.log("DEBUG: outputString =" + outputString);
  
  elementIteratorArray = document.getElementsByClassName("kPan3Calculation");
  
  elementIteratorArray[adjustedCurrentKValueSelected].innerHTML = outputString;
  
  updateKPanAverage(); 
}

function calculateMoistureRate()
{
  console.log("DEBUG: calculateMoistureRate function called");
  
  var moistureRateValueRead = document.getElementById("moistureRate").value;
  
  if((moistureRateValueRead == 0) || (moistureRateValueRead == ""))
  {
  var preWashDryWeight = document.getElementById("preWashDryWeight").value;
    var beginningWeight = document.getElementById("beginningWetWeight").value;
    var moistureRateValue = Math.round((1 - (preWashDryWeight / beginningWeight)) * 10000) / 10000; //round to 4 decimal places

    console.log("DEBUG: Setting Moisture Rate to" + (moistureRateValue * 100) + "%");

    document.getElementById("moistureRate").value = ((moistureRateValue * 100)).toFixed(2) + "%";
    document.getElementById("moistureRate").placeholder = ((moistureRateValue) * 100) + "%";
    
  }
  
  var sieveCount = document.getElementById('sieveCount').value
  for (i = 1; i < sieveCount; i++) //iterate through all sieves
  {
    calculateFinalWeight(i); //update all of the other calculations as well 
  }
}

/******************************************************************************
* function calculateOrePercent()
* Date: 11-14-2017
* Author: Matt Nutsch
* Description: calculate and update the Ore Percent field.
*
******************************************************************************/
function calculateOrePercent()
{
  var preWashDryWeight = document.getElementById("preWashDryWeight").value;
  var postWashDryWeight = document.getElementById("postWashDryWeight").value;
  
  var orePercentInput = document.getElementById("orePercent");
  
  var orePercentValue = "";
  
  if((preWashDryWeight == 0) || (postWashDryWeight == 0))
  {
    orePercentValue = ""; 
  }
  else
  {
    orePercentValue = Math.round(parseFloat(postWashDryWeight / preWashDryWeight) * 10000) / 10000; //initially round to 4 decimal places while a decimal
    orePercentValue = (orePercentValue * 100).toFixed(2) + "%";  //round to 2 decimal places when converting to a percentage
  }
  orePercentInput.value = orePercentValue;  
}

//disable or enable wet weights based on the location
function enableDisableWetWeights()
{
  //get the value from the location
  var locationId = document.getElementById("locationId").value;

  if(locationId.length > 0)
  {
    //update the values
    var xmlhttp2 = new XMLHttpRequest();
    xmlhttp2.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {
        if(this.responseText == "1")
        {
          //accept wet weights
          document.getElementById("beginningWetWeight").style.display = "none";
          document.getElementById("preWashDryWeight").style.display = "none";
          document.getElementById("postWashDryWeight").style.display = "none";
          
          document.getElementById("beginningWetWeightLabel").style.display = "none";
          document.getElementById("preWashDryWeightLabel").style.display = "none";
          document.getElementById("postWashDryWeightLabel").style.display = "none";
          
          document.getElementById("beginningWetWeightWrapper").style.display = "none";
          document.getElementById("preWashDryWeightWrapper").style.display = "none";
          document.getElementById("postWashDryWeightWrapper").style.display = "none";
          
          wetWeightsEnabled = 0;         
        }
        else
        {
          //do not accept wet weights
          document.getElementById("beginningWetWeight").style.display = "block";
          document.getElementById("preWashDryWeight").style.display = "block";
          document.getElementById("postWashDryWeight").style.display = "block";
          
          document.getElementById("beginningWetWeightLabel").style.display = "block";
          document.getElementById("preWashDryWeightLabel").style.display = "block";
          document.getElementById("postWashDryWeightLabel").style.display = "block";
          
          document.getElementById("beginningWetWeightWrapper").style.display = "block";
          document.getElementById("preWashDryWeightWrapper").style.display = "block";
          document.getElementById("postWashDryWeightWrapper").style.display = "block";
          
          wetWeightsEnabled = 1;
        }
        
        //console.log("DEBUG: wetWeightsEnabled = " + wetWeightsEnabled);
      }
    }

    //get the content
    xmlhttp2.open("GET", "../../Includes/QC/wt_locationcheckwetweightsfor.php?locationId="+locationId, true);
    xmlhttp2.send();
  
  }
}

//update the final weight values in the Sieves table
function calculateFinalWeight(argNumber)
{ 
  //console.log("Calculate Final Rate (item) called");
  
  var startWeight = null;
  var endWeight = null;
  var finalWeight = null;
  
  if((document.getElementById('startWeight' + argNumber) != null) && (document.getElementById('endWeight' + argNumber) != null))
  {
    //update the final weight value
    startWeight = document.getElementById('startWeight' + argNumber).value;
    endWeight = document.getElementById('endWeight' + argNumber).value;
    finalWeight = startWeight = endWeight - startWeight;
    finalWeight = Math.round(finalWeight * 100) / 100;
    finalWeight = finalWeight.toFixed(2);
    //alert("DEBUG: Final Weight # " + argNumber + " calculated as " + finalWeight);
    document.getElementById('finalWeight' + argNumber).value = finalWeight;
    calculateTotalFinalWeight();
  }
}

//update the total final weight value in the Sieves table
function calculateTotalFinalWeight()
{
  //console.log("Calculate Total Final Weight called");
  
  try
  {
    var sieveCount = document.getElementById('sieveCount').value;
  }
  catch(err)
  {
    var sieveCount = 0;
    console.log("error: " + err);
  }
  var runningTotal = 0;
  var itemWeight = 0;
  for (i = 1; i < sieveCount; i++) 
  {   
    itemWeight = parseFloat(document.getElementById('finalWeight' + i).value);
    //console.log(runningTotal + " + " + itemWeight + " = " + (runningTotal + itemWeight));
    runningTotal = runningTotal + itemWeight;    
  }
  runningTotal = Math.round(runningTotal * 100) / 100;
  try
  {
    document.getElementById('totalFinalWeight').value = runningTotal;
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  calculateRates();
  
  updateOversizePercent();
  
  return runningTotal;
}

/*******************************************************************************
 * Name: setSplitWeight()
 * Author: Matt Nutsch
 * Date: 
 * Description:
 * This function sets the split weight value in the Sieve table to the input 
 * amount from the other tab
 * 
 ******************************************************************************/
function setSplitWeight()
{
  //console.log("Set Split Weight called");
  
  var splitWeightValue;
  try //set the split weight value in the Sieves table if the table is loaded and the value is already input
  {
    splitWeightValue = document.getElementById('splitSampleWeight').value;
    if(splitWeightValue.length == 0) //show a 0 instead of a blank when the user hasn't entered split weight yet
    {
      splitWeightValue = 0;
    }
    document.getElementById('splitWeightSieve').value = splitWeightValue;
    
    //update the delta between total final weight and split weight
    var deltaWeightValue = document.getElementById('totalFinalWeight').value - document.getElementById('splitWeightSieve').value;
    deltaWeightValue = Math.round(deltaWeightValue * 10) / 10; //round to 1 decimal
    deltaWeightValue = deltaWeightValue.toFixed(1);
    document.getElementById('deltaWeight').value = deltaWeightValue;
    
  }
  catch(err) 
  {
    //Sometimes this will run while the sieve table is not visible. We don't want to see error messages about this.
  }
}


/*******************************************************************************
 * Name: setLostValues()
 * Author: Matt Nutsch
 * Date: 
 * Description:
 * This function populates the Grams Lost and Percent Lost fields.
 * 
 ******************************************************************************/
function setLostValues()
{
  console.log("DEBUG: setLostValues called");
  
  //calculate grams lost
  var preWashDryWeight = document.getElementById('preWashDryWeight').value;
  var postWashWeight = document.getElementById('postWashDryWeight').value;
  var oversizeWeight = document.getElementById('oversizeWeight').value;
  if(oversizeWeight == "")
  {
    oversizeWeight = 0;
  }
  //console.log("DEBUG: preWashDryWeight == " + preWashDryWeight);
  //console.log("DEBUG: postWashWeight == " + postWashWeight);
  //console.log("DEBUG: oversizeWeight == " + oversizeWeight);
  
  var gramsLost = preWashDryWeight - postWashWeight;
  document.getElementById('gramsLost').value = gramsLost.toFixed(2);
  
  //calculate percent lost
  var percentLost = 1 - ((parseInt(postWashWeight) + parseInt(oversizeWeight)) / parseInt(preWashDryWeight));
  percentLost = Math.round(percentLost * 10000) / 10000; //round to 4 decimal places
  document.getElementById('percentLost').value = (percentLost * 100).toFixed(2) + "%";  
  
}


/*******************************************************************************
 * Name: calculateRates()
 * Author: Matt Nutsch
 * Date: 
 * Description:
 * This function populates the Percent Final column in the Edit Sample page.
 * 
 ******************************************************************************/
function calculateRates()
{
  try
  {
    var sieveCount = document.getElementById('sieveCount').value
  }
  catch(err)
  {
    console.log("error: " + err);
    var sieveCount = 0;
  }
  
  setLostValues();
  
  setSplitWeight(); 
  
  var preWashDryWeight = document.getElementById("preWashDryWeight").value;
  var beginningWeight = document.getElementById("beginningWetWeight").value;

  //calculate grams lost
  var postWashWeight = document.getElementById('postWashDryWeight').value;
  var preWashWeight = document.getElementById('preWashDryWeight').value;
  var gramsLost = preWashDryWeight - postWashWeight;
  
  var isCamsizer = null;
  try
  {
    isCamsizer = document.getElementById('isCamsizer').value;
  }
  catch (e) 
  {
    //console.log(e); // pass exception object to error handler
  }
  
  document.getElementById('gramsLost').value = gramsLost.toFixed(2);
  
  //calculate percent lost
  var oversizeWeight = document.getElementById('oversizeWeight').value;
  if(oversizeWeight == "")
  {
    oversizeWeight = 0;
  }
  var percentLost = 1 - ((parseInt(postWashWeight) + parseInt(oversizeWeight)) / parseInt(preWashDryWeight));
  //console.log("DEBUG: postWashWeight = " + postWashWeight);
  //console.log("DEBUG: preWashDryWeight = " + preWashDryWeight);
  percentLost = Math.round(percentLost * 10000) / 10000; //round to 4 decimal places
  //console.log("DEBUG: percentLost = " + percentLost);
  document.getElementById('percentLost').value = (percentLost * 100).toFixed(2) + "%"; 
  
  var moistureRateValueRead = document.getElementById("moistureRate").value;
  //console.log("DEBUG: moistureRateValueRead = " + moistureRateValueRead);
  if((moistureRateValueRead == 0) || (moistureRateValueRead == ""))
  {
    //calculate moisture rate - dev note: We don't call the calculateMoistureRate() function, because that would result in an infinite loop
    var moistureRateValue = Math.round((1 - (preWashDryWeight / beginningWeight)) * 10000) / 10000; //round to 4 decimal places
    //console.log("DEBUG: Setting Moisture Rate to" + (moistureRateValue * 100) + "%");
    document.getElementById("moistureRate").value = (moistureRateValue * 100).toFixed(2) + "%";
    document.getElementById("moistureRate").placeholder = (moistureRateValue * 100).toFixed(2) + "%";
  }
  else
  {
    if(!isNaN(moistureRateValueRead))
    {
      document.getElementById("moistureRate").value = (moistureRateValueRead * 100).toFixed(2) + "%";
    }
  }
  
  //calculate new split weight
  try
  {
    var totalFinalWeightForCalc = document.getElementById('totalFinalWeight').value;
  }
  catch(err)
  {
    console.log("error: " + err);
    var totalFinalWeightForCalc = 0;
  }
  //console.log("DEBUG: totalFinalWeightForCalc = " + totalFinalWeightForCalc);
  //console.log("DEBUG: percentLost = " + percentLost);
  var newSplit = totalFinalWeightForCalc * (1 + percentLost);
  //console.log("DEBUG: newSplit before rounding = " + newSplit);
  newSplit = Math.round(newSplit * 100) / 100; //round to 2 decimal places
  //console.log("DEBUG: newSplit after rounding = " + newSplit);
  document.getElementById('newSplit').value = newSplit;
  //console.log("DEBUG: newSplit field value after setting = " + document.getElementById('newSplit').value);
  
  //get the sieve number of the sieve with value == PAN
  var tempItem;
  var newPan = 0;
  var panLocation = 0;
  var panPercentageFirstPass = 0;
  var tempName;
  var itemFinalPercentFirstPass = 0;
  var totalFinalPercentFirstPass = 0;
  var plusSeventy = 0;
  var minusSeventy = 0;
  var negFortyPlusSeventy = 0;
  var negFiftyPlusOneForty = 0;
  var negSeventyPlusOneForty = 0;
  var negOneForty = 0;
  var orePercentInput;
  var orePercentValueAsNumber = 0;
  var tempValue = 0; //used as a temporary value for formatting
  var nearSize = 0;
  var selectedLocation; //used to determine if this is a Core Sample
  
  orePercentInput = document.getElementById("orePercent");
  orePercentValueAsNumber = orePercentInput.value;
  orePercentValueAsNumber = orePercentValueAsNumber.slice(0, -1); //trim the % sign
  orePercentValueAsNumber = orePercentValueAsNumber / 100; //convert to a decimal

  for (i = 1; i < sieveCount; i++) //iterate through all sieves
  {
    //calculate the percent finals
    if(checkIfCoreSample()) //if this is a core sample then use the regular weight * ore percent
    {
      itemFinalPercentFirstPass = parseFloat((document.getElementById('finalWeight' + i).value * orePercentValueAsNumber)) / parseFloat(totalFinalWeightForCalc);
    }
    else
    {
      if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
      {
        itemFinalPercentFirstPass = parseFloat(document.getElementById('finalWeight' + i).value) / parseFloat(newSplit);
      }
      else //otherwise use the regular weight
      {
        itemFinalPercentFirstPass = parseFloat(document.getElementById('finalWeight' + i).value) / parseFloat(totalFinalWeightForCalc);
      }
    }
    //set the percentage in the Sieve table
    //console.log("item " + i + " final percent first pass: " + itemFinalPercentFirstPass);
    totalFinalPercentFirstPass = totalFinalPercentFirstPass + itemFinalPercentFirstPass;    
    itemFinalPercentFirstPass = itemFinalPercentFirstPass * 100; //this is done to correct a problem with rounding in Javascript
    itemFinalPercentFirstPass = Math.round(itemFinalPercentFirstPass * 100) / 100; //round to 2 decimal places
    document.getElementById('percentFinal' + i).value = itemFinalPercentFirstPass + "%";
    itemFinalPercentFirstPass = itemFinalPercentFirstPass / 100; //this is done to correct a problem with rounding in Javascript
    
    //also look for the PAN row
    tempName = "screenSize" + i;
    tempItem = document.getElementById(tempName);
    //console.log(tempName + ": " + tempItem.value);
    if(tempItem.value.toUpperCase() == "PAN")
    {
      //console.log("PAN found at sieve item #: " + i);
      panLocation = i;
      panPercentageFirstPass = itemFinalPercentFirstPass;
    }
    
    //also update the range values while we are iterating through the table
    //calculate +70 = 70 and below screen size
    if(parseInt(tempItem.value) <= 70)
    {  
      //console.log("plusSeventy before = " + plusSeventy);
      plusSeventy = plusSeventy + parseFloat(document.getElementById('finalWeight' + i).value);
      //console.log("plusSeventy after = " + plusSeventy);
    }
    
    //calculate -70 = > 70
    if(parseInt(tempItem.value) > 70)
    {
      minusSeventy = minusSeventy + parseFloat(document.getElementById('finalWeight' + i).value);      
    }
    //dev note: PAN gets added in later
    
    //calculate -40 +70 = 70, 60, 50
    if(parseInt(tempItem.value) <= 70)
    {
      if(parseInt(tempItem.value) > 40)
      {
        negFortyPlusSeventy = negFortyPlusSeventy + parseFloat(document.getElementById('finalWeight' + i).value);
      }
    }
    
    //calculate -50 +140 = 60 - 140
    if(parseInt(tempItem.value) <= 140)
    {
      if(parseInt(tempItem.value) > 50)
      {
        negFiftyPlusOneForty = negFiftyPlusOneForty + parseFloat(document.getElementById('finalWeight' + i).value);
      }
    }
    
    //calculate -70 +140 = 80 - 140
    if(parseInt(tempItem.value) <= 140)
    {
      if(parseInt(tempItem.value) > 70)
      {
        negSeventyPlusOneForty = negSeventyPlusOneForty + parseFloat(document.getElementById('finalWeight' + i).value);
      }
    }
    
    //calculate -140 = 200, Pan
    if(parseInt(tempItem.value) > 140)
    {
      //console.log(tempName + ": " + tempItem.value + " = " + parseFloat(document.getElementById('finalWeight' + i).value));
      //console.log("negOneForty was = " + negOneForty);
      negOneForty = negOneForty + parseFloat(document.getElementById('finalWeight' + i).value);      
      //console.log("negOneForty now = " + negOneForty);
    }
    
    //calculate Near Size
    if((parseInt(tempItem.value) == 60) || (parseInt(tempItem.value) == 70) || (parseInt(tempItem.value) == 80))
    {
      //console.log(tempName + ": " + tempItem.value + " = " + parseFloat(document.getElementById('finalWeight' + i).value));
      //console.log("negOneForty was = " + negOneForty);
      nearSize = nearSize + parseFloat(document.getElementById('finalWeight' + i).value);      
      //console.log("negOneForty now = " + negOneForty);
    }
    
    //dev note: PAN gets added in later
  }
  
  //console.log("total final percent first pass: " + totalFinalPercentFirstPass);
  
  //calculate the new Pan value
  var percentToAddToPan = 1 - totalFinalPercentFirstPass;
  //console.log("percent to add to pan: " + percentToAddToPan);
  //console.log("Pan location: " + panLocation);
  
  //calculate the PAN final percentage differently if this is a Core Sample.
  selectedLocation = document.getElementById("locationId").options[document.getElementById("locationId").selectedIndex].text;
  if(selectedLocation == "Core Sample")
  {
    newPan = parseFloat(panPercentageFirstPass);
  }
  else
  {
    newPan = parseFloat(percentToAddToPan) + parseFloat(panPercentageFirstPass);
  }
  //console.log("New Pan: " + newPan);
  
  //set the new pan
  newPan = newPan * 100; //this is done to correct a problem with rounding in Javascript
  newPan = Math.round(newPan * 100) / 100; //round to 2 decimal places
  try
  {
    document.getElementById('percentFinal' + panLocation).value = newPan + "%";
    newPan = newPan / 100; //this is done to correct a problem with rounding in Javascript
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  //update the values under the Sieve table  
  //update +70
  //console.log("DEBUG: plusSeventy before rounding = " + plusSeventy);
  plusSeventy = Math.round(plusSeventy * 100) / 100;
  //console.log("DEBUG: plusSeventy after rounding = " + plusSeventy);
  if(checkIfCoreSample())
  {
    plusSeventy = plusSeventy / parseFloat(totalFinalWeightForCalc);
  }
  else
  {
    if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
    { 
      //convert to percent of new split
      plusSeventy = plusSeventy / document.getElementById('newSplit').value; 
    }
    else
    {
      plusSeventy = plusSeventy / parseFloat(totalFinalWeightForCalc);
    }
  }
  //console.log("DEBUG: plusSeventy as percent of split = " + document.getElementById('newSplit').value);
  //console.log("DEBUG: newSplit field value = " + plusSeventy);
  try
  {
    document.getElementById('plusSeventy').value = (plusSeventy * 100).toFixed(2) + "%";
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  //update -70
  minusSeventy = Math.round(minusSeventy * 100) / 100;
  if(checkIfCoreSample())
  {
    minusSeventy = minusSeventy / parseFloat(totalFinalWeightForCalc);
  }
  else
  {
    if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
    { 
      //convert to percent of new split
      minusSeventy = minusSeventy / document.getElementById('newSplit').value; 
    }
    else
    {
      minusSeventy = minusSeventy / parseFloat(totalFinalWeightForCalc);
    }
  }
  minusSeventy = minusSeventy + newPan;
  try
  {
    document.getElementById('minusSeventy').value = (minusSeventy * 100).toFixed(2) + "%";
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  //update -40 +70
  negFortyPlusSeventy = Math.round(negFortyPlusSeventy * 100) / 100;
  if(checkIfCoreSample())
  {
    negFortyPlusSeventy = negFortyPlusSeventy / parseFloat(totalFinalWeightForCalc);
  }
  else
  {
    if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
    { 
      //convert to percent of new split
      negFortyPlusSeventy = negFortyPlusSeventy / document.getElementById('newSplit').value; 
    }
    else
    {
      negFortyPlusSeventy = negFortyPlusSeventy / parseFloat(totalFinalWeightForCalc);
    }
  }
  try
  {
    document.getElementById('negFortyPlusSeventy').value = (negFortyPlusSeventy * 100).toFixed(2) + "%";
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  //update -50 +140
  negFiftyPlusOneForty = Math.round(negFiftyPlusOneForty * 100) / 100;
  if(checkIfCoreSample())
  {
    negFiftyPlusOneForty = negFiftyPlusOneForty / parseFloat(totalFinalWeightForCalc);
  }
  else
  {
    if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
    { 
      //convert to percent of new split
      negFiftyPlusOneForty = negFiftyPlusOneForty / document.getElementById('newSplit').value; 
    }
    else
    {
      negFiftyPlusOneForty = negFiftyPlusOneForty / parseFloat(totalFinalWeightForCalc);
    }
  }
  try
  {
    document.getElementById('negFiftyPlusOneForty').value = (negFiftyPlusOneForty * 100).toFixed(2) + "%";
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  //update -70 +140
  negSeventyPlusOneForty = Math.round(negSeventyPlusOneForty * 100) / 100;
  if(checkIfCoreSample())
  {
    negSeventyPlusOneForty = negSeventyPlusOneForty / parseFloat(totalFinalWeightForCalc);
  }
  else
  {
    if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
    { 
      //convert to percent of new split
      negSeventyPlusOneForty = negSeventyPlusOneForty / document.getElementById('newSplit').value; 
    }
    else
    {
      negSeventyPlusOneForty = negSeventyPlusOneForty / parseFloat(totalFinalWeightForCalc);
    }
  }
  try
  {
    document.getElementById('negSeventyPlusOneForty').value = (negSeventyPlusOneForty * 100).toFixed(2) + "%";
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  //update -140
  //console.log("negOneForty before rounding = " + negOneForty);
  negOneForty = Math.round(negOneForty * 100) / 100;
  //console.log("negOneForty after rounding = " + negOneForty);
  if(checkIfCoreSample())
  {
    negOneForty = negOneForty / parseFloat(totalFinalWeightForCalc);
  }
  else
  {
    if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
    { 
      //convert to percent of new split
      negOneForty = negOneForty / document.getElementById('newSplit').value; 
    }
    else
    {
      negOneForty = negOneForty / parseFloat(totalFinalWeightForCalc);
    }
  }
  negOneForty = negOneForty + newPan;
  //console.log("newPan = " + newPan);
  //console.log("negOneForty as percent = " + negOneForty);
  try
  {
    document.getElementById('negOneForty').value = (negOneForty * 100).toFixed(2) + "%";
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
  //update Near Size
  nearSize = Math.round(nearSize * 100) / 100;
  if(checkIfCoreSample())
  {
    nearSize = nearSize / parseFloat(totalFinalWeightForCalc);
  }
  else
  {
    if(wetWeightsEnabled == 1) //if this is not a core sample AND wet weights are enabled then use the new split
    { 
      //convert to percent of new split
      nearSize = nearSize / document.getElementById('newSplit').value; 
    }
    else
    {
      nearSize = nearSize / parseFloat(totalFinalWeightForCalc);
    }
  }
  try
  {
    document.getElementById('nearSize').value = (nearSize * 100).toFixed(2) + "%";
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
}

/*******************************************************************************
 * Name: changeBlankSieveRangesToNA()
 * Author: Matt Nutsch
 * Date: 11-2-2017
 * Description:
 * This function changes the value of any blank sieve ranges to "NA".
 * 
 ******************************************************************************/
function changeBlankSieveRangesToNA()
{
  var plusSeventy = document.getElementById('plusSeventy'); //plusSeventy
  var minusSeventy = document.getElementById('minusSeventy'); //minusSeventy
  var negFortyPlusSeventy = document.getElementById('negFortyPlusSeventy'); //negFortyPlusSeventy
  var negFiftyPlusOneForty = document.getElementById('negFiftyPlusOneForty'); //negFiftyPlusOneForty
  var negSeventyPlusOneForty = document.getElementById('negSeventyPlusOneForty'); //negSeventyPlusOneForty
  var negOneForty = document.getElementById('negOneForty'); //negOneForty
  
  if(plusSeventy.value == "0.00%")
  {
    plusSeventy.value = "NA";
  }
  
  if(minusSeventy.value == "0.00%")
  {
    minusSeventy.value = "NA";
  }
  
  if(negFortyPlusSeventy.value == "0.00%")
  {
    negFortyPlusSeventy.value = "NA";
  }
  
  if(negFiftyPlusOneForty.value == "0.00%")
  {
    negFiftyPlusOneForty.value = "NA";
  }
  
  if(negSeventyPlusOneForty.value == "0.00%")
  {
    negSeventyPlusOneForty.value = "NA";
  }
  
  if(negOneForty.value == "0.00%")
  {
    negOneForty.value = "NA";
  }
  
}

/*******************************************************************************
 * Name: updateLocationSpan()
 * Author: Matt Nutsch
 * Date: 
 * Description:
 * This function changes updates the location span at the top of the page.
 * 
 ******************************************************************************/
function updateLocationSpan()
{
  //console.log("updateLocationSpan called");
  var sampleLocationSpan = document.getElementById('sampleLocationSpan');
  var sampleLocation = document.getElementById('locationId');
  //console.log("sampleLocation.options[sampleLocation.selectedIndex].text = " + sampleLocation.options[sampleLocation.selectedIndex].text);
  try
  {
    sampleLocationSpan.textContent = sampleLocation.options[sampleLocation.selectedIndex].text;
  }
  catch(err)
  {
    console.log("error: " + err);
  }
  
}

/*******************************************************************************
 * Name: updateDateTimeSpan()
 * Author: Matt Nutsch
 * Date: 
 * Description:
 * This function changes updates the datetime span at the top of the page.
 * 
 ******************************************************************************/
function updateDateTimeSpan()
{
  console.log("updateDateTimeSpan called");
  var dateTimeSpan = document.getElementById('dateTimeSpan');
  var dateTimeField = document.getElementById('dt');
  console.log("dateTimeField.value = " + dateTimeField.value);
  dateTimeSpan.textContent = dateTimeField.value;
  console.log("dateTimeSpan.textContent = " + dateTimeSpan.textContent);
}
</script>
<?php

/***************************************
* Name: function test_input($data) 
* Description: This function removes harmful characters from input.
* Source: https://www.w3schools.com/php/php_form_validation.asp
****************************************/
function test_input($data) 
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
//====================================================================== END PHP
?>

<!-- HTML -->





