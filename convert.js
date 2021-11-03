// import fs module to read/write file
const fs = require('fs');

// load JSON data
let file = fs.readFileSync("./data/nobel-laureates.json");
let data = JSON.parse(file)

.laureates.forEach(laureate => {
    // get the id, givenName, and familyName of the laureate
    let id = laureate.id;
    let person = laureate.givenName ? true : false;
    if (person) {
        let givenName = laureate.givenName.en;
        let familyName = laureate.familyName ? laureate.familyName.en : null;

        // print the extracted information
        console.log(id + "\t" + givenName + (familyName ? ("\t" + familyName) : null));
    } else {
        let orgName = laureate.orgName.en;

        // print the extracted information
        console.log(id + "\t" + orgName);
    }

    // get the birth/founding date of the laureate
    let birthDate, birthCity, birthCountry;
    if (person && laureate.birth) {
        birthDate = laureate.birth.date;
        if (laureate.birth.place.city) {
            birthCity = laureate.birth.place.city.en;
        }
        if (laureate.birth.place.country) {
            birthCountry = laureate.birth.place.country.en;
        }
    } else if (laureate.founded) {
        birthDate = laureate.founded.date;
        if (laureate.founded.place.city) {
            birthCity = laureate.founded.place.city.en;
        }
        if (laureate.founded.place.country) {
            birthCountry = laureate.founded.place.country.en;
        }
    }
    console.log((birthDate ? (birthDate + "\t") : null) + (birthCity ? (birthCity + "\t") : null) + (birthCountry ?? null));
});