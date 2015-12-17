DROP TABLE IF EXISTS `citizen_groups`;

CREATE TABLE IF NOT EXISTS `citizen_groups`(
    `citizen_id` int(11) NOT NULL,
    `group_id` int(11) NOT NULL,
    FOREIGN KEY(citizen_id) REFERENCES users(id),
    FOREIGN KEY(group_id) REFERENCES groups(id)
);

INSERT INTO citizen_groups(citizen_id, group_id) VALUES(1, 1);
INSERT INTO citizen_groups(citizen_id, group_id) VALUES(2, 1);
INSERT INTO citizen_groups(citizen_id, group_id) VALUES(1, 2);
INSERT INTO citizen_groups(citizen_id, group_id) VALUES(2, 3);