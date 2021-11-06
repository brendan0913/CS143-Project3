<?php
    // get the id parameter from the request
    $id = intval($_GET['id']);

    // set the Content-Type header to JSON, 
    // so that the client knows that we are returning JSON data
    header('Content-Type: application/json');

    /*
    Send the following fake JSON as the result
        {
            "id":"745",
            "givenName":{ "en":"A. Michael" },
            "familyName":{ "en":"Spence" },
            "gender":"male",
            "birth":{
                "date":"1943-00-00",
                "place":{
                    "city":{ "en":"Montclair, NJ" },
                    "country":{ "en":"USA" }
                }
            },
            "nobelPrizes":[{
                "awardYear":"2001",
                "category":{ "en":"Economic Sciences" },
                "sortOrder":"2",
                "affiliations":[{
                    "name":{ "en":"Stanford University" },
                    "city":{ "en":"Stanford, CA" },
                    "country":{ "en":"USA" }
                }]
            }]
        }
    */

    $db = new mysqli('localhost', 'cs143', '', 'class_db');
    if ($db->connect_errno > 0) 
        die('Unable to connect to database [' . $db->connect_error . ']');

    $is_org = True;

    $query = "SELECT * FROM Organization WHERE lid = $id";
    $rs = $db->query($query);
    while ($row = $rs->fetch_assoc()) { 
        $name = $row['orgName']; 
    }
    $rs->free();

    if (is_null($name)){
        $is_org = False;
        $query = "SELECT * FROM Person WHERE lid = $id";
        $rs = $db->query($query);
        while ($row = $rs->fetch_assoc()) { 
            $first = $row['givenName']; 
            $last = $row['familyName'];
            $gender = $row['gender']; 
        }
        $rs->free();
    }

    $query = "SELECT * FROM Birth WHERE lid = $id";
    $rs = $db->query($query);
    while ($row = $rs->fetch_assoc()) { 
        $date = $row['date']; 
        $city = $row['city'];
        $country = $row['country']; 
    }
    $rs->free();

    $has_founded = False;
    $has_place = False;
    $has_date = False;
    if (!is_null($city) || !is_null($country) || !is_null($date)){
        $founded = array();
        $has_founded = True;
    }
    if (!is_null($date)){
        $has_date = True;
        $founded["date"] = $date;
    }
    if (!is_null($city) || !is_null($country)){
        $has_place = True;
        $place = array();
        if (!is_null($city)){
            $place["city"] = (object) ["en" => $city];
        }
        if (!is_null($country)){
            $place["country"] = (object) ["en" => $country];
        }
        $place = (object) $place;
        $founded["place"] = $place;
        $founded = (object) $founded;
    }
    $nobelPrizes = array();
    $prize_ids = array();
    $query = "SELECT nid FROM Laureate WHERE lid = $id";
    $rs = $db->query($query);
    while ($row = $rs->fetch_array()){
        $prize_ids[] = $row[0];
    }
    foreach($prize_ids as $nid){
        $query = "SELECT * FROM Prize WHERE nid = $nid";
        $rs = $db->query($query);
        while ($row = $rs->fetch_assoc()) { 
            $awardYear = $row['awardYear']; 
            $cat = (object) [ "en" => $row['category']];
            $sortOrder = $row['sortOrder']; 
        }
        $rs->free();
        $prizeInfo = array();
        $prizeInfo["awardYear"] = $awardYear;
        $prizeInfo["category"] = $cat;
        $prizeInfo["sortOrder"] = $sortOrder;
        $prizeInfo = (object) $prizeInfo;
        array_push($nobelPrizes, $prizeInfo);
    }
    


    if ($is_org){
        $output = array("id" => strval($id), "orgName" => $name);
        if ($has_founded){
            $output["founded"] = $founded;
        }
        $output["nobelPrizes"] = $nobelPrizes;
        $output = (object) $output;
    }
    else {
        $output = array("id" => strval($id));
        if (!is_null($first)){
            $output["givenName"] = $first;
        }
        if (!is_null($last)){
            $output["familyName"] = $last;
        }
        if ($has_founded){
            $output["birth"] = $founded;
        }
        $output["nobelPrizes"] = $nobelPrizes;
        $output = (object) $output;
    }
    echo json_encode($output);
?>