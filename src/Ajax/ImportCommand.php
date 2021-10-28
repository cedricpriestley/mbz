<?php

namespace Drupal\mbz_migrate\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class AjaxCommand.
 */
class ImportCommand implements CommandInterface {

  /**
   *
   * @var array
   */
  protected $data;

/**
   * Constructs an ImportCommand object.
   *
   * @param array $data
   *   A JSON array.
   */
  public function __construct($data) {
    $this->data = $data;
  }

  /**
   * Render custom ajax command.
   *
   * @return ajax
   *   Command function.
   */
  public function render() {
    return [
      'command' => 'populateArtistForm',
      'data' => $this->data,
    ];
  }
}
