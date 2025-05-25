<?php
/**
 * Plugin Name: Hide Internal Tags
 * Description: Hides internal tags (translate, translated) from non-logged-in users and a>
 */

function filter_terms($terms, $hidden_slugs) {
    if (is_user_logged_in() || !is_array($terms)) {
        return $terms;
    }

    return array_filter($terms, function($term) use ($hidden_slugs) {
        return !in_array($term->slug, $hidden_slugs);
    });
}

function hide_internal_terms($terms, $post_id, $taxonomy) {
    if ($taxonomy !== 'post_tag') {
        return $terms;
    }

    return filter_terms($terms, array('translate', 'translated'));
}
add_filter('get_the_terms', 'hide_internal_terms', 10, 3);

function hide_internal_tags($tags) {
    return filter_terms($tags, array('translate', 'translated'));
}
add_filter('get_the_tags', 'hide_internal_tags');

// Lisää noindex-tagin tietyille tageille
function hit_noindex_meta_for_hidden_tags() {
    if (is_tag(array('translate', 'translated'))) {
        echo '<meta name="robots" content="noindex, nofollow">' . PHP_EOL;
    }
}
add_action('wp_head', 'hit_noindex_meta_for_hidden_tags');
