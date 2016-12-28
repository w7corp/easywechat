<?php
namespace EasyWeChat\OpenPlatform\Traits;

use EasyWeChat\OpenPlatform\VerifyTicket as ComponentVerifyTicket;

trait VerifyTicket
{
    /**
     * Verify Ticket.
     *
     * @var \EasyWeChat\OpenPlatform\VerifyTicket
     */
    protected $verifyTicket;

    /**
     * Set verify ticket instance.
     *
     * @param \EasyWeChat\OpenPlatform\VerifyTicket $verifyTicket
     *
     * @return $this
     */
    public function setVerifyTicket(ComponentVerifyTicket $verifyTicket)
    {
        $this->verifyTicket = $verifyTicket;

        return $this;
    }

    /**
     * Get verify ticket instance.
     *
     * @return \EasyWeChat\OpenPlatform\VerifyTicket
     */
    public function getVerifyTicket()
    {
        return $this->verifyTicket;
    }
}