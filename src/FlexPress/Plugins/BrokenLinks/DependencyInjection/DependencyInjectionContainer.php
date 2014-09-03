<?php

namespace FlexPress\Plugins\BrokenLinks\DependencyInjection;

use FlexPress\Components\Hooks\Hooker;
use FlexPress\Plugins\BrokenLinks\BrokenLinks as BrokenLinksPlugin;
use FlexPress\Plugins\BrokenLinks\Hooks\BrokenLinks as BrokenLinksHookable;
use FlexPress\Components\PostType\Helper as PostTypeHelper;
use FlexPress\Components\Taxonomy\Helper as TaxHelper;
use FlexPress\Plugins\BrokenLinks\PostType\BrokenLink;
use FlexPress\Plugins\BrokenLinks\Taxonomy\BrokenLinkType;

class DependencyInjectionContainer extends \Pimple
{

    public function init()
    {

        $this['objectStorage'] = $this->factory(
            function () {
                return new \SplObjectStorage();
            }
        );

        $this['brokenLinksHookable'] = function () {
            return new BrokenLinksHookable();
        };

        $this['hooker'] = function ($c) {
            return new Hooker($c['objectStorage'], array(
                $c['brokenLinksHookable']
            ));
        };

        $this['brokenLinkTypeTax'] = function () {
            return new BrokenLinkType();
        };

        $this['taxHelper'] = function ($c) {
            return new TaxHelper($c['objectStorage'], array(
                $c["brokenLinkTypeTax"]
            ));
        };

        $this['brokenLinkPostType'] = function () {
            return new BrokenLink();
        };

        $this['postTypeHelper'] = function ($c) {
            return new PostTypeHelper($c['objectStorage'], array(
                $c["brokenLinkPostType"]
            ));
        };

        $this['BrokenLinks'] = function ($c) {
            return new BrokenLinksPlugin($c['postTypeHelper'], $c['taxHelper'], $c['hooker']);
        };

    }
}
