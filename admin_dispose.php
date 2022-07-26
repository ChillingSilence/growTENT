<?php
include 'tpl/auth.php';
include 'tpl/sql.php';
$plant = $_POST['plantid'];

// Set the date here just to be helpful for later db queries
$date = date('Y-m-d');

if($_POST['submitdisposal']=='submitdisposal'){
	// The first thing we want to do is add a note for the plant so it'll show up in the View Plant history
        $updatesql="INSERT INTO plant_notes (plant_uniqueid, note_date, notes) VALUES ('$plant', '$date', 'Plant has been disposed of')";
        // Now we submit the initial disposal note for the plant into the database
        if ($result = mysqli_query($con, $updatesql)) {
                $savesuccess = 'true';
                }
	// Grab the disposal reason
	$disposal_reason = filter_var($_POST['disposal_reason'], FILTER_SANITIZE_STRING);
	// Then we do it again, setting the Inventory status so it's marked as no longer being alive
        $updatesql="UPDATE inventory SET current_state='Disposed of - $disposal_reason',is_alive='0' WHERE plant_uniqueid='$plant'";
	// Then pop it into the DB
        if ($result = mysqli_query($con, $updatesql)) {
                $savesuccess = 'true';
                }
        }


// Now we check to see if we've been given additional notes to save, submit it to db if-so:
if (strlen($_POST['newnotes'] > 1)) {
        // We've got something submitted, so check the length of newnotes
        $newnotes = filter_var($_POST['newnotes'], FILTER_SANITIZE_STRING);
        if (strlen($newnotes > 1 )) {
                $sql="INSERT INTO plant_notes (plant_uniqueid, note_date, notes) VALUES ('$plant', '$date', '$newnotes')";
                if ($result = mysqli_query($con, $sql)) {
                        // echo "Returned rows are: " . mysqli_num_rows($result);
                        // Free result set
                        //mysqli_free_result($result);
                        $savesuccess = 'true';
                        }
                }
        else {
                $savesuccess = 'failed';
                }
        }


$sql = "SELECT * from inventory where plant_uniqueid = '$plant'";
$result = mysqli_query($con,$sql);
$plantresults = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Update the cultivar
$cultivar = $plantresults[0]["cultivar"];

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>growTENT :: Disposal</title>

    <!-- Sets initial viewport load and disables zooming  -->
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">

    <!-- Makes your prototype chrome-less once bookmarked to your phone's home screen -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- Include the compiled Ratchet CSS -->
    <link rel="stylesheet" href="ratchet-theme-ios.min.css">
    <link rel="stylesheet" href="ratchet.min.css">
    <link rel="stylesheet" href="app.css">

    <!-- Include the compiled Ratchet JS -->
    <!-- <script src="ratchet.js"></script> -->
    <script src="html5-qrcode.min.js"></script>
  </head>
  <body>

    <!-- Make sure all your bars are the first things in your <body> -->
    <header class="bar bar-nav">
     <a href="admin.php"><button class="btn btn-link btn-nav pull-left">
       <span class="icon icon-home"></span>
       Home
     </button></a>

      <h1 class="title">Plant disposal</h1>
    </header>

    <!-- Wrap all non-bar HTML in the .content div (this is actually what scrolls) -->
    <div class="content">
      <p class="content-padded" align='center'>Disposal of complete plants and removing them from the registry</p>
<?php if($savesuccess=='true'){ echo "<p class='content-padded' align='center'><font color='red'>Saved!</font></p>";} ?>
      <div class="card">
	<p>This is where the content goes.</p>
        <form action='admin_dispose.php' method='post' class='input-group'>
         <div class='input-row'>
          <label>Cultivar: </label>
          <input type='text' placeholder='Cultivar' name='cultivar' readonly value='<?php echo $cultivar; ?>'>
         </div>
         <div class='input-row'>
          <label>Plant UID: </label>
          <input type='text' placeholder='Plant Unique ID' name='plantid' id='plantid' readonly value='<?php echo $plant; ?>'>
         </div>
	<p class='content-padded'>Select disposal reason: <br />
        <select name='disposal_reason' id='disposal_reason' style='margin-top: 3px; margin-bottom: 3px;'>
<?php
        foreach($disposalreasons as $currentreason) {
                        echo "        <option value='" . $currentreason . "'>" . $currentreason . "</option>\n";
                        }
?>
        </select>
	 </p>
         <div class='content-padded'><label>Additional notes: </label>
          <textarea name="newnotes" id="newnotes" maxlength="2048" rows="3"></textarea></div>
        <button class='btn btn-positive btn-block' type='submit' name='submitdisposal' value='submitdisposal'>Submit disposal</button>
      </div>
    </div>

  </body>
</html>