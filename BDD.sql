CREATE DATABASE socialmanagement;

USE socialmanagement;

CREATE TABLE user(
    id              bigint UNSIGNED NOT NULL PRIMARY KEY,
    username        varchar(255) NOT NULL,
    access_token    varchar(255) NOT NULL
);

CREATE TABLE statistic (
    user_id         bigint UNSIGNED NOT NULL,
    date            date NOT NULL,
    followers       bigint UNSIGNED NOT NULL,
    posts       bigint UNSIGNED NOT NULL,
    CONSTRAINT statistic_pkey PRIMARY KEY (user_id, date),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

SELECT AVG(followers), MONTH(date), YEAR(date)
FROM statistic
GROUP BY MONTH(date)


/*
INSERT INTO `statistic` (`user_id`, `date`, `followers`, `posts`) VALUES
(3075657012, '2016-03-29', 70, 114),
(3075657012, '2016-03-30', 185, 114),
(3075657012, '2016-03-31', 207, 114),
(3075657012, '2016-04-01', 208, 114),
(3075657012, '2016-04-02', 219, 114),
(3075657012, '2016-04-03', 218, 114),
(3075657012, '2016-04-04', 243, 114),
(3075657012, '2016-04-05', 259, 114),
(3075657012, '2016-04-06', 322, 114),
(3075657012, '2016-04-07', 373, 114),
(3075657012, '2016-04-08', 411, 114),
(3075657012, '2016-04-09', 431, 114),
(3075657012, '2016-04-10', 431, 114),
(3075657012, '2016-04-11', 441, 114),
(3075657012, '2016-04-12', 481, 114),
(3075657012, '2016-04-13', 509, 114),
(3075657012, '2016-04-14', 531, 114),
(3075657012, '2016-04-15', 573, 114),
(3075657012, '2016-04-16', 602, 114),
(3075657012, '2016-04-17', 676, 114),
(3075657012, '2016-04-18', 720, 114),
(3075657012, '2016-04-25', 812, 135);
 */
