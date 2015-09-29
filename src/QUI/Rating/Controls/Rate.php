<?php

/**
 * This file contains \QUI\Rating\Controls\Rate
 */
namespace QUI\Rating\Controls;

use QUI;

/**
 * Class Rate
 *
 * @package QUI\Rating\Controls
 */
class Rate extends QUI\Control
{
    /**
     * Konstruktor
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        $this->setAttributes(array(
            'stars'       => 5,
            'showDetails' => true
        ));

        parent::__construct($attributes);

        $this->addCSSFile(dirname(__FILE__) . '/Rate.css');
    }

    /**
     * @return string
     */
    public function getBody()
    {
        $Engine = QUI::getTemplateManager()->getEngine();
        $rating = QUI\Rating\Handler::getRatingFromSite(
            $this->_getSite()
        );

        $ratings     = json_decode($rating['ratings'], true);
        $bestRating  = null;
        $worstRating = null;
        $details     = array_fill(1, $this->getAttribute('stars'), 0);

        if (!is_array($ratings)) {
            $ratings = array();
        }

        foreach ($ratings as $entry) {

            if (!$bestRating || $entry > $bestRating) {
                $bestRating = $entry;
            }

            if (!$worstRating || $entry < $worstRating) {
                $worstRating = $entry;
            }

            $details[$entry]++;
        }

        $details = array_reverse($details, true);

        $Engine->assign(array(
            'Site'        => $this->_getSite(),
            'value'       => round($rating['average'], 1),
            'ratingCount' => count($ratings),
            'bestRating'  => $bestRating,
            'worstRating' => $worstRating,
            'this'        => $this,
            'details'     => $details,
            'Math'        => new QUI\Utils\Math()
        ));

        return $Engine->fetch(
            dirname(__FILE__) . '/Rate.html'
        );
    }

    /**
     * Return the Project
     *
     * @return \QUI\Projects\Site
     */
    protected function _getSite()
    {
        if ($this->getAttribute('Site')) {
            return $this->getAttribute('Site');
        }

        $Site = \QUI::getRewrite()->getSite();
        $this->setAttribute('Site', $Site);

        return $Site;
    }
}
