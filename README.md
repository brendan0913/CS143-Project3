# CS143-Project3
Data conversion project for CS143

## Schema:
- Laureate(lid, nid)
- Person(lid, givenName, familyName, gender)
- Organization(lid, orgName)
- Birth(lid, date, city, country) // Also used for organization foundings
- Prize(nid, awardYear, category, sortOrder, affilId)
- Affiliation(affilId, name, city, country)