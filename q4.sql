-- - Laureate(lid, nids)
-- - Person(lid, givenName, familyName, gender)
-- - Organization(lid, orgName)
-- - Birth(lid, date, city, country) // Also used for organization foundings
-- - Prize(nid, awardYear, category, sortOrder, affilIds)
-- - Affiliation(affilId, name, city, country)

SELECT DISTINCT COUNT(*) OVER() diff_locations FROM Affiliation WHERE name="University of California" GROUP BY city, country;