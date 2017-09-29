<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pagerduty
 *
 * @author TajinderM
 */

include_once('../_inc/config.inc');

class pagerduty {
  //put your code here
  public function createPagerdutyIncident($data=array()) {
    $service_key = constant('pagerduty_service_key');
    $client = constant('pagerduty_event_client');
    $client_url = constant('pagerduty_client_url');
    $url = constant('pagerduty_event_url');
    $type = $this->getIncidentStatus($data['type']);
    if($type != 'noreq') {
      $options["service_key"] = $service_key;
      $options["event_type"] = $type;
      $options["description"] =  $data['title'];
      $options["client"] = $client;
      $options["client_url"] = $client_url;
      $options["incident_key"] = $data['id'];
      if(!empty($data['description'])) {
        $options["details"] = array(
          "description" => $data['description'],
        );
      }
      if(!empty($data['links'])) {
        $options['contexts'] = $this->getPagerDutyContext($data['links']);
      }
      $json = json_encode($options);
      $output = $this->curlEventRequest($json, $url);
      return $output;
    }
  }
  
  public function getPagerDutyContext($links = array()) {
    $context = array();
    foreach($links as $item) {
      $context[] = array(
        'type' => 'link',
        'href' => $item,
      );
    }
  }
  
  protected function getIncidentStatus($status) {
    $type = 'noreq';
    $status_id = (int)$status;
    if($status_id == 6) {
      $type = 'trigger';
    }
    elseif($status_id == 7 || $status_id == 4 || $status_id == 5 || $status_id == 10) {
      $type = 'acknowledge';
    }
    elseif($status_id == 3) {
      $type = 'resolve';
    }
    
    return $type;
  }
  
  protected function curlEventRequest($json, $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HEADER, TRUE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);  //Post Fields
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch);

    return $output;
  }
  
  public function postPagerDutyComment($data = array()) {
    $requestor_email = $this->getWorkorderUserEmail($data['user_id']);
    $incident_id = $this->getPagerdutyIncidentId($data['woid']);
    $payload = array(
      'note' => array(
        'content' => $data['comment']
      )
    );
    $json = json_encode($payload);
    $url = constant('pagerduty_restapi_url') . '/incidents/' . urlencode($incident_id) . '/notes';
    $output = $this->curlRestRequest($json, $url, $requestor_email);

    return $output;
  }
  
  protected function getWorkorderUserEmail($user_id) {
    global $mysql;
    $select = "SELECT email FROM `users` WHERE `id`= ?";
    $result = $mysql->sqlprepare($select, array($user_id));
    $email = $result->fetch_assoc();
    
    return $email['email'];
  }
  
  protected function curlRestRequest($json, $url, $requestor_email = '', $type = "POST") {
    $api_key = constant('pagerduty_rest_key');
    $ch= curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    $headers[] = 'Content-type: application/json';
    $headers[] = 'Authorization: Token token=' . $api_key;
    if(!empty($requestor_email))
      $headers[] = 'From: ' . $requestor_email;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, TRUE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if($type == "POST")
      curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    else
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    $output = curl_exec($ch);

    return $output;
  }
  
  protected function getPagerdutyIncidentId($woid) {
    $url = 'https://api.pagerduty.com/incidents?incident_key=' . $woid . '&time_zone=UTC';
    $output = $this->curlRestRequest('', $url, '', 'GET');
    $trimmed = explode('X-Request-Id:', $output);
    $ticket_string = substr($trimmed[1], 33);
    $ticket = json_decode(trim($ticket_string),'true');
    return $ticket['incidents'][0]['id'];
  }
}
