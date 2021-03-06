<?php 

include('Database.php');

class User {
    /**
     * @var User
     */
    public $user;

    /**
     * @var Database
     */
    private $db;

    function __construct() {
        $this->user = new stdClass();
        $this->db = new Database();
    }

    function getData($chatId){
        $query = 'SELECT * from users where id = ' . $chatId;
        $result = $this->db->connection->query($query);
        $obj = array();
        while ($row = $result->fetchArray()) {
            $obj = array(
                'id'=> $row['id'],
                'first_frame' => $row['first_frame'],
                'last_frame' => $row['last_frame'],
                'frame_to_user' => $row['frame_to_user']
            );
        }
        return $obj;
    }
    function newUser($chatId, $info) {
        $queryNew = "INSERT INTO users(id, first_frame, last_frame, frame_to_user) VALUES($chatId, " . $info->first_frame . ", " . $info->last_frame . ", '" . intval(($info->first_frame + $info->last_frame) / 2). "')";
        $this->db->connection->query($queryNew);
    }
    function updateUser($info, $chatId){
        $queryUpdate = "UPDATE users set first_frame=" . $info->first_frame . ", last_frame=" . $info->last_frame . ", frame_to_user='" . intval(($info->first_frame + $info->last_frame) / 2). "' where id = $chatId";
        $this->db->connection->query($queryUpdate);
    }

    function getCount($chatId) {
        $queryUser = 'SELECT COUNT(*) as count from users where id = '.$chatId;
        $countResult = $this->db->connection->query($queryUser);
        return $countResult;
    }
}