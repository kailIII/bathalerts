<?php

class Crime extends Module {

    public $url = "https://data.bathhacked.org/resource/e46f-mhfs.json";

    
    function __construct($postcode) {
        parent::__construct($postcode);

        $postcodelat = self::$postCodeLoc[0];
        $postcodelong = self::$postCodeLoc[1];

        $this->url .= '?$where=month>%272014-08-01%27%20AND%20month>%272014-11-01%27%20AND%20within_circle(location,'.$postcodelong.','.$postcodelat.',1000)';
    }
    
    function getData() {
        //Find what's the latest crime known about in the dataset.



        $crimes = json_decode($this->fetch(), true);
        /*foreach($crimes as $crime) {
            var_dump($crime);
        }*/
        return $crimes;
    }
    
}

?>