<?php

namespace FlexPress\Plugins\BrokenLinks\PostType;

use FlexPress\Components\PostType\AbstractPostType;
use FlexPress\Plugins\BrokenLinks\Hooks\BrokenLinks;

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

    public function getArgs()
    {
        return array_merge(
            parent::getArgs(),
            array(
                "public" => false,
                "supports" => "title",
                "_edit_link" => "edit.php?" . BrokenLinks::SUDO_EDIT_LINK . "=%d"
            )
        );
    }

}