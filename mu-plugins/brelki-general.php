<?php
/**
 * Brelki General MU Plugin
 * Description: A collection of general-purpose functions and features for WordPress sites.
 * Version: 1.0.0
 * Author: Brelki Team
 * Author URI: https://brelki.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: brelki-general
 */

function brelki_remove_dashboard_widgets() {
  global $wp_meta_boxes;

  unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
  unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
  unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
  unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
  unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
  unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments'] );
  unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
  unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
  unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts'] );
  unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health'] );
  unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_dashboard_widget'] );
}

// Allow SVG uploads and sanitize SVG files on upload to remove scripts and event handlers.
// Adds MIME type, ensures WP accepts SVG, and sanitizes contents before saving.
add_filter('upload_mimes', function($mimes) {
  $mimes['svg']  = 'image/svg+xml';
  $mimes['svgz'] = 'image/svg+xml';
  return $mimes;
});

// Ensure WordPress treats .svg as a valid filetype when checking file extension and type.
add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
  $ext = pathinfo($filename, PATHINFO_EXTENSION);
  if (strtolower($ext) === 'svg') {
    $data['ext']  = 'svg';
    $data['type'] = 'image/svg+xml';
  }
  return $data;
}, 10, 4);

// Sanitize uploaded SVG files before they're moved to final uploads directory.
add_filter('wp_handle_upload_prefilter', function($file) {
  $filename = $file['name'];
  $ext = pathinfo($filename, PATHINFO_EXTENSION);
  if (strtolower($ext) !== 'svg' && strtolower($ext) !== 'svgz') {
    return $file;
  }

  // Read temporary file contents
  $tmp_name = $file['tmp_name'];
  if (!is_readable($tmp_name)) {
    return $file;
  }

  $svg = file_get_contents($tmp_name);
  if ($svg === false) {
    return $file;
  }

  // Basic sanitization: remove script/style tags and event handler attributes
  // This is not a full-proof sanitizer but reduces common attack vectors.
  libxml_use_internal_errors(true);
  $doc = new DOMDocument();
  $doc->preserveWhiteSpace = false;

  if (strpos($svg, '<svg') === false) {
    return $file;
  }

  if (!$doc->loadXML($svg, LIBXML_NONET)) {
    if (!$doc->loadHTML($svg, LIBXML_NONET)) {
      return $file;
    }
  }

  $removeTags = ['script', 'style', 'iframe', 'object', 'embed', 'foreignObject', 'link'];
  foreach ($removeTags as $tag) {
    $nodes = $doc->getElementsByTagName($tag);
    for ($i = $nodes->length - 1; $i >= 0; $i--) {
      $node = $nodes->item($i);
      if ($node) {
        $node->parentNode->removeChild($node);
      }
    }
  }

  $xpath = new DOMXPath($doc);
  foreach ($xpath->query('//@*') as $attr) {
    $name = $attr->nodeName;
    $value = $attr->nodeValue;

    if (stripos($name, 'on') === 0) {
      $attr->ownerElement->removeAttributeNode($attr);
      continue;
    }

    if (is_string($value) && preg_match('#^\s*javascript:#i', $value)) {
      $attr->ownerElement->removeAttributeNode($attr);
      continue;
    }

    if (is_string($value) && preg_match('#^\s*data:#i', $value) && !preg_match('#^\s*data:image/#i', $value)) {
      $attr->ownerElement->removeAttributeNode($attr);
      continue;
    }

    if (stripos($name, 'href') !== false && is_string($value) && preg_match('#^\s*javascript:#i', $value)) {
      $attr->ownerElement->removeAttributeNode($attr);
      continue;
    }
  }

  $sanitized = $doc->saveXML($doc->documentElement ?: $doc);
  if ($sanitized && trim($sanitized) !== '') {
    file_put_contents($tmp_name, $sanitized);
    $file['type'] = 'image/svg+xml';
  }

  return $file;
});

add_action( 'wp_dashboard_setup', 'brelki_remove_dashboard_widgets' );