<?php

namespace Drupal\slav\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\Html;

/**
 *  controller.
 */
class cats extends FormBase {

  /**
   * Form id
   */
  public function getFormId() {
    return 'slav_hello_form';
  }


  /**
   * Form constructor.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please register your cat.'),
    ];

    $form['system_message1'] = [
      '#type' => 'markup',
      '#markup' => '<div class="form-system-messages"></div>',
    ];

    $form['NameOfCat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your cat’s name:'),
      '#description' => $this->t('Enter the name of your cat. Please keep it within 2 to 32 symbols.'),
      '#required' => TRUE,
    ];


    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your Gmail'),
      '#required' => TRUE,
      '#description' => $this ->t('Please use a-z and _ or -'),
      '#ajax' => [
        'callback' => '::GmailValidation',
      ],
      '#prefix' => '<div id="gmailOfcat">',
    ];

    $form['gmail-result-msg'] = [
      '#markup' => '<div id="gmail-result-msg">',
      "#weight" => -100,
    ];


    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add cat'),
      '#ajax' => [
        'callback' => '::UseAjaxSubmit',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];
    return $form;
  }


  /**
   * Form validation.
   */

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $name = $form_state->getValue('NameOfCat');

    if (strlen($name) < 2) {
      $form_state->setErrorByName($name,
        $response->addCommand(
          new HtmlCommand(
            '.form-system-messages',
            'Name doest not meet requirements (too short)' . $form_state->getValue('NameOfCat')

          )
        )
      );
    }


//    $this->t('Name doest not meet requirements (too long)')
    if (strlen($name) > 32) {
      $form_state->setErrorByName('NameTooLong');

    }
    return $response;
  }
//$name = $form_state->getValue('NameOfCat');
//$this->messenger()->addStatus($this->t('You named your cat: %name', ['%name' => $name]));


  public function GmailValidation (array &$form, FormStateInterface $form_state){
    $ajax_response = new AjaxResponse();
    $email = $form_state->getValue('email');
    if(!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[A-Za-z-_]+[@]+[a-z]+[.]+[a-z]+$/', $email)){
      $ajax_response->addCommand(new HtmlCommand('#gmail-result-msg', 'This gmail is incorrect'));
    }
    else {
      $ajax_response->addCommand(new HtmlCommand('#gmail-result-msg', '$gmail'));
    }
    return $ajax_response;
  }

  /**
   * Form txt about name field.
   */
  public function UseAjaxSubmit(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $name = $form_state->getValue('NameOfCat');

    if (strlen($name) <= 32) {
      $response->addCommand(
        new HtmlCommand(
          '.form-system-messages',
          ' ' . ' '
        )
      );
    }

    if (strlen($name) < 2) {
      $response->addCommand(
        new HtmlCommand(
          '.form-system-messages',
          ' Please choose longer name, because this name is too short' . '!!!'
//          . $form_state->getValue('NameOfCat')
        )
      );
    }
    if (strlen($name) > 32) {
      $response->addCommand(
        new HtmlCommand(
          '.form-system-messages',
          ' Please choose shorter name, because this name is too long' . '!!!'
//          . $form_state->getValue('NameOfCat')
        )
      );
    }

      return $response;
  }

  /**
   * Form submit.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }


}
