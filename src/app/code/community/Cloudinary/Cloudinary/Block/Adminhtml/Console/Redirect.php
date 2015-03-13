<?php

class Cloudinary_Cloudinary_Block_Adminhtml_Console_Redirect extends Mage_Adminhtml_Block_Abstract
{

    public function build()
    {
        return $this->getLayout()
            ->createBlock('core/text')
            ->setText(
                sprintf(
                    '<script type="text/javascript">window.open("%s");window.history.back();</script>',
                    $this->getRedirectUrl()
                )
            );
    }

}