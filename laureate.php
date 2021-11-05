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

if ($is_org){
    $output = (object) [
        "id" => strval($id),
        "orgName" => $name,
        "founded" => (object) [
            "date" => $date,
            "place" => (object) [
                "city" => (object) [
                    "en" => $city
                ],
                "country" => (object) [
                    "en" => $country
                ]
            ]
        ]
        // "nobelPrizes" => array(
        //     (object) [
    
        //     ]
        // )
        // "affliations" => array(
        //     "UCLA",
        //     "White House"
        // )
    ];
}
else {
    $output = (object) [
        "id" => strval($id),
        "givenName" => (object) [
            "en" => $first
        ],
        "familyName" => (object) [
            "en" => $last
        ],
        "gender" => $gender,
        "birth" => (object) [
            "date" => $date,
            "place" => (object) [
                "city" => (object) [
                    "en" => $city
                ],
                "country" => (object) [
                    "en" => $country
                ]
            ]
        ]
        // "nobelPrizes" => array(
        //     (object) [
    
        //     ]
        // )
        // "affliations" => array(
        //     "UCLA",
        //     "White House"
        // )
    ];
}

echo json_encode($output);

?>