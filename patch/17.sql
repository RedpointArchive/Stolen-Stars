ALTER TABLE system
ADD notes TEXT;

UPDATE system
SET notes = 'The system where it all begins.'
WHERE name = 'Arro';

UPDATE system
SET notes = 'Drugs ''n'' stuff'
WHERE name = 'Zazzar';