<?php

namespace Framework\RestApi\Test\Dummies;

use Framework\Base\Mailer\Mailer;
use Framework\Base\Mailer\MailInterface;

class DummySendGrid extends Mailer
{
    public function send(MailInterface $mail)
    {
        return 'Sent';
    }
}
