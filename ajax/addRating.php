<?php

/**
 * Add a tag
 *
 * @param String $project - name of the project
 * @param String $siteId -
 * @param String $rating -
 *
 * @return array
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_rating_ajax_addRating',
    function ($project, $siteId, $rating) {
        $Project = QUI::getProjectManager()->decode($project);
        $Site    = $Project->get($siteId);

        QUI\Rating\Handler::rate($Site, $rating);

        $result            = QUI\Rating\Handler::getRatingFromSite($Site);
        $result['average'] = round($result['average'], 1);

        return $result;
    },
    array('project', 'siteId', 'rating')
);
