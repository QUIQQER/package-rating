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
            'stars' => 5
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

        foreach ($ratings as $entry) {

            if (!$bestRating || $entry > $bestRating) {
                $bestRating = $entry;
            }

            if (!$worstRating || $entry < $worstRating) {
                $worstRating = $entry;
            }
        }

        $Engine->assign(array(
            'Site'        => $this->_getSite(),
            'value'       => round($rating['average'], 1),
            'ratingCount' => count($ratings),
            'bestRating'  => $bestRating,
            'worstRating' => $worstRating,
            'this'        => $this
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