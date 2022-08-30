<?php
function shortcode_footnote($atts, $content = null) {
    extract(shortcode_atts(array(
        'title' => ''
    ),$atts));
    return '<sup class="footnote-ref js-article-aside-trigger"><i class="ref-txt">'.$title.'</i><i class="close-txt">×</i></sup><cite class="article-aside footnote short-crop left" id="cite_right_'.$title.'" style=""><span class="article-aside-txt"><span class="footnote-num">'.$title.'.</span>'.$content.'</span></cite>';
}
add_shortcode('footnote', 'shortcode_footnote');