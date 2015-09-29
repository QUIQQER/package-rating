<?php

/**
 * Add a tag
 *
 * @param String $project - name of the project
 * @param String $siteId -
 * @param String $rating -
 *
 * @return Array
 */
function package_quiqqer_rating_ajax_addRating(
    $project,
    $siteId,
    $rating
) {
    $Project = QUI::getProjectManager()->decode($project);
    $Site    = $Project->get($siteId);

    QUI\Rating\Handler::rate($Site, $rating);

    $result            = QUI\Rating\Handler::getRatingFromSite($Site);
    $result['average'] = round($result['average'], 1);

    return $result;
}

QUI::$Ajax->register(
    'package_quiqqer_rating_ajax_addRating',
    array('project', 'siteId', 'rating')
);
