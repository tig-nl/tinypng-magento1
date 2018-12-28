<?php
class Tiny_CompressImages_Block_Adminhtml_System_Config_Form_Field_Credits
    extends Varien_Data_Form_Element_Abstract
{
    const TINY_COMPRESSIMAGES_BASE_UPGRADE_URL = 'https://tinypng.com/dashboard/api?type=upgrade&mail=';

    /**
     * @var Tiny_CompressImages_Helper_Data
     */
    protected $_helper = null;

    /**
     * The constructor
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        $this->_helper = Mage::helper('tiny_compressimages');
    }

    /**
     * Generate the amount of credits remaining for the CompressImages extension.
     *
     * @return string
     */
    public function getElementHtml()
    {
        /** @var Tiny_CompressImages_Helper_Config $configHelper */
        $configHelper = Mage::helper('tiny_compressimages/config');

        if (!$configHelper->isConfigured()) {
            if (!$configHelper->getApiKey()) {
                return '<span class="compressimages-api-deactivated">'
                    . $this->_helper->__('Please enter your api key to check the amount of compressions left.')
                    . '</span>';
            }

            if (!$configHelper->isEnabled()) {
                return '<span class="compressimages-api-deactivated">'
                    . $this->_helper->__('Please enable the extension to check the amount of compressions left.')
                    . '</span>';
            }
        }

        $payingState      = Mage::helper('tiny_compressimages/tinify')->getPayingState();
        $remainingCredits = Mage::helper('tiny_compressimages/tinify')->getRemainingCredits();

        if (!$remainingCredits && $payingState !== 'free') {
            $remainingCredits = 'unlimited';
        }

        $resultString = $this->_helper->__(
            'You are on a <b>%s plan</b> with <b>%s</b> compressions left this month.', $payingState, $remainingCredits
        );

        if ($payingState === 'free') {
            $resultString = $this->addUpgradeButton($resultString);
        }

        return $resultString;
    }

    /**
     * @param $resultString
     *
     * @return string
     */
    private function addUpgradeButton($resultString)
    {
        $apiEmail         = Mage::helper('tiny_compressimages/tinify')->getApiEmail();
        $upgradeUrl       = self::TINY_COMPRESSIMAGES_BASE_UPGRADE_URL . $apiEmail;

        $resultString .= $this->_helper->__(
            '<br/><br/><span class="tinypng-upgrade-text">Remove all limitations? Visit your TinyPNG dashboard to upgrade your account.</span>'
            . '<a href="%s" class="tinypng-upgrade-button" target="_blank">Upgrade Plan</a>',
            $upgradeUrl
        );

        return $resultString;
    }
}
