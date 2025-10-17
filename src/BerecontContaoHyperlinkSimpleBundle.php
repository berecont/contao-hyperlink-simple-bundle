<?php

declare(strict_types=1);

namespace Berecont\ContaoHyperlinkSimpleBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Berecont\ContaoHyperlinkSimpleBundle\DependencyInjection\BerecontContaoHyperlinkSimpleExtension;

final class BerecontContaoHyperlinkSimpleBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        // sorgt dafür, dass deine Extension + Configuration geladen werden
        return new BerecontContaoHyperlinkSimpleExtension();
    }
}