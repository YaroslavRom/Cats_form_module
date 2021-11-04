<?php

namespace Drupal\slav\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * slav  controller.
 */
class SlavController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function getFormId(){
    return 'slav_hello_world';
  }

  /** cat list page  */
  public function CatPage() {
    $form = \Drupal::formBuilder()->getForm('Drupal\slav\Form\cats');
//    $CatPage['content'] = [];
//    $CatPage['form'] = $form;

    return [$form, $this->PullCat()];
  }




/** Creation cats table  output from database  */
public function PullCat() {
  $database =\Drupal::database();
  $responses = $database->select('slav_cats', 'q')
    -> fields('q', ['cats_name', 'email', 'cats_photo', 'created'])
    ->orderBy('created', 'DESC')
    ->execute()->fetchAll();
  $data = [];

  foreach ($responses as $row) {
    $file = File::load($row->cats_photo);
//    $uri = $file->getFileUri();

//    $fid = $row['cats_photo'];
//    $file = File::load($fid);
    $cat_pic = [
      '#theme' => 'image',
//      '#uri' => $file->getFileUri(),
      '#alt' => 'Its a Cat',
      '#title' => 'Cat',
      '#width' => 255,
    ];
    $renderer = \Drupal::service('renderer');
    $value['cats_photo'] = $renderer->render($fid['cats_photo']);
    $data[] = [
      'name' => $row->cats_name,
      'email' => $row->email,
      'created' => $row->created,
      'image' => [
        'data' => $cat_pic,
      ],

    ];

  }
  $heading = ['Name', 'Email', 'Image', 'Data'];

  $construct['table'] = [
    '#type' => 'table',
    '#header' => $heading,
    '#rows' => $data,
  ];
  return [
    $construct,
  ];
}

}
