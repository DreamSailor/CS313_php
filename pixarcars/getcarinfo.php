<?php
    // Start the session
    session_start();
    include "database.php";
    include "helper.php";
    

/**************************************
 * 
 *  File: GetCarInfo.php
 *  Created by: jsimpson
 *  Date: May 18, 2016 7:01:44 PM
 *  Description: FIle to access DB and get information
 * 
 ****************************************/

?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/styles.css"/>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script src="../js/main.js"></script>
  
        <title>My Pixar Car Collection</title>
    </head>
    <body>

        <?php
            //Open the DB
            $db = OpenDB("pixar_cars");
            
            //Get Value from dropdown and then store it in session variable too
            $carIndex =  $_POST['cars'];            
            $_SESSION["currRecord"] = $carIndex;
           
            
            $statement = "SELECT id, name,description,date_aquired,"
                    . "primary_version,primary_car_id,race_car,race_number,race_sponsor "
                    . "FROM cars WHERE id=$db->quote($carIndex)";
            
            $carInfo = dbRead($db,$statement);
            sessionWrite($carInfo);
            
            //make sure we only got one and display info
            if(count($carInfo) == 1)
            {
                echo "<b>Car: ". $carInfo[0]["name"] ."</b>"
                . "<span class='bg-primary pull-right smallpad'>Bought on:  " 
                . $carInfo[0]["date_aquired"]."</span><br>"
                ."<strong>Description:</strong> " . $carInfo[0]["description"] ."<br>";
                

                if($carInfo[0]["primary_version"] == 1)
                     echo "<strong>Primary Car:</strong> " ."Yes<br/>";
                else
                     echo "<strong>Primary Car:</strong> " ."No<br/>";

                //Check if Car is a race car or not
                if($carInfo[0]["race_car"]==1)
                {
                    echo "<div class='row'><strong>\n"
                    . "<div class='col-sm-4' >RaceCar</div>\n<div class='col-sm-4 text-center' >Number</div>\n"
                    . "<div class='col-sm-4 text-center' >Sponsor</div>\n</strong></div>\n";
                                        
                    echo "<div class='row'>\n"
                    . "<div class='col-sm-4' >Yes</div\n>"
                    . "<div class='col-sm-4 text-center' >" . $carInfo[0]['race_number']. "</div>\n"
                    . "<div class='col-sm-4 text-center' >" . $carInfo[0]['race_sponsor'] 
                    ."</div>\n</div>\n";   
        
                }
                else
                {
                    echo "<div class='row'><strong>\n"
                    . "<div class='col-sm-4 ' >RaceCar</div>\n"
                    . "<div class='col-sm-8 ' ></div>\n</strong></div>\n";
                                        
                    echo "<div class='row'>\n". "<div class='col-sm-4' >No</div>\n"
                    ."<div class='col-sm-8 ' ></div></div>\n";  
                }
                echo "<br/>";

            }
        ?>
        
        <div class="row">
            <div class="col-sm-1 " ></div>
            <div class="col-sm-4" >
                <?php
                    //  Add image from Image table path
                    if(count($carInfo) == 1)                   
                    {
                        $imageInfo = dbRead($db,"SELECT folderpath, name FROM image WHERE car_id=$db->quote($carIndex)"); 
                        if(empty($imageInfo))
                            $imageFile = "./carshots/noimage.jpg";  //if not use a place holder
                        else
                            $imageFile = $imageInfo[0]["folderpath"] ."/" .$imageInfo[0]["name"];
                     
   
                         echo "<img class='img-thumbnail' src='" 
                        .$imageFile ."'height='225' width='225' /><br>";
                        

                    }

                ?>
                
            </div>
            <div class="col-sm-1 " > </div>
            <div class="col-sm-3 ">
                <?php
                
                    //Added infor for Friends from Many-to-Many query
                    if(count($carInfo) == 1)                   
                    {
                       echo "<strong>Friends:</strong><br/>";
                       
                       $statement1 = $db->query("SELECT c.name FROM cars as c "
                               . "LEFT JOIN friend_bridge AS fb ON "
                               . "fb.friend_id = c.id WHERE fb.car_id = "
                               . "$db->quote($carIndex)");
                       $friendInfo = $statement1->fetchAll(PDO::FETCH_ASSOC);
                       foreach($friendInfo as $row)
                       {
                           echo $row['name']. "<br/>";
                           
                            
                       }
                    }

                ?>    
               
            </div>
            <div class="col-sm-3 " >
                <?php 
                
                    //Added info for Locations from Many-to-Many query
                    if(count($carInfo) == 1)                   
                    {
                        echo "<strong>Locations:</strong><br>";
                       $statement2 = $db->query("SELECT l.name,l.country FROM "
                               . "location as l LEFT JOIN location_car_bridge "
                               . "AS lcb ON lcb.place_id = l.id WHERE lcb.car_id"
                               . " = $db->quote($carIndex)");
                       $locationInfo = $statement2->fetchAll(PDO::FETCH_ASSOC);
                       foreach($locationInfo as $row)
                       {
                           echo $row['name']. " - " . $row['country']. "<br/>";
                           
                           
                       }
                    }
                ?>
                
            </div>
      
        </div>
        
        <?php $db = null;  //Close out the DB ?>       
        
    </body>
</html>
