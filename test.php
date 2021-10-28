<?php
use GuzzleHttp\Client;
use MusicBrainz\Filters\ArtistFilter;
use MusicBrainz\Filters\RecordingFilter;
use MusicBrainz\HttpAdapters\GuzzleHttpAdapter;
use MusicBrainz\MusicBrainz;

require '/home/cedric/projs/php/drupal/vendor/autoload.php';
$brainz = new MusicBrainz(new GuzzleHttpAdapter(new Client()), 'username', 'password');
$brainz->setUserAgent('ApplicationName', '0.2', 'http://example.com');

$args = array(
  "recording"  => "Buddy Holly",
  "artist"     => 'Weezer',
  "creditname" => 'Weezer',
  "status"     => 'Official'
);
try {
  $recordings = $brainz->search(new RecordingFilter($args));
  print_r($recordings);
} catch (Exception $e) {
  print $e->getMessage();
}
?>
