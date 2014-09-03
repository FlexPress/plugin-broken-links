<?php

namespace FlexPress\Plugins\BrokenLinks\Hooks;

use FlexPress\Components\Hooks\HookableTrait;

class BrokenLinks
{

    use HookableTrait;

    const POST_TYPE_NAME = "broken-link";
    const TAX_NAME_TYPE = "broken-link-type";

    const TERM_NAME_INTERNAL = "Internal";
    const TERM_NAME_EXTERNAL = "External";

    const SUDO_EDIT_LINK = "broken-link-edit";

    const META_NAME_OFFENDING_POST_ID = "fpt_offending_post_id";

    /**
     *
     *  Removes the date column from the broken link listings.
     *
     * @type filter
     *
     * @param $columns
     *
     * @return mixed
     * @author Tim Perry
     *
     */
    public function managePostsColumns($columns)
    {

        if (get_post_type() === $this::POST_TYPE_NAME) {
            unset($columns['date']);
        }

        return $columns;

    }

    /**
     *
     * Redirects the edit page to the page that has the broken link on
     * instead of editing the actual broken link itself.
     *
     * @author Tim Perry
     * @type action
     *
     */
    public function init()
    {

        if (isset($_GET[$this::SUDO_EDIT_LINK])) {

            $post_id = get_post_meta($_GET[$this::SUDO_EDIT_LINK], $this::META_NAME_OFFENDING_POST_ID, true);
            wp_redirect(get_edit_post_link($post_id, "edit"));
            exit;

        }

    }

    /**
     * Usees css to hide some of the features for the broken links post type.
     *
     * @type action
     */
    public function adminHead()
    {

        if (get_post_type() === $this::POST_TYPE_NAME) {
            ?>

            <style type="text/css">
                #posts-filter .tablenav, .add-new-h2, .subsubsub, .search-box {
                    display: none;
                }

                #posts-filter {
                    margin-top: 20px;
                }
            </style>

        <?php
        }

    }

    /**
     * Adds a sub meny page to run the link checker
     *
     * @author Tim Perry
     * @type action
     *
     */
    public function adminMenu()
    {

        add_submenu_page(
            'edit.php?post_type=' . $this::POST_TYPE_NAME,
            'Run Link Checker',
            'Run Link Checker',
            'edit_posts',
            'broken-link-checker',
            array($this, "subPageCallback")
        );

    }

    /**
     * Removes the quick edit functionality
     *
     * @type action
     *
     * @return mixed
     * @author Tim Perry
     *
     */
    public function postRowActions($actions)
    {
        if (get_post_type() === $this::POST_TYPE_NAME) {

            unset($actions['edit']);
            unset($actions['view']);
            unset($actions['trash']);
            unset($actions['inline hide-if-no-js ']);

        }

        return $actions;

    }

    /**
     *
     * Outputs the markup for the link checker subpage
     *
     * @author Tim Perry
     *
     */
    public function subPageCallback()
    {

        ?>
        <div class="wrap">
            <div id="icon-link-manager" class="icon32"></div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h2>Broken Link Checker</h2>

                <p>Please note checking for broken links can take a very long time! Please note this also deletes the
                    old results.</p>
                <?php

                submit_button("Check site for broken links");

                if (isset($_REQUEST["submit"])) {

                    $this->updateBrokenLinks();
                    echo "<p><b>Updated broken links.</b></p>";

                }
                ?>
            </form>
        </div>
    <?php

    }

    /**
     *
     * Returns a list of broken links,
     *
     * @return array
     * @author Tim Perry
     *
     */
    protected function updateBrokenLinks()
    {

        global $wpdb;

        // remove the old broken links
        if ($old_link_ids = $wpdb->get_results(
            $wpdb->prepare("select ID from $wpdb->posts where post_type = %s", $this::POST_TYPE_NAME)
        )
        ) {

            foreach ($old_link_ids as $link_id) {

                $wpdb->query($wpdb->prepare("delete from $wpdb->post_meta where post_id = %d", $link_id));
                $wpdb->query($wpdb->prepare("delete from $wpdb->posts where ID = %d", $link_id));

            }

        }

        // update links
        if ($posts_with_links = $wpdb->get_results(
            "select * from $wpdb->posts as p where p.post_content like '%<a%</a>%' and p.post_status = 'publish' "
        )
        ) {

            foreach ($posts_with_links as $posts_with_link) {

                if (preg_match_all('/<a[^>]* href="([^"\']*)".*>(.+)<\/a>/', $posts_with_link->post_content, $links)) {

                    foreach ($links[1] as $link) {

                        $type = self::TERM_NAME_EXTERNAL;

                        if (stripos($link, "http") === false) {

                            $link = FPT_SITE_URL . $link;
                            $type = self::TERM_NAME_INTERNAL;

                        }

                        if ($this->linkIsBroken($link)) {

                            if (get_page_by_title($link, OBJECT, $this::POST_TYPE_NAME) === null) {

                                if ($post_id = wp_insert_post(
                                    array(
                                        "post_title" => $links[2][0] . " [" . $link . "]",
                                        "post_type" => $this::POST_TYPE_NAME,
                                        "post_status" => "publish"
                                    )
                                )
                                ) {

                                    add_post_meta($post_id, $this::META_NAME_OFFENDING_POST_ID, $posts_with_link->ID);
                                    wp_set_object_terms($post_id, $type, $this::TAX_NAME_TYPE);

                                }

                            }

                        }

                    }


                }

            }

        }

    }

    /**
     *
     * Returns if a link is broken
     *
     * @param $link
     *
     * @return bool
     * @author Tim Perry
     *
     */
    public static function linkIsBroken($link)
    {

        if (stripos("mailto:", $link)) {
            return false;
        }

        if ($link === '#') {
            return false;
        }

        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);

        $is_broken = (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200);
        curl_close($ch);

        return $is_broken;

    }

}