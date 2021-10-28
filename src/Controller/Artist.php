<?php

namespace Drupal\mbz_migrate\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\mbz_migrate\Entity\ArtistInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Artist.
 *
 *  Returns responses for Artist routes.
 */
class Artist extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Artist revision.
   *
   * @param int $artist_revision
   *   The Artist revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($artist_revision) {
    $artist = $this->entityTypeManager()->getStorage('artist')
      ->loadRevision($artist_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('artist');

    return $view_builder->view($artist);
  }

  /**
   * Page title callback for a Artist revision.
   *
   * @param int $artist_revision
   *   The Artist revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($artist_revision) {
    $artist = $this->entityTypeManager()->getStorage('artist')
      ->loadRevision($artist_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $artist->label(),
      '%date' => $this->dateFormatter->format($artist->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of an Artist.
   *
   * @param \Drupal\mbz_migrate\Entity\ArtistInterface $artist
   *   An Artist object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ArtistInterface $artist) {
    $account = $this->currentUser();
    $artist_storage = $this->entityTypeManager()->getStorage('artist');

    $langcode = $artist->language()->getId();
    $langname = $artist->language()->getName();
    $languages = $artist->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $artist->label()]) : $this->t('Revisions for %title', ['%title' => $artist->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all artist revisions") || $account->hasPermission('administer artist entities')));
    $delete_permission = (($account->hasPermission("delete allartist revisions") || $account->hasPermission('administer artist entities')));

    $rows = [];

    $vids = $artist_storage->revisionIds($artist);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\mbz_migrate\ArtistInterface $revision */
      $revision = $artist_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $artist->getRevisionId()) {
          $link = $this->l($date, new Url('entity.artist.revision', [
            'artist' => $artist->id(),
            'artist_revision' => $vid,
          ]));
        }
        else {
          $link = $artist->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.artist.translation_revert', [
                'artist' => $artist->id(),
                'artist_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.artist.revision_revert', [
                'artist' => $artist->id(),
                'artist_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.artist.revision_delete', [
                'artist' => $artist->id(),
                'artist_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['artist_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
