<?php

declare(strict_types=1);

namespace Berecont\ContaoHyperlinkSimpleBundle\Controller\ContentElement;

use Berecont\ContaoHyperlinkSimpleBundle\Service\AttributePolicy;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class HyperlinkSimpleController extends AbstractContentElementController
{
    public function __construct(
        private readonly ScopeMatcher $scopeMatcher,
        private readonly AttributePolicy $attributePolicy,
    ) {}

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $items = StringUtil::deserialize($model->hyperlinks, true);

        foreach ($items as &$item) {
            $item['relTokens'] = StringUtil::deserialize($item['relTokens'] ?? '', true);
            $item['target']    = !empty($item['target']);
            $item['download']  = !empty($item['download']);

            // customAttributes parsen & prüfen
            $custom = (string)($item['customAttributes'] ?? '');
            $attrs  = [];

            if ($custom !== '') {
                foreach (preg_split('/\r\n|\r|\n/', $custom) as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }

                    $name  = $line;
                    $value = '';
                    if (str_contains($line, '=')) {
                        [$name, $value] = array_map('trim', explode('=', $line, 2));
                        $value = preg_replace('/^"(.*)"$/', '$1', $value);
                        $value = preg_replace("/^'(.*)'$/", '$1', $value);
                    }

                    // Konfigurierbare Policy verwenden
                    if (!$this->attributePolicy->isAllowed($name)) {
                        continue;
                    }

                    $attrs[$name] = $value;
                }
            }

            $item['customAttributes'] = $attrs;
        }
        unset($item);

        $template->set('hyperlinks', $items);

        if ($this->scopeMatcher->isBackendRequest($request)) {
            $template->setName('@Contao/backend/be_hyperlinksimple');
        }

        return $template->getResponse();
    }
}