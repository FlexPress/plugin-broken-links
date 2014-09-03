<?php

namespace FlexPress\Plugins\BrokenLinks\Taxonomy;

use FlexPress\Components\Taxonomy\AbstractTaxonomy;
use FlexPress\Plugins\BrokenLinks\PostType\BrokenLink;

class BrokenLinkType extends AbstractTaxonomy
{

    const TAX_NAME = 'broken-link-type';

    /**
     * Gets the name of the taxonomy
     *
     * @return string
     * @author Tim Perry
     */
    public function getName()
    {
        return self::TAX_NAME;
    }

    /**
     * Get a array of supported post types
     *
     * @return array
     * @author Tim Perry
     */
    public function getSupportedPostTypes()
    {
        return array(BrokenLink::POST_TYPE_NAME);
    }

    public function getPluralName()
    {
        return "Broken Link Types";
    }

    public function getSingularName()
    {
        return "Broken Link Type";
    }

}