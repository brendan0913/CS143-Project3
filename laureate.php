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
        $prizeInfo = array();
        $prizeInfo["awardYear"] = $awardYear;
        $prizeInfo["category"] = $cat;
        $prizeInfo["sortOrder"] = $sortOrder;

        $aids = array();
        $query = "SELECT affilId FROM Prize WHERE nid = $nid";
        $rs = $db->query($query);
        while ($row = $rs->fetch_array()){
            $aids[] = $row[0];
        }
        $rs->free();
        $affils = array();
        if (!is_null($aids[0])){
            foreach($aids as $aid){
                $query = "SELECT * FROM Affiliation WHERE affilId = $aid";
                $rs = $db->query($query);
                while ($row = $rs->fetch_assoc()){
                    $name = (object) [ "en" => $row['name']];
                    $has_city = False;
                    $has_country = False;
                    if (!is_null($row['city'])){
                        $city = (object) [ "en" => $row['city']];
                        $has_city = True;
                    }
                    if (!is_null($row['country'])){
                        $country = (object) [ "en" => $row['country']];
                        $has_country = True;
                    }
                }
                $affilInfo = array();
                $affilInfo["name"] = $name;
                if ($has_city){
                    $affilInfo["city"] = $city;
                }
                if ($has_country){
                    $affilInfo["country"] = $country;
                }
                $rs->free();
                array_push($affils, $affilInfo);
            }
            $prizeInfo["affiliations"] = $affils;
        }
        $prizeInfo = (object) $prizeInfo;
        array_push($nobelPrizes, $prizeInfo);
    }

    if ($is_org){
        $output = array("id" => strval($id), "orgName" => (object) ["en" => $name]);
        if ($has_founded){
            $output["founded"] = $founded;
        }
        $output["nobelPrizes"] = $nobelPrizes;
        $output = (object) $output;
    }
    else {
        $output = array("id" => strval($id));
        if (!is_null($first)){
            $output["givenName"] = (object) ["en" => $first];
        }
        if (!is_null($last)){
            $output["familyName"] = (object) ["en" => $last];
        }
        if (!is_null($gender)){
            $output["gender"] = $gender;
        }
        if ($has_founded){
            $output["birth"] = $founded;
        }
        $output["nobelPrizes"] = $nobelPrizes;
        $output = (object) $output;
    }
    echo json_encode($output);
?>