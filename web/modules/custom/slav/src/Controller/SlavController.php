<?php

namespace Drupal\slav\Controller;

use Drupal\Core\Controller\ControllerBase;

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
    $build = [
      '#markup' => $this->t('Hello World!'),
    ];
    return $build;
  }

}
