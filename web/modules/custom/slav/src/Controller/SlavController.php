<?php

namespace Drupal\alexandr\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * An example controller.
 */
class SlavController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function getFormId(){
    return 'slav_hello_world';
  }
  public function content()
  {
    $TheWorkingform = \Drupal::formBuilder()->getForm('Drupal\slav\Form\cats');

    return[
      [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('Hello! You can add here a photo of your cat.'),
        '#attributes' => [
          'class' => ['cats-title'],
        ],
      ],
      $TheWorkingform,
    ];
  }

}
