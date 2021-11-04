<?php

namespace Drupal\slav\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\InsertCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\media\Entity\Media;


/**
 *  controller.
 */
class cats extends FormBase {

  protected $dateNtime;

  /**
   * Form id
   */
  public function getFormId() {
    return 'slav_hello_form';
  }

  public static function create(ContainerInterface $container)
  {
    $activetime = parent::create($container);
    $activetime->dateNtime = $container -> get('datetime.time');
    return $activetime;
  }

  /**
   * Form constructor.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions']['#type'] = 'actions';
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
      '#ajax' => [
        'event' => 'change',
        'callback' => '::UseAjaxName',
      ],
    ];

    $form['system_message2'] = [
      '#type' => 'markup',
      '#markup' => '<div class="form-system-email"></div>',
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your Gmail'),
      '#required' => TRUE,
      '#description' => $this ->t('Please use a-z and "_" or "-" '),
      '#size' => 20,
      '#prefix' => '<div id="gmailOfcat">',
      '#ajax' => [
        'event' => 'keyup',
        'callback' => '::UseAjaxMail',
      ],
    ];

    $form['system_message3'] = [
      '#type' => 'markup',
      '#markup' => '<div class="form-system-img"></div>',
    ];

    $form['CatImg'] = [
      '#type' => 'managed_file',
      '#required' => TRUE,

      '#title' => $this->t('Your Cats Picture'),
      '#description' => $this ->t('Please use image files under 2 MB '),

      '#progress_indicator' => 'throbber',
      '#progress_message' => 'Uploading ...',
      '#upload_location' => 'public://',

      '#upload_validators' =>  [
        'file_validate_is_image' => [],
        'file_validate_extensions' => ['png jpg jpeg'],
        'file_validate_size' => [2097152],
      ],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add cat'),
//      '#ajax' => [
//        'callback' => '::UseAjaxSubmit',
//        'event' => 'click',
//        'progress' => [
//          'type' => 'throbber',
//        ],
//      ],
    ];
    return $form;
  }


  /**
   * Form validation.
   */

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $name = $form_state->getValue('NameOfCat');


    /**  validation for name of cat */
    if (strlen($name) < 2) {
      $form_state->setErrorByName('NameTooShort');
    }
    if (strlen($name) > 32) {
      $form_state->setErrorByName('NameTooLong');
    }
    /** End of cats name validation  */

    /** validation for email  */
    $email = $form_state->getValue('email');
    if ((!filter_var($email, FILTER_VALIDATE_EMAIL)) || (strpbrk($email, '1234567890+*/!#$^&*()='))) {
      $form_state->setErrorByName('IncorrectMail');
    }
    /** End of validation for email  */

    return $response;
  }

  /**
   * Form txt about name field.
   */
  public function UseAjaxMail(array &$form, FormStateInterface $form_state) {
    /**  Ajax email validation  */
    $response = new AjaxResponse();
    $email = $form_state->getValue('email');

    if ((!filter_var($email, FILTER_VALIDATE_EMAIL)) || (strpbrk($email, '1234567890+*/!#$^&*()='))) {
      $response->addCommand(
        new HtmlCommand(
          '.form-system-email',
          ' Please Enter Correct Email' . ' !!!'
        )
      );
    }
    else {
      $response->addCommand(
        new HtmlCommand(
          '.form-system-email',
          ' ' . ' '
        )
      );
    }
    /** End of  Ajax email validation  */


    return $response;
  }
  public function UseAjaxName(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $name = $form_state->getValue('NameOfCat');

    /** ajax validation for name of cat */
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
        )
      );
    }
    if (strlen($name) > 32) {
      $response->addCommand(
        new HtmlCommand(
          '.form-system-messages',
          ' Please choose shorter name, because this name is too long' . '!!!'
        )
      );
    }
    /** End of cats name ajax validation  */
    return $response;
  }

  public function UseAjaxSubmit(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    /** submit callback  */
    return $response;
  }

  /**
   * Form submit.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if($this->validateForm($form, $form_state)==TRUE){

      $connect = \Drupal::service('database');
      $file = file_save_upload('CatImg', [], 'public://cats');

      $connect->insert('slav_cats')
        -> fields([
          'cats_name' => $form_state->getValue('NameOfCat'),
          'email' => $form_state->getValue('email'),
          'uid' => $this->currentUser()->id(),
          'created' => date('m-d-Y', $this->dateNtime->getCurrentTime()),
        'cats_photo' => $form_state->getValue('CatImg')[0],

        ])
        ->execute();
      \Drupal::messenger()->addMessage($this->t("Cat is registered :)"), 'status', TRUE);


      $url = \Drupal\Core\Url::fromRoute('slav.content');
      return $form_state->setRedirectUrl($url);
    }
  }


}


