<html>
<head>
<style>
    .STATUSON{
        background: rgb(0, 255, 0);
        border-radius: 50%;
        display: inline-block;
        height: 20px;
        margin-left: 4px;
        margin-right: 4px;
        width: 20px;
    }
    .STATUSOFF{
        background: rgb(255, 0, 0);
        border-radius: 50%;
        display: inline-block;
        height: 20px;
        margin-left: 4px;
        margin-right: 4px;
        width: 20px;
    }
</style>
</head>
<body>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
    function doON(rValue) {
        $.ajax({
            url: "esi.php",
            type: "POST",
            data:  'doON=' + rValue,
        });
    }
    function doOFF(rValue) {
        $.ajax({
            url: "esi.php",
            type: "POST",
            data:  'doOFF=' + rValue,
        });
    }

    $(document).ready(function(){
        $.ajax({
            url: "esi.php",
            type: "POST",
            data:  'update=k',
            success: function(data) {
                $('#output').html(data);
            },
        });
    });

    function loadNowPlaying(){
        $.ajax({
            url: "esi.php",
            type: "POST",
            data:  'update=k',
            success: function(data) {
                $('#output').html(data);
            },
        });
    }
    setInterval(function(){loadNowPlaying()}, 10000);

</script>

<?php

function OnOffService($SET, $SERVICE){
    $db = new mysqli('localhost', 'root', '123!@#qaZ', 'test');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    $sql = 'UPDATE `service_statuses` SET status='.$SET.' WHERE service_name="'.$SERVICE.'"';

    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
}

// catch AJAX backend for manually testing
if(isset($_POST['doON'])) {
    $SERVICE = $_POST['doON'];
    OnOffService("1", $SERVICE);
    // lame hack? stop rest of page from loading and just do JS backend.
    // TODO: move this to it's own PHP file for backend work.
    die();
}
else if(isset($_POST['doOFF'])) {
    $SERVICE = $_POST['doOFF'];
    OnOffService("0", $SERVICE);
    // lame hack? stop rest of page from loading and just do JS backend.
    // TODO: move this to it's own PHP file for backend work.
    die();
}
else if(isset($_POST['update'])) {
    UpdateServiceStatus();
}

// function used to call the services and their statues
function RunServiceStatusQuery($SERVICE){
    $db = new mysqli('localhost', 'root', '123!@#qaZ', 'test');
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $sql = "SELECT * FROM `service_statuses` WHERE service_name='".$SERVICE."'";

    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
    while($row = $result->fetch_assoc()){
        // are we on or off?
        if($row['status']==1){
            $STATUS = "<td class=''><span class=\"STATUSON\"></span></td>";
            $SWITCH = '<td class=\'\'><a href="#" id="message-content" onclick="doOFF(\''.$SERVICE.'\')">SWITCH</a></td>';
        }
        else {
            $STATUS = "<td class=''><span class=\"STATUSOFF\"></span></td>";
            $SWITCH = '<td class=\'\'><a href="#" id="message-content" onclick="doON(\''.$SERVICE.'\')">SWITCH</a></td>';
        }

        echo "<tr><td>" . $row['service_name'] ."</td>". $STATUS ."". $SWITCH ."</tr>";
    }
    $result->free();
}

function UpdateServiceStatus(){
    /* [ SERVICE TABLE START ] */
    echo"<table>";
    RunServiceStatusQuery("SSH");
    RunServiceStatusQuery("FTP");
    RunServiceStatusQuery("HTTP");
    RunServiceStatusQuery("TELNET");
    RunServiceStatusQuery("SNMP");
    echo"</table>";
    /* [ SERVICE TABLE END ] */
}


echo'<br><div id="output"></div>';




?>
</body>
</html>
