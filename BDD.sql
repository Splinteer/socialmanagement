CREATE DATABASE socialmanagement;

USE socialmanagement;

CREATE TABLE user(
    username        varchar(255) NOT NULL PRIMARY KEY,
    access_token    varchar(255) NOT NULL
);

CREATE TABLE statistic (
    username        varchar(255) NOT NULL,
    date            date NOT NULL,
    followers       bigint UNSIGNED NOT NULL,
    posts       bigint UNSIGNED NOT NULL,
    CONSTRAINT statistic_pkey PRIMARY KEY (username, date)
);


SELECT AVG(followers), MONTH(date), YEAR(date)
FROM statistic
GROUP BY MONTH(date)
