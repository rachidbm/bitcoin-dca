<?php

declare(strict_types=1);

/*
 * This file is part of the Bitcoin-DCA package.
 *
 * (c) Jorijn Schrijvershof <jorijn@jorijn.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jorijn\Bitcoin\Dca\EventListener\Notifications;

use Jorijn\Bitcoin\Dca\Event\BuySuccessEvent;

class SendEmailOnBuyListener extends AbstractSendEmailListener
{
    public const NOTIFICATION_SUBJECT_LINE = 'You bought %s sats on %s';

    public function onBuy(BuySuccessEvent $event): void
    {
        if (!$this->isEnabled) {
            return;
        }

        $templateVariables = array_merge(
            [
                'buyOrder' => $event->getBuyOrder(),
                'tag' => $event->getTag(),
            ],
            $this->getTemplateVariables()
        );

        $html = $this->renderTemplate($this->templateLocation, $templateVariables);

        $email = $this->createEmail()
            ->subject(
                sprintf(
                    '[%s] %s',
                    $this->emailConfiguration->getSubjectPrefix(),
                    sprintf(
                        self::NOTIFICATION_SUBJECT_LINE,
                        number_format($event->getBuyOrder()->getAmountInSatoshis()),
                        ucfirst($this->templateInformation->getExchange())
                    )
                )
            )
            ->html($html)
            ->text($this->htmlConverter->convert($html))
        ;

        $this->notifier->send($email);
    }
}
