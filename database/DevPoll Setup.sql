CREATE DATABASE devpoll;

GRANT USAGE ON *.* TO devpoll@localhost IDENTIFIED BY 'devpoll';

GRANT ALL PRIVILEGES ON devpoll.* TO devpoll@localhost;

FLUSH PRIVILEGES;

-- Run these statements after step 5 from the DevPoll.rtf file

create table devpoll.survey
(
surveyid int(5) not null primary key,
firstname varchar(30),
lastname varchar(30),
districtid int(5) not null,
surveystatus boolean not null,
numberquestions int(2) not null,
siteid int(5),
dateopen date not null,
dateclosed date not null,
lastaccessed timestamp,
completed boolean not null,
datesent datetime,
sent boolean
);

create table devpoll.questions
(
questionid int(5) not null primary key,
surveyid int(5) not null,
questiontext varchar(200),
questiontype varchar(30) not null,
datecreated timestamp,
foreign key (surveyid) references survey (surveyid)
);

create table devpoll.comments
(
commentid int(5) not null primary key,
district varchar(30) not null,
commenttext varchar(200) not null,
datecreated timestamp,
siteid int(5) not null
);

create table devpoll.answers
(
answerid int(5) not null primary key,
questionid int(5) not null,
answertext varchar(200) not null,
datecreated timestamp,
foreign key (questionid) references questions (questionid)
);

create table devpoll.roles
(
roleid int(2) not null primary key,
roletype varchar(2) not null,
roledescription varchar(30) not null,
cancreatesite boolean not null,
canopensurvey boolean not null,
canclosesurvey boolean not null,
canaddquestions boolean not null,
candeletequestions boolean not null,
cantakesurvey boolean not null,
cancreatereport boolean not null,
canseereport boolean not null,
canseesurvey boolean not null,
canseedistrictreports boolean not null,
canseereportsbyschool boolean not null,
cancreateloginid boolean not null,
cangrantaccess boolean not null,
canassignrole boolean not null);

create table devpoll.security
(
userid varchar(20) not null primary key,
roleid int(2) not null,
roletype varchar(1) not null,
accesscode varchar(20) not null,
lastlogin timestamp,
active boolean not null,
password varchar(60),
foreign key (roleid) references roles (roleid)
);

create table devpoll.users
(
userid varchar(20) not null primary key,
districtid int(5) not null,
schoolid int(5) not null,
role varchar(20) not null,
opensurveys varchar(20),
firstname varchar(30),
lastname varchar(30),
email varchar(30),
foreign key (userid) references security (userid));

insert into devpoll.roles 
(
roleid, 
roletype, 
roledescription, 
cancreatesite, 
canopensurvey, 
canclosesurvey, 
canaddquestions, 
candeletequestions, 
cantakesurvey, 
cancreatereport, 
canseereport, 
canseesurvey, 
canseedistrictreports, 
canseereportsbyschool, 
cancreateloginid, 
cangrantaccess, 
canassignrole) values

-- (5,'A','Site Administrator','Y','Y','Y','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y'),
-- (4,'A','District Administrator','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','Y','Y'),
-- (3,'T','Tester','N','N','N','N','N','Y','N','Y','Y','N','N','N','N','N'),
-- (2,'N','Parents','N','N','N','N','N','Y','N','Y','Y','Y','N','N','N','N'),
-- (1,'N','Staff','N','N','N','N','N','Y','N','Y','Y','Y','N','N','N','N'),
-- (0,'N','Community','N','N','N','N','N','N','N','Y','Y','N','N','N','N','N');

(5,'A','Site Administrator',1,1,1,0,0,0,1,1,1,1,1,1,1,1),
(4,'A','District Administrator',1,1,1,1,1,1,1,1,1,1,1,0,1,1),
(3,'T','Tester',0,0,0,0,0,1,0,1,1,0,0,0,0,0),
(2,'N','Parents',0,0,0,0,0,1,0,1,1,1,0,0,0,0),
(1,'N','Staff',0,0,0,0,0,1,0,1,1,1,0,0,0,0),
(0,'N','Community',0,0,0,0,0,0,0,1,1,0,0,0,0,0);
