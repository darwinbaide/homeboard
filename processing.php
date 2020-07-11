<?php
# Start of Data aquisition


$version = $_POST['version'];// grabs the version to see what function to go into
//$version="test";



if($version == "directory"){
    $htmlText="<div class='jumbotron text-center' style='margin-top: 10px; max-width: 700px;'><h1>New Pending Work</h1></div> ";
    $result = SQLSend('call BidSource.BSgetDirectory();');
    while($row = mysqli_fetch_array($result)){
    $htmlText.='<div class="row" style="margin-left: 10px;"><div class="col-sm-10 alert alert-primary"><b>'.$row[2]."</b> | ".$row[4].' <span class="badge badge-secondary">'.$row[1].'</span></div>';
    $htmlText.='<div class=""><button type="button" class="btn btn-lg btn-success" onclick="openSite('.$row[0].')">Open</button></div></div>';
    }
    $arr = array('Done' => 'yes', 'errors' => "",'fileName'=>"test", 'Data' =>$htmlText);// sends back data to display 
    echo json_encode($arr);// sends the response with correct json
}
if($version== "work"){
    $websiteID=$_POST['websiteID'];
    $requestType=$_POST['type'];
    //$websiteID="279";
    $frameIT="no";
    if($requestType == "1"){
        $command="SELECT * FROM BidSource.BSProcessPending where bswebsitemapid = ".$websiteID. " and (Status = 1 or Status =2);";
    }else{
            $command="SELECT * FROM BidSource.BSProcessPending where bswebsitemapid = ".$websiteID. " and (Status = 1 or Status =2 or Status =3);";
        }
    
    
    $htmlStatus="1";
    $htmlPending="";
    $htmlContent="";
    $FirstLine="";
    $htmlText='<h3 class="text-center">Files<a href="javascript:void(0)" class="closebtn text-dark" onclick="closeNav()">&times;</a></h3>    <div class="form-check text-center">    <input type="checkbox" class="form-check-input" id="requestType" onclick="changeRequest();">    <label class="form-check-label" for="requestType">Show Approved</label></div>';
    $result = SQLSend($command);
    while($row = mysqli_fetch_array($result)){
        if($row[5]==1){
        
        if($htmlContent==""){
            $htmlContent=$row[2];
            $htmlPending=$row[0];
            $htmlText.='<a class="btn btn-primary " name="'.$row[2].'" onclick="closeNav(); linkClick(\''.$row[2].'\'); ">'.$row[2].'</a>';
        }else{
            $htmlText.='<a class="btn btn-danger " name="'.$row[2].'"  onclick="closeNav();  linkClick(\''.$row[2].'\'); ">'.$row[2].'</a>';
        }
    }else if($row[5]==2){
            $htmlText.='<a class="btn btn-warning " name="'.$row[2].'"  onclick="closeNav(); linkClick(\''.$row[2].'\'); ">'.$row[2].'</a>';
        }else{
            $htmlText.='<a class="btn btn-success " name="'.$row[2].'"  onclick="closeNav(); linkClick(\''.$row[2].'\'); ">'.$row[2].'</a>';
        }
    }
    $nameFile=$htmlContent;
    if(strpos($htmlContent, '.') == false){
        $filename="/media/bidsource/pending/".$websiteID."/".$htmlContent;
        //$filename="/media/bidsource/pending/279/b3b25d414c2d46549c5cc8aeec6a403c";
        $htmlContent = file_get_contents($filename);
        $FirstLine = trim(fgets(fopen($filename, 'r')));
    }else{
     $frameIT="yes";// if it has a file extension then it needs to be framed   
     $initial="/media/bidsource/pending/".$websiteID."/".$htmlContent;
     copy($initial, "/var/www/html/darwin/temp/".$htmlContent);
     $htmlContent= "darwin/temp/".$htmlContent;
    }
    //echo $htmlContent;
    $command='UPDATE `BidSource`.`BSProcessPending`SET `Status` = 2, `processstartdatetime` = now() WHERE `PendingID` = '.$htmlPending.' ;';
    SQLSend($command);
    $arr = array('Done' => 'yes', 'errors' => "",'fileName'=>"test", 'Data' =>$htmlText, 'Content' =>$htmlContent, 'status' =>$htmlStatus, 'frame' =>$frameIT, 'pending' =>$htmlPending, 'filename' =>$nameFile, 'firstLine' =>$FirstLine);// sends back data to display 
    echo json_encode($arr);// sends the response with correct json



}

if($version== "insert"){
    $codeName= $_POST['codeName'];
    $codeDescription= $_POST['codeDescription'];
    $codeKeywords= $_POST['codeKeywords'];
    $codeRelated= $_POST['codeRelated'];
    $codeLanguage= $_POST['codeLanguage'];
    $codeVersion= $_POST['codeVersion'];
    $codeCode= $_POST['codeCode'];
    $codeFile= $_POST['codeFile'];
    $codeLocation= $_POST['codeLocation'];
    $command='INSERT INTO `codestorage`.`codefiles`(`codeName`,`keywords`,`description`,`relatedTopics`,`language`,`version`,`active`,`example`,`exampleFile`,`exampleLocation`)
    VALUES("'.$codeName.'","'.$codeKeywords.'","'.$codeDescription.'","'.$codeRelated.'","'.$codeLanguage.'","'.$codeVersion.'",1,"'.$codeCode.'","'.$codeFile.'","'.$codeLocation.'");';
    SQLSend($command);
    $arr = array('Done' => 'yes');// sends back data to display 
    echo json_encode($arr);// sends the response with correct json
    

}

if($version== "junk"){
    $pending= $_POST['pending'];
    $status1= $_POST['status'];
    $command='UPDATE `BidSource`.`BSProcessPending`SET `Status` = '.$status1.', `processstartdatetime` = null WHERE `PendingID` = '.$pending.' ;';
    SQLSend($command);

}

if($version== "test"){
  
    $command='SELECT * FROM codestorage.codefiles;';
    $result=SQLSend($command);
    while($row = mysqli_fetch_array($result)){
        var_dump($row);    
    }
    
}



function SQLSend($query){
    
    $dbhost = "127.0.0.1:3306";
    $dbuser = "darwin";
    $dbpass = "Db142493";
    $dbname = "codestorage";
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    /* check connection */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }
    /* Select queries return a resultset */
    $result = $mysqli->query($query);
    $mysqli->close();
    return $result;
    }

?>


