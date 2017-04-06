CREATE TABLE articles (
id SERIAL PRIMARY KEY,
title varchar(255),
publication_year INTEGER,
link_to_article VARCHAR(255) UNIQUE
);

CREATE TABLE authors (
id SERIAL PRIMARY KEY,
name varchar(255) UNIQUE
);

CREATE TABLE write (
author_id INTEGER REFERENCES authors(id),
article_id INTEGER REFERENCES articles(id)
);

CREATE TABLE keywords (
id SERIAL PRIMARY KEY,
word varchar(50) UNIQUE
);

CREATE TABLE attached (
article_id INTEGER REFERENCES articles(id),
keyword_id INTEGER REFERENCES keywords (id)
);

CREATE TABLE users(email VARCHAR(50) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, rights BOOLEAN NOT NULL);

CREATE INDEX authors_index ON authors (name);
CREATE INDEX articles_index ON articles (title);
CREATE INDEX keywords_index ON keywords (keyword);
