<?php

namespace FlexPress\Plugins\BrokenLinks;

use FlexPress\Components\Hooks\Hooker;
use FlexPress\Components\PostType\Helper as PostTypeHelper;
use FlexPress\Components\Taxonomy\Helper as TaxHelper;
use FlexPress\Plugins\AbstractPlugin;

class BrokenLinks extends AbstractPlugin
{

    protected $postTypeHelper;
    protected $taxHelper;
    protected $hooker;

    public function __construct(PostTypeHelper $postTypeHelper, TaxHelper $taxHelper, Hooker $hooker)
    {

        $this->postTypeHelper = $postTypeHelper;
        $this->taxHelper = $taxHelper;
        $this->hooker = $hooker;

    }

    /**
     * Add the init hook so we can init the plugin
     *
     * @param $file
     * @author Tim Perry
     *
     */
    public function init($file)
    {
        parent::init($file);
        add_action('init', array($this, 'initHook'));

    }

    /**
     *
     * Used to setup the hooker, post type/tax helpers.
     *
     * @author Tim Perry
     *
     */

    public function initHook()
    {
        $this->hooker->hookUp();
        $this->taxHelper->registerTaxonomies();
        $this->postTypeHelper->registerPostTypes();
    }

}
