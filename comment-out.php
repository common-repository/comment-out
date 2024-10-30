<?php
/*

    Plugin Name: Comment-Out
    Plugin URI: https://lachlanallison.com/comment-out
    Description: A plugin to comment out text in posts/pages.
    Version: 1.1
    Author: Lachlan Allison
    Author URI: https://lachlanallison.com
    License: GPLv3 or later
    License URI: https://opensource.org/licenses/GPL-3.0

    Comment-Out is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    any later version.
    
    Comment-Out is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with Comment-Out. If not, see https://opensource.org/licenses/GPL-3.0.

*/
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function get_contents($str, $startDelimiter, $endDelimiter) {
    $contents = array();
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = $contentStart = $contentEnd = 0;
    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
      $contentStart += $startDelimiterLength;
      $contentEnd = strpos($str, $endDelimiter, $contentStart);
      if (false === $contentEnd) {
        break;
      }
      $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
      $startFrom = $contentEnd + $endDelimiterLength;
    }
  
    return $contents;
}

function co_comment_out($atts, $content = null){
    if (!empty($content))
        return '';
}

function co_reg_list_page() {
    add_options_page('Comments List', 'Comments List', 'manage_options', 'comment-out', 'co_list_page');
}

function co_list_page() {
    ?>

    <div>
    <h1>List of comments</h1>
    <?php 
    global $wpdb;
    $results = $wpdb->get_results("SELECT `post_title`, `post_content`, `id` FROM $wpdb->posts WHERE `post_status` = 'publish' AND `post_content` LIKE '%[comment]%[/comment]%';", ARRAY_A);
    foreach ($results as $arr){

        echo '<h2>Post Title:</h2> ' . $arr['post_title'];
        echo '<br><a href="' . get_site_url() . '/wp-admin/post.php?post=' . $arr['id'] . '&action=edit" >Edit Post</a>';
        $comments = get_contents($arr['post_content'], '[comment]', '[/comment]');
        foreach ($comments as $comment){
            echo '<br><h2>Comment:</h2> ' . $comment;
        }
        echo '<br><hr>';
    }
    ?>
    </div>  
    <?php
}

add_shortcode('comment', 'co_comment_out');
add_action('admin_menu', 'co_reg_list_page');
?>
