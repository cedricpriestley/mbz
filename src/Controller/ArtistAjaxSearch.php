<?php

namespace Drupal\mbz_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;

/**
 * Defines a route controller for autocomplete musicbrainz artist search
 */
class ArtistAjaxSearch extends ControllerBase {

  /**
   * Handler for autocomplete request.
   */
  public function name(Request $request) {
    $results = [];
    $input = $request->query->get('q');

    // Get the typed string from the URL, if it exists.
    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);
    $data = mbz_migrate_artist_search($input);

    $count = $data['count'];
    $offset = $data['offset'];
    $artists = $data['artists'];

    foreach ($artists as $artist) {
      $artist_name = $artist['name'];
      $artist_disambiguation = $artist['disambiguation'];
      $artist_mbid = $artist['id'];
      $artist_value = $artist['disambiguation'] ? "$artist_name($artist_disambiguation)::$artist_mbid" : "$artist_name::$artist_mbid";
      $artist_label = $artist['disambiguation'] ? "$artist_name($artist_disambiguation)::$artist_mbid" : "$artist_name::$artist_mbid";
      $results[] = [
        'value' => $artist_value,
        'label' => $artist_label,
      ];
    }

    return new JsonResponse($results);
  }
}
