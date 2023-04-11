<?php
/**
 * @file
 * Contains \Drupal\mandarina_module\Form\LoginWifiForm.
 */

namespace Drupal\mandarina_module\Form;

use Drupal\mandarina_module\FuncionesGenerales;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;

class LoginWifiForm extends FormBase{
    
    public function getFormID() {
        return 'login_wifi_form';
    }
    
    public function buildForm(array $form, FormStateInterface $form_state, $node = array() ){
        
        $request    = \Drupal::request();
        $clientMac  = $request->query->get('clientMac');
        $apMac      = $request->query->get('apMac');
        $redirectUrl = $request->query->get('redirectUrl');
        $ssidName   = $request->query->get('ssidName');
        $radioId    = $request->query->get('radioId');
        $site       = $request->query->get('site');
        $t          = $request->query->get('t');
        
        $form['#attributes'] = [
            'class' => [
                'card',
            ],
            'novalidate' => 'novalidate'
        ];
        $form['card_header'] = [
            '#type' => 'fieldset',
            '#attributes' => [
                'class' => ['card-header']
            ]
        ];
        $form['card_header']['info'] = [
            '#markup' => t('Register your data to connect to the wifi network')
        ];
        $form['card_body'] = [
            '#type' => 'fieldset',
            '#attributes' => [
                'class' => ['card-body']
            ]
        ];
        $form['card_body']['nombre'] = [
            '#type' => 'textfield',
            '#title' => FALSE,
            '#attributes'    => array(
                'placeholder' => t('Name'),
                'minlength' => 2
            ),
            '#title_display' => 'invisible',
            '#required' => TRUE,
        ];
        $form['card_body']['apellido'] = [
            '#type' => 'textfield',
            '#title' => FALSE,
            '#attributes'    => array(
                'placeholder' => t('Lastname'),
                'minlength' => 2
            ),
            '#title_display' => 'invisible',
            '#required' => TRUE,
        ];
        $form['card_body']['telefono'] = [
            '#type' => 'tel',
            '#title' => FALSE,
            '#attributes'    => array(
                'placeholder' => t('Phone'),
                'minlength' => 2
            ),
            '#title_display' => 'invisible',
            '#required' => TRUE,
        ];
        $form['card_body']['correo'] = [
            '#type' => 'email',
            '#title' => FALSE,
            '#attributes'    => array(
                'placeholder' => t('E-mail'),
                'minlength' => 2
            ),
            '#title_display' => 'invisible',
            '#required' => TRUE,
        ];
        $form['clientMac'] = [
            '#type' => 'hidden',
            '#default_value' => $clientMac
        ];
        $form['apMac'] = [
            '#type' => 'hidden',
            '#default_value' => $apMac
        ];
        $form['ssidName'] = [
            '#type' => 'hidden',
            '#default_value' => $ssidName
        ];
        $form['radioId'] = [
            '#type' => 'hidden',
            '#default_value' => $radioId
        ];
        $form['redirectUrl'] = [
            '#type' => 'hidden',
            '#default_value' => $redirectUrl
        ];
        $form['site'] = [
            '#type' => 'hidden',
            '#default_value' => $site
        ];
        $form['t'] = [
            '#type' => 'hidden',
            '#default_value' => $t
        ];
        $form['card_body']['simplenews'] = [
            '#type' => 'checkbox',
            '#title' => t('I agree to receive information by email'),
            '#attributes' => array(
                'checked' => 'checked'
            )
        ];
        $form['card_body']['entrar'] = array(
            '#title'  => FALSE,
            '#type'   => 'submit',
            '#value'  => t('Enter to the wifi network'),
            '#attributes' => array(
                'class' => array('btn-lg btn-primary entrar-wifi')
            )
        );
        $form['#attached']['library'][] = 'mandarina_module/wait';
        $form['modal'] = [
            '#markup' => '<div class="modal fade" id="wait-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Estamos procesando tu información</h5>
                        </div>
                        <div class="modal-body text-center">
                        <div class="spinner-border m-5" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        </div>
                    </div>
                </div>
            </div>',
            '#allowed_tags' => ['a', 'em', 'strong', 'cite', 'blockquote', 'code', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'iframe', 'script', 'div', 'span', 'img', 'i', 'label', 'h2']
        ];
        return $form;
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (empty($form_state->getValue('nombre'))) {
            $text = t("The name can't be empty.");
            $form_state->setErrorByName('edit-nombre', $text);
        }
        if (empty($form_state->getValue('apellido'))) {
            $text = t("The lastname can't be empty.");
            $form_state->setErrorByName('edit-apellido', $text);
        }
        if (empty($form_state->getValue('telefono'))) {
            $text = t("The phone can't be empty.");
            $form_state->setErrorByName('edit-telefono', $text);
        }
        if (empty($form_state->getValue('correo'))) {
            $text = t("The email can't be empty.");
            $form_state->setErrorByName('edit-correo', $text);
        }
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state) {
        if( isset($_GET['debug']) && $_GET['debug'] == 1 ){
            sleep(5);
        }else{
            $token = FuncionesGenerales::login();
            if( $token ){
                $milliseconds = strtotime("+2 hours");
                $redirect = FuncionesGenerales::authorize($_GET['clientMac'], $_GET['apMac'], $_GET['ssidName'], $_GET['radioId'], $_GET['t'], $milliseconds, $_GET['site'], $token);
                if( $redirect ){
                    $nombre   = $form_state->getValue('nombre');
                    $apellido = $form_state->getValue('apellido');
                    $telefono = $form_state->getValue('telefono');
                    $email    = $form_state->getValue('correo');
                    $exist = FuncionesGenerales::verifyExist($telefono, $email);
                    if( !$exist ){
                        FuncionesGenerales::saveClient( $nombre, $apellido, $telefono, $email );
                    }else{
                        if( !FuncionesGenerales::alreadyVisit($exist) ){
                            FuncionesGenerales::saveVisit( $exist );
                        }
                    }
                    $response = new TrustedRedirectResponse($_GET['redirectUrl']);
                    $form_state->setResponse($response); 
                }
            }
        }
    }
}
