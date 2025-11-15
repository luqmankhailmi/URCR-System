

CREATE TABLE USERS (
user_id int auto_increment PRIMARY KEY,
user_email VARCHAR(25) NOT NULL,
user_pass VARCHAR(25)
);

CREATE TABLE CLUBS (
club_id int auto_increment PRIMARY KEY,
club_name VARCHAR(20) NOT NULL,
cat_id int,
FOREIGN KEY (cat_id) REFERENCES CATEGORY (cat_id)
);

CREATE TABLE CATEGORY (
cat_id int auto_increment PRIMARY KEY,
cat_name VARCHAR(20)
);

CREATE TABLE ANNOUNCEMENT (
ann_id INT auto_increment PRIMARY KEY,
ann_content VARCHAR(100),
club_id int,
FOREIGN KEY (club_id) REFERENCES CLUBS (club_id)
);

CREATE TABLE EVENTS (
event_id INT auto_increment PRIMARY KEY,
event_name VARCHAR(20),
event_desc VARCHAR(100),
club_id int,
FOREIGN KEY (club_id) REFERENCES CLUBS (club_id)
);

CREATE TABLE MEDIA (
media_id INT auto_increment PRIMARY KEY,
media_url VARCHAR(100),
club_id int,
FOREIGN KEY (club_id) REFERENCES CLUBS (club_id)
);

CREATE TABLE REVIEWS (
rev_id INT auto_increment PRIMARY KEY,
rev_comment VARCHAR(100),
rev_rating INT(2),
club_id int,
user_id int,
FOREIGN KEY (club_id) REFERENCES CLUBS (club_id),
FOREIGN KEY (user_id) REFERENCES USERS (user_id)
);

CREATE TABLE USER_CLUBS (
    user_id int,
    club_id int,
    date_joined DATE,
    PRIMARY KEY (user_id, club_id),
    FOREIGN KEY (user_id) REFERENCES USERS (user_id),
    FOREIGN KEY (club_id) REFERENCES CLUBS (club_id)
)

// USER_CLUBS (2)
create table user_club (
    uc_id int auto_increment primary key,
    user_id int,
    club_id int,
    FOREIGN KEY (user_id) references users (user_id),
    FOREIGN KEY (club_id) references clubs (club_id)
)