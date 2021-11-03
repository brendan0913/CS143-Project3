// import fs module to read/write file
const fs = require('fs');

// load JSON data
let file = fs.readFileSync("./data/nobel-laureates.json");
let data = JSON.parse(file)

.laureates.forEach(laureate => {
    // get the id, givenName, and familyName of the laureate
    let id = laureate.id;
    if (laureate.givenName) {
        let givenName = laureate.givenName.en;
        let familyName = laureate.familyName ? laureate.familyName.en : null;

        // print the extracted information
        console.log(id + "\t" + givenName + (familyName ? ("\t" + familyName) : null));
    } else if (laureate.orgName) {
        let orgName = laureate.orgName.en;

        // print the extracted information
        console.log(id + "\t" + orgName);
    } else {
        console.log("WTF did you do?");
        return;
    }
});