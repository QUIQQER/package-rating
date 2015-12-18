<?php

/**
 * This file contains QUI\Rating\Handler
 */
namespace QUI\Rating;

use QUI;

/**
 * Class Handler
 * Handles the rating for each site
 *
 * @package QUI\Rating
 */
class Handler
{
    /**
     * @param QUI\Projects\Site $Site
     * @param integer $rating
     */
    public static function rate($Site, $rating)
    {
        $rating = (int)$rating;

        if ($rating > 5) {
            return;
        }

        if ($rating < 0) {
            return;
        }

        $table = QUI::getDBProjectTableName('ratings', $Site->getProject());

        $result = QUI::getDataBase()->fetch(array(
            'from' => $table,
            'where' => array(
                'id' => $Site->getId()
            ),
            'limit' => 1
        ));

        $ratings  = array();
        $userHash = QUI::getUsers()->getSecHash();

        if (isset($result[0])) {
            $ratings = json_decode($result[0]['ratings'], true);
        }

        $ratings[$userHash] = $rating;

        $sum     = array_sum($ratings);
        $average = $sum / count($ratings);

        if (isset($result[0])) {
            QUI::getDataBase()->update(
                $table,
                array(
                    'ratings' => json_encode($ratings),
                    'average' => $average
                ),
                array(
                    'id' => $Site->getId()
                )
            );

        } else {
            QUI::getDataBase()->insert($table, array(
                'id' => $Site->getId(),
                'ratings' => json_encode($ratings),
                'average' => $average
            ));
        }

        QUI\Cache\Manager::clear(
            'quiqqer/rating/site/' . $Site->getId()
        );
    }

    /**
     * Return the rating data of a site
     *
     * @param QUI\Projects\Site $Site
     * @return array
     */
    public static function getRatingFromSite($Site)
    {
        $cacheName = 'quiqqer/rating/site/' . $Site->getId();

        try {
            return QUI\Cache\Manager::get($cacheName);

        } catch (QUI\Exception $Exception) {
        }

        $table = QUI::getDBProjectTableName('ratings', $Site->getProject());

        $result = QUI::getDataBase()->fetch(array(
            'from' => $table,
            'where' => array(
                'id' => $Site->getId()
            ),
            'limit' => 1
        ));

        $data = array(
            'ratings' => 0,
            'average' => 0
        );

        if (isset($result[0])) {
            $data = $result[0];
        }

        QUI\Cache\Manager::set($cacheName, $data);

        return $data;
    }
}
