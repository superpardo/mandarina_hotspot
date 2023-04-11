<?php

/**
 * @file
 * Contains \Drupal\mandarina_module\FuncionesGenerales.
 */

namespace Drupal\mandarina_module;

use Drupal\node\Entity\Node;

class FuncionesGenerales
{
    const CONTROLLER_SERVER = "https://wap.tplinkcloud.com";
    const OPERATOR_USER = "superpardo@gmail.com";
    const OPERATOR_PASSWORD = "m4nd4r1n4.1234";
    const COOKIE_FILE_PATH = "cookie.txt";
    const TOKEN_FILE_PATH = "csrfToken.txt";
    
    public static function verifyExist( $telefono, $email){
        $query = \Drupal::entityQuery('node');
        $query->condition('status', 1);
        $group = $query->orConditionGroup()
            ->condition('field_telefono', $telefono)
            ->condition('field_email', $email);
        $query->condition($group);
        $query->condition('type', 'cliente');
        $query->sort('title', 'DESC');
        $aux_datos = $query->execute();
        if( empty( $aux_datos ) ){
            return FALSE;
        }else{
            return array_values($aux_datos)[0];
        }
    }
    
    public static function addSuscription( $email ){
        $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $subscription_manager = \Drupal::service('simplenews.subscription_manager');
        $subscription_manager->subscribe($email, 'default', FALSE, 'website', $lang);
    }
    
    public static function saveClient($nombre, $apellido, $telefono, $email){
        $uid = \Drupal::currentUser()->id();
        $node = Node::create(array(
            'nid' => NULL,
            'type' => 'cliente',
            'title' => $nombre,
            'uid' => $uid,
            'status' => 1,
        ));
        $node->field_apellido = $apellido;
        $node->field_telefono = $telefono;
        $node->field_email    = $email;
        $node->save();
        self::addSuscription( $email );
        self::saveVisit($node->id());
    }
    
    public static function alreadyVisit( $cliente ){
        $now = date('Y-m-d');
        $query = \Drupal::entityQuery('node');
        $query->condition('type', 'visita');
        $query->condition('field_cliente.target_id', $cliente, '=');
        $query->condition('field_fecha_visita', $now, '=');
        $results = $query->execute();
        if( empty( $results ) ){
            return FALSE;
        }else{
            return TRUE;
        }
    }
    
    public static function saveVisit( $cliente ){
        $uid = \Drupal::currentUser()->id();
        $node = Node::create(array(
            'nid' => NULL,
            'type' => 'visita',
            'title' => $cliente ." - ". time() ,
            'uid' => $uid,
            'status' => 1,
        ));
        $node->field_cliente = $cliente;
        $node->field_fecha_visita = date("Y-m-d", time());
        $node->save();
    }
    
    public static function login(){
        $data = array(
            'name' => self::OPERATOR_USER,
            'password' => self::OPERATOR_PASSWORD
        );
        $ch = curl_init();
        $url = self::CONTROLLER_SERVER . "/api/v2/hotspot/login";
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR => self::COOKIE_FILE_PATH,
            CURLOPT_COOKIEFILE => self::COOKIE_FILE_PATH,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        if( $response->errorCode == 0 ){
            return $response->result->token;
        }else{
            return FALSE;
        }
    }
    
    public static function authorize($clientMac,$apMac,$ssidName,$radioId,$t, $milliseconds,$site, $token)
    {
        // Send user to authorize and the time allowed
        $data = array(
            'clientMac' => $clientMac,
            'apMac' => $apMac,
            'ssidName' => $ssidName,
            't' => $t,
            'radioId' => $radioId,
            'site' => $site,
            'authType' => 4,
            'time' => $milliseconds,
        );
        
        $ch = curl_init();
        $url = self::CONTROLLER_SERVER . "/api/v2/hotspot/extPortal/auth?token=" . $token;
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR => self::COOKIE_FILE_PATH,
            CURLOPT_COOKIEFILE => self::COOKIE_FILE_PATH,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        if( $response->errorCode == 0 ){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}
