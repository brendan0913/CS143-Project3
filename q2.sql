-- - Laureate(lid, nid)
-- - Person(lid, givenName, familyName, gender)
-- - Organization(lid, orgName)
-- - Birth(lid, date, city, country) // Also used for organization foundings
-- - Prize(nid, awardYear, category, sortOrder, affilId)
-- - Affiliation(affilId, name, city, country)

SELECT country FROM Affiliation WHERE name="CERN";