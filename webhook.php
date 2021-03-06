<?php
include('Model/Info.php');
include('Model/User.php');
class Webhook {

    /**
     * @var apiToken
     */
    private $apiToken;

    /**
     * @var pathTelegram
     */
    private $path;

    /**
     * @var info
     */
    private $info;

    /**
     * @var User
     */
    private $user;

    /**
     * @var chatId
     */
    private $chatId;

    function __construct(){
        
        $this->apiToken = "1654407912:AAGLY3uwxXUiaODsHNmsBkp3ddQY8_96zLc";
        $this->path = "https://api.telegram.org/bot";
        $this->user = new User();
    }

    function init() {
        // Get the info of API service
        $json = file_get_contents('https://framex-dev.wadrid.net/api/video/');
        $obj = json_decode($json);
        $this->info = new Info($obj[0]);

        // Get the data of telegram messages
        $update = json_decode(file_get_contents("php://input"), TRUE);
        if(isset($update)) {

            $this->chatId = $update["message"]["chat"]["id"];
            $messageTelegram = $update["message"]["text"];            
            $obj = $this->user->getData($this->chatId);
            $message = '';
            switch ($messageTelegram) {
                case 'Yes':
                case 'No':
                    $messageResponse = $this->bisect($obj, $messageTelegram);
                    break;
                case '/start':
                    //Check the count of users
                    $countResult = $this->user->getCount($this->chatId);
                    //if the result is 0 then new user
                    if($countResult->fetchArray()['count'] === 0) {
                        $this->user->newUser($this->chatId,$this->info);
                    } else {
                        //restart user
                        $this->user->updateUser($this->info, $this->chatId);
                    }
                    $obj = $this->user->getData($this->chatId);
                    $messageResponse = $this->bisect($obj);
                    break;
                case '/getData':
                    // Get the actual data in database of an user
                    $messageResponse = new stdClass();
                    $messageResponse->found = false;
                    $messageResponse->frame_to_user = $obj['frame_to_user'];
                    $messageResponse->data = json_encode($obj,true);
                    $messageResponse->showData = true;
                    break;
                default:
                    // If send another command return default message
                    $messageResponse = new stdClass();
                    $messageResponse->found = false;
                    $messageResponse->frame_to_user = $obj['frame_to_user'];
                    $messageResponse->data = 'command not found, only accept Yes, No, /start, /getData';
                    $messageResponse->showData = true;
                    break;
            }

            $this->sendMessage($this->chatId, $messageResponse, $obj);   
        }
    }


    function bisect($objUser, $option = '') {
        $left = (int) $objUser['first_frame'];
        $right = (int) $objUser['last_frame'];
        
        if ($option == 'Yes') {
            $right = $objUser['frame_to_user'];
        } else if($option == 'No'){
            $left = $objUser['frame_to_user'];
        } 
        $mid = intval(($left + $right) / 2);
        $info = new stdClass();
        $info->first_frame = $left;
        $info->last_frame = $right;
        $info->frame_to_user = $mid;
        $info->found = (($left + 1) < $right) ? false : true;
        $info->frame = $mid;
        $info->messageResponse = false;
        $info->showData = false;
        $this->user->updateUser($info, $this->chatId);
        return $info;
    }

    function sendMessage($chatId, $message, $objUser) {
       
        $ch = curl_init($this->path . $this->apiToken . "/sendPhoto");

        $buttons = [];
        // If frame is found return only option /start
        if($message->found){
            $caption ='f"Found! Take-off = "' . $message->frame_to_user;
            $buttons = [['/start']];
        } else {
            $caption = 'f' . $message->frame_to_user . ' - did the rocket launch yet? (yes / no)';
            $buttons = [['Yes'], ['No']];
        }
        if($message->showData) {
            $caption = $message->data;
        }

        $keyboard =  [
            'keyboard' => $buttons,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'selective' => true
        ];
        // params to curl
        $params = [
            'chat_id'=>$this->chatId, 
            'photo'=> $this->info->url . "frame/" . $message->frame_to_user,
            'allow_sending_without_reply' => true,
            'caption' => $caption,
            'reply_markup' => json_encode($keyboard,true)   
        ];
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);        
    }
}

$web_hook = new Webhook();
$web_hook->init();


?>