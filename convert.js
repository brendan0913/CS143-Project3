// import fs module to read/write file
const fs = require('fs');

// load JSON data
let file = fs.readFileSync("./data/nobel-laureates.json");
let data = JSON.parse(file);

// ID variables
let nd = 0;
let ad = 0;

// Arrays (that will become tables) of objects
let lr = [];
let pr = [];
let or = [];
let br = [];
let nb = [];
let af = [];

data.laureates.forEach(laureate => {
    // get the id, givenName, and familyName of the laureate
    let id = laureate.id;
    let person = laureate.givenName ? true : false;
    let gvName, fmName, g, oName;
    if (person) {
        gvName = laureate.givenName.en;
        fmName = laureate.familyName ? laureate.familyName.en : null;
        g = laureate.gender;
    } else {
        oName = laureate.orgName.en;
    }

    // get the birth/founding date of the laureate
    let bDate, bCity, bCountry;
    if (person && laureate.birth) {
        bDate = laureate.birth.date;
        bCity = laureate.birth.place.city ? laureate.birth.place.city.en : null;
        bCountry = laureate.birth.place.country ? laureate.birth.place.country.en : null;
    } else if (laureate.founded) {
        bDate = laureate.founded.date;
        bCity = laureate.founded.place.city ? laureate.founded.place.city.en : null;
        bCountry = laureate.founded.place.country ? laureate.founded.place.country.en : null;
    }

    let nds = [];
    // get the nobel prize information
    laureate.nobelPrizes.forEach(prize => {
        // Each Nobel prize will be unique, so no need to check
        nb.push({
            nid: nd,
            awardYear: prize.awardYear,
            category: prize.category.en,
            sortOrder: prize.sortOrder,
            afflIds: []
        });
        nds.push(nd);

        if (prize.affiliations) {
            prize.affiliations.forEach(affl => {
                let aName = affl.name.en;
                let aCity = affl.city ? affl.city.en : null;
                let aCountry = affl.country ? affl.country.en : null;

                // Check each object of Affiliation to see if there's a duplicate
                let dup = false;
                af.forEach(entry => {
                    let obj = Object.values(entry);
                    if (obj.includes(aName) && obj.includes(aCity) && obj.includes(aCountry)) {
                        dup = true;
                        nb[nd].afflIds.push(entry.aid);
                    }
                });
                if (!dup) {
                    af.push({
                        aid: ad,
                        afflName: aName,
                        afflCity: aCity,
                        afflCountry: aCountry
                    });
                    nb[nd].afflIds.push(ad);
                    ad++;
                }
            });
        }
        nd++;
    });

    // Add this laureate to the dict
    lr.push({
        lid: id,
        nids: nds
    });
    if (person) {
        pr.push({
            lid: id,
            givenName: gvName,
            familyName: fmName,
            gender: g
        });
    } else {
        or.push({
            lid: id,
            orgName: oName
        });
    }
    br.push({
        lid: id,
        date: bDate,
        city: bCity,
        country: bCountry
    });
});

// Iterate through laureates (lr) to get tables: Laureate, Person, Organization, Birth
// console.log(pr);
lr.forEach(l => {
    let index = pr.findIndex(e => e.lid === l.lid)
    if (index === -1) {
        index = or.findIndex(e => e.lid === l.lid);
    }
    let person = (pr[index] && pr[index].lid === l.lid) ? true : false;

    // Some logging to show how to access elements
    if (person) {

        // console.log(pr[index].lid + "\t" + pr[index].givenName + (pr[index].familyName ? ("\t" + pr[index].familyName) : ""));
    } else {

        // console.log(or[index].lid + "\t" + or[index].orgName);
    }
});

// Iterate through nb to get the Prize table
// console.log(nb);
nb.forEach(p => {
    
});

// Iterate through af to get the Affiliation table
// console.log(af);
af.forEach(a => {
    
});