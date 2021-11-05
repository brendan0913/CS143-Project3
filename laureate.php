<?php
    // get the id parameter from the request
    $id = intval($_GET['id']);

    // set the Content-Type header to JSON, 
    // so that the client knows that we are returning JSON data
    header('Content-Type: application/json');

    /*
    Send the following fake JSON as the result
    {  "id": $id,
        "givenName": { "en": "A. Michael" },
        "familyName": { "en": "Spencer" },
        "affiliations": [ "UCLA", "White House" ]
    }
    */

    $db = new mysqli('localhost', 'cs143', '', 'class_db');
    if ($db->connect_errno > 0) die('Unable to connect to database [' . $db->connect_error . ']');

    $query = "SELECT * FROM Person WHERE lid = $id";
    $rs = $db->query($query);
    while ($row = $rs->fetch_assoc()) { 
        $givenName = $row['givenName']; 
        $familyName = $row['familyName'];
        $gender = $row['gender']; 
        print "Name: $givenName $familyName Sex: $gender\n"; 
    }
    $rs->free();

    $query = "SELECT * FROM Organization WHERE lid = $id";
    $rs = $db->query($query);
    while ($row = $rs->fetch_assoc()) { 
        $orgName = $row['orgName'];
        print "Name: $orgName\n"; 
    }
    $rs->free();

    $query = "SELECT * FROM Birth WHERE lid = $id";
    $rs = $db->query($query);
    while ($row = $rs->fetch_assoc()) { 
        $birthDate = $row['date'];
        $birthCity = $row['city'];
        $birthCountry = $row['country'];
        print "BirthDate: $birthDate BirthCity: $birthCity BirthCountry: $birthCountry\n"; 
    }
    $rs->free();

    $query = "SELECT * FROM Prize WHERE nid IN(SELECT nid FROM Laureate L WHERE lid = $id)";
    $rs = $db->query($query);
    while ($row = $rs->fetch_assoc()) { 
        $awardYear = $row['awardYear'];
        $category = $row['category'];
        $sortOrder = $row['sortOrder'];
        $affilId = $row['affilId'];
        print "AwardYear: $awardYear Category: $category SortOrder: $sortOrder AffilId: $affilId\n"; 
    }
    $rs->free();

    $query = "SELECT * FROM Affiliation WHERE affilId = $affilId";
    $rs = $db->query($query);
    while ($row = $rs->fetch_assoc()) { 
        $affilName = $row['name'];
        $affilCity = $row['city'];
        $affilCountry = $row['country'];
        print "AffiliationName: $affilName City: $affilCity Country: $affilCountry\n"; 
    }
    $rs->free();

    $output = (object) [
        "id" => strval($id),
        "givenName" => (object) [
            "en" => "A. Michael"
        ],
        "familyName" => (object) [
            "en" => "Spencer"
        ],
        "affliations" => array(
            "UCLA",
            "White House"
        )
    ];
    echo json_encode($output);

?>