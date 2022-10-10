<?php
class Common
{
    public function android_push($token, $load, $key)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array();
        $fields['registration_ids'] = $token;
        $fields['data'] = $load;
            
        $headers = array(
            'Authorization: key=' . $key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, true));
        $result = curl_exec($ch);
 
        if($result === FALSE) 
        {
            die('Curl failed: ' . curl_error($ch));
        }

        curl_close($ch);
        //print_r($result);
    }
}
?>