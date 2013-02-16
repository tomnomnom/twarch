<?php
$db = new \PDO('sqlite:'.__DIR__.'/tweets.sq3');

// FTS table
$db->query('DROP TABLE tweets');
$db->query('
  CREATE VIRTUAL TABLE tweets USING fts4(
    id      INTEGER, 
    created INTEGER, 
    text    TEXT
  )
');
$db->query('CREATE UNIQUE INDEX id ON tweets (id)');
$db->query('CREATE INDEX created ON tweets (created ASC)');

// Stats
$db->query('DROP TABLE stats');
$db->query('
  CREATE TABLE stats (
    key TEXT,
    value TEXT
  )
');
$db->query('CREATE UNIQUE INDEX key on stats (key)');

// Words
$db->query('DROP TABLE words');
$db->query('
  CREATE TABLE words (
    word TEXT,
    count INTEGER
  )
');
$db->query('CREATE UNIQUE INDEX word on words (word)');
$db->query('CREATE INDEX count on words (count DESC)');
