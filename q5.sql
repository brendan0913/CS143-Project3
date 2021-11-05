-- - Laureate(lid, nid)
-- - Person(lid, givenName, familyName, gender)
-- - Organization(lid, orgName)
-- - Birth(lid, date, city, country) // Also used for organization foundings
-- - Prize(nid, awardYear, category, sortOrder, affilId)
-- - Affiliation(affilId, name, city, country)

SELECT DISTINCT COUNT(*) OVER() org_prize_years FROM Prize 
WHERE nid IN(SELECT nid FROM Laureate L, Organization O WHERE L.lid = O.lid ) 
GROUP BY awardYear