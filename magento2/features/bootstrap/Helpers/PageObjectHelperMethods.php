<?php

namespace Helpers;

trait PageObjectHelperMethods
{
    /**
     * @param array $params
     */
    function openPage($params = [])
    {
        $this->open($params);
        $this->waitForPageLoad();
    }

    function acceptAlert()
    {
        $this->getDriver()->getWebDriverSession()->accept_alert();
    }

    /**
     * @param mixed $condition
     * @param int $maxWait
     */
    function waitForCondition($condition, $maxWait = 120000)
    {
        $this->getSession()->wait($maxWait, $condition);
    }

    /**
     * @param int $maxWait
     */
    function waitForPageLoad($maxWait = 120000)
    {
        $this->waitForCondition('(document.readyState == "complete") && (typeof window.jQuery == "function") && (jQuery.active == 0)', $maxWait);
    }

    /**
     * @param string $elementName
     * @param int $maxWait
     */
    function waitForElement($elementName, $maxWait = 120000)
    {
        $visibilityCheck = $this->getElementVisibilyCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && $visibilityCheck", $maxWait);
    }

    /**
     * @param string $elementName
     * @param int $maxWait
     */
    function waitUntilElementDisappear($elementName, $maxWait = 120000)
    {
        $visibilityCheck = $this->getElementVisibilyCheck($elementName);
        $this->waitForCondition("(typeof window.jQuery == 'function') && !$visibilityCheck", $maxWait);
    }

    /**
     * @param int $waitTime
     */
    function waitTime($waitTime)
    {
        $this->getSession()->wait($waitTime);
    }

    function scrollToBottom()
    {
        $this->getSession()->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    }

    /**
     * @param string $elementName
     */
    function clickElement($elementName)
    {
        $this->getElementWithWait($elementName)->click();
    }

    /**
     * @param string $elementName
     * @return mixed
     */
    function getElementValue($elementName)
    {
        return $this->getElementWithWait($elementName)->getValue();
    }

    /**
     * @param string $elementName
     * @param string $value
     */
    function setElementValue($elementName, $value)
    {
        $this->getElementWithWait($elementName)->setValue($value);
    }

    /**
     * @param string $elementName
     */
    function getElementText($elementName)
    {
        return $this->getElementWithWait($elementName)->getText();
    }

    /**
     * @param string $elementName
     * @param int $waitTime
     * @return mixed
     */
    public function getElementWithWait($elementName, $waitTime = 2500)
    {
        $this->waitForElement($elementName, $waitTime);
        return $this->getElement($elementName);
    }

    /**
     * @param $elementName
     * @return string
     */
    public function getElementVisibilyCheck($elementName)
    {
        $visibilityCheck = 'true';

        if (isset($this->elements[$elementName]['css'])) {
            $elementFinder = $this->elements[$elementName]['css'];
            $visibilityCheck = "jQuery('$elementFinder').is(':visible')";
        }

        if (isset($this->elements[$elementName]['xpath'])) {
            $elementFinder = $this->elements[$elementName]['xpath'];
            $visibilityCheck = "jQuery(document.evaluate('$elementFinder', document, null, XPathResult.ANY_TYPE, null).iterateNext()).is(':visible')";
        }

        return $visibilityCheck;
    }

    /**
     * @param string $elementName
     * @return mixed
     */
    public function isElementVisible($elementName)
    {
        $xpath = $this->getElement($elementName)->getXpath();
        return $this->getDriver()->isVisible($xpath);
    }
}
