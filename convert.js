// import fs module to read/write file
const fs = require('fs');

// load JSON data
let file = fs.readFileSync("./data/nobel-laureates.json");
let data = JSON.parse(file);

data.laureates.forEach(laureate => {
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
        birthCity = laureate.birth.place.city ? laureate.birth.place.city.en : null;
        birthCountry = laureate.birth.place.country ? laureate.birth.place.country.en : null;
    } else if (laureate.founded) {
        birthDate = laureate.founded.date;
        birthCity = laureate.founded.place.city ? laureate.founded.place.city.en : null;
        birthCountry = laureate.founded.place.country ? laureate.founded.place.country.en : null;
    }
    console.log((birthDate ? (birthDate + "\t") : null) + (birthCity ? (birthCity + "\t") : null) + (birthCountry ?? null));

    // get the nobel prize information
    laureate.nobelPrizes.forEach(prize => {
        let awardYear = prize.awardYear;
        let category = prize.category.en;
        console.log(awardYear + "\t" + category);

        if (prize.affiliations) {
            prize.affiliations.forEach(affl => {
                let afflName = affl.name.en;
                let afflCity = affl.city ? affl.city.en : null;
                let afflCountry = affl.country ? affl.country.en : null;
                console.log(afflName + "\t" + (afflCity ? (afflCity + "\t") : null) + (afflCountry ?? null));
            });
        }
    });

    // line break
    console.log();
});