// import fs module to read/write file
const fs = require('fs');

// load JSON data
let file = fs.readFileSync("/home/cs143/data/nobel-laureates.json");
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

// Fill Laureate.del file
// Laureate(lid, nids)
input = ''
lr.forEach(l => {
    if (l.nids.length === 1){
        input += Object.values(l).join("|") + "\n"
    } else {
        for (let i = 0; i < l.nids.length; i++){
            input += l.lid + "|" + l.nids[i] + "\n"
        }
    }
    // input += l.lid + "|" + l.nids.join("|") + "\n"
    // ^^ puts it all on one line (there are 955 laureates but 962 rows in
    // Laureate since some of them got multiple prizes)
});
fs.writeFile('Laureate.del', input, (err) => {
    if (err) throw err;
})

// Fill Person.del file
// Person(lid, givenName, familyName, gender)
input = ''
pr.forEach(p => {
    let l = p.lid
    let name =  p.givenName ? p.givenName : "\\N"
    let fam_name =  p.familyName ? p.familyName : "\\N"
    let gen = p.gender ? p.gender : "\\N"
    input += l + "|" + name + "|" + fam_name + "|" + gen + "\n"
});
fs.writeFile('Person.del', input, (err) => {
    if (err) throw err;
})

// Fill Organization.del file
// Organization(lid, orgName)
input = ''
or.forEach(o => {
    let l = o.lid
    let name =  o.orgName ? o.orgName : "\\N"
    input += l + "|" + name + "\n"
});
fs.writeFile('Organization.del', input, (err) => {
    if (err) throw err;
})

// Fill Birth.del file
// Birth(lid, date, city, country)
input = ''
br.forEach(b => {
    let l = b.lid
    let d =  b.date ? b.date : "\\N"
    let city_name =  b.city ? b.city : "\\N"
    let country_name = b.country ? b.country : "\\N"
    input += l + "|" + d + "|" + city_name + "|" + country_name + "\n"
});
fs.writeFile('Birth.del', input, (err) => {
    if (err) throw err;
})

// Fill Prize.del file
// Prize(nid, awardYear, category, sortOrder, affilIds)
input = ''
nb.forEach(p => {
    let l = p.nid
    let year =  p.awardYear ? p.awardYear : "\\N"
    let cat =  p.category ? p.category : "\\N"
    let order = p.sortOrder ? p.sortOrder : "\\N"
    if (p.afflIds.length === 1){
        input += l + "|" + year + "|" + cat + "|" + order + "|" + p.afflIds[0] + "\n"
    }
    else if (p.afflIds.length === 0){
        input += l + "|" + year + "|" + cat + "|" + order + "|" + "\\N" + "\n"
    }
    else {
        for (let i = 0; i < p.afflIds.length; i++){
            input += l + "|" + year + "|" + cat + "|" + order + "|" + p.afflIds[i] + "\n"       
        }
    }
    
});
fs.writeFile('Prize.del', input, (err) => {
    if (err) throw err;
})

// Fill Affiliation.del file
// Affiliation(affilId, name, city, country)
input = ''
af.forEach(a => {
    let id = a.aid
    let name =  a.afflName ? a.afflName : "\\N"
    let city =  a.afflCity ? a.afflCity : "\\N"
    let country = a.afflCountry ? a.afflCountry : "\\N"
    input += id + "|" + name + "|" + city + "|" + country + "\n"    
});
fs.writeFile('Affiliation.del', input, (err) => {
    if (err) throw err;
})

// Iterate through laureates (lr) to get tables: Laureate, Person, Birth
// console.log(pr);
// lr.forEach(l => {
//     let index = pr.findIndex(e => e.lid === l.lid)
//     if (index === -1) {
//         index = or.findIndex(e => e.lid === l.lid);
//     }
//     let person = (pr[index] && pr[index].lid === l.lid) ? true : false;

//     // Some logging to show how to access elements
//     if (person) {
//         // console.log(pr[index].lid + "\t" + pr[index].givenName + (pr[index].familyName ? ("\t" + pr[index].familyName) : ""));
//     } 
//     // else {
//     //     console.log(or[index].lid + "\t" + or[index].orgName);
//     // }
// });
