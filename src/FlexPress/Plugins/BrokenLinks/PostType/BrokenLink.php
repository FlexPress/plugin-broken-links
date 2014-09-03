<?php

namespace FlexPress\Plugins\BrokenLinks\PostType;

use FlexPress\Components\PostType\AbstractPostType;

class BrokenLink extends AbstractPostType
{

    const POST_TYPE_NAME = 'broken-link';

    /**
     * Gets the name of the taxonomy
     *
     * @return string
     * @author Tim Perry
     */
    public function getName()
    {
        return self::POST_TYPE_NAME;
    }

    public function getPluralName()
    {
        return "Broken Links";
    }

    public function getSingularName()
    {
        return "Broken Link";
    }

}