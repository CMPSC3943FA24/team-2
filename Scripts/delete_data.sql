SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM cards
WHERE card_id NOT IN (
    SELECT card_id FROM (
        SELECT card_id FROM cards
        ORDER BY card_id
        LIMIT 13
    ) AS keep_rows
);

SET FOREIGN_KEY_CHECKS = 1;
