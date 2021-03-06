<?php


class Database {

    /**
     * @var Database
     */
    public $connection;

    /**
     * @var Database name
     */
    private $db_name;

    function __construct() {
        $this->db_name = 'is_rocket_launched.db';
        $this->connection = new SQLite3($this->db_name);
        $this->connection->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY, first_frame TEXT, last_frame TEXT, frame_to_user TEXT)");        
    }
}