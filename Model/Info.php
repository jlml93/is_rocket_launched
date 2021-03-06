<?php 


class Info {

    public $name_video;

    public $first_frame;

    public $last_frame;
    
    public $url;

    function __construct($data) {
        $this->name_video = $data->name;
        $this->first_frame = 0;
        $this->last_frame = (int)$data->frames - 1;
        $this->url = str_replace('http','https',$data->url);
    }
}