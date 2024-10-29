<?php
/*
Plugin Name: Auto Post Title
Plugin URI: http://www.thoughtlab.com/blog/index.php/auto-post-title/
Description: Use shortcodes to automatically set the display title of all posts, pages, or any custom post type according to the format you provide.
Version: 1.2.2
Author: ThoughtLab
Author URI: http://www.thoughtlab.com/blog/index.php/category/wordpress/
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

You should have received a copy of the GNU General Public License 
along with this program; if not, write to the Free Software 
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

// create custom plugin settings menu
add_action('admin_menu', 'apt_create_menu');

function apt_create_menu() {

	//create new top-level menu
	add_options_page('Auto Post Title Settings', 'Title Format', 'administrator', __FILE__, 'apt_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_apt_settings' );
}


function sanitize_opt($value){
	return addslashes( html_entity_decode($value));
}
function register_apt_settings() {
	$post_types = get_post_types(array('public'=>true),'names');
    foreach($post_types as $type){
		if($type != 'attachment'){
    		register_setting( 'apt-settings', 'apt_'.$type, 'sanitize_opt' );
		}
    }
}

function apt_settings_page() {
	function check_option($option){
		if(get_option($option) == 'on'){
			echo ' checked="checked"';
		}
	}
?>
<div class="wrap">
<h2>Auto Post Title Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'apt-settings' ); ?>
	<p>Use the following format to inject data into the post title.</p>
	<p><strong><em>[author] - [title] ([date format="n/j/Y"])</em></strong><br/>
	will return:<br/>
	<strong><em>John - A New Post With a Title (2/17/2010)</em></strong></p>
	<p>Here are some available shortcodes:</p>
	<ol>
		<li>id</li>
		<li>title</li>
		<li>author</li>
		<li>date (<em>use the attribute: format="" to format date. <a href="http://php.net/manual/en/function.date.php">Click Here</a> for formatting info.</em>)</li>
		<li>modified (<em>also takes the format="" attribute.</em>)</li>
		<li>category (<em>only the first category will be shown.</em>)</li>
		<li>content</li>
		<li>excerpt</li>
		<li>status (<em>published, draft, etc.</em>)</li>
		<li>type (<em>the post type</em>)</li>
		<li>name (<em>the post slug</em>)</li>
		<li>comments (<em>show number of comments</em>)</li>
	</ol>
	<p>Each shortcode will take the attribute: case="", which will convert the text of the shortcode to the specified case.<br/>
	The values available are:
		<ol>
			<li>upper</li>
			<li>lower</li>
			<li>first (<em>the first letter of the first word will be capitalized</em>)</li>
			<li>words (<em>the first letter of each word will be capitalized</em>).</li>
		</ol>
	</p>
	<p>You can also use custom fields. For example, if you had a custom field with a key of "foo," you would use the tag <strong><em>[foo]</em></strong>.<br/>If there are multiple instances of that custom field, only the first will be used.</p>
    <table class="form-table"><?php
    $post_types = get_post_types(array('public'=>true),'names');
    foreach($post_types as $type){
    if($type != 'attachment'){?>
		<tr>
			<td id="titlediv">
				<h3><?php echo ucwords($type); ?> Title</h3>
				<input size="30" type="text" id="title" name="<?php echo 'apt_'.$type; ?>" value="<?php echo htmlentities(stripslashes(get_option('apt_'.$type))); ?>"/>
			</td>
        </tr><?php
    	}
    }
	
    ?>
        
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

    </form>
    </div>
   <?php }
   add_filter('the_title','apt_title_filter');
   add_filter('single_post_title','apt_title_filter');
   function apt_title_filter($title){
    global $post;
	if($post->post_title != $title){ return $title; }
    $new_title_format = stripslashes(html_entity_decode(get_option('apt_'.$post->post_type)));
    if($new_title_format != ''){
    	$new_title = $new_title_format;
    	preg_match_all('/\[([^\]]*)\]/',$new_title_format,$tags);
    	//var_dump($tags[1]);
    	foreach($tags[1] as $tag){
    		$t = current(explode(' ',$tag));
    		//$val = $post->post_.$tag;
    		switch($t){
    			case 'id':
    				$text = $post->ID;
    				break;
    			case 'title':
    				$text = $title;
    				break;
    			case 'author':
    				$author = get_userdata($post->post_author);
    				$text = $author->display_name;
    				break;
    			case 'date':
    				$text = date('n/j/Y',strtotime($post->post_date));
    				break;
    			case 'modified':
    				$text = date('n/j/Y',strtotime($post->post_modified));
    				break;
    			case 'category':
					$cats = get_the_category($post->ID);
    				$text = $cats[0]->cat_name;
    				break;
    			case 'content':
    				$text = $post->post_content;
    				break;
    			case 'excerpt':
    				$text = get_the_excerpt();
    				break;
    			case 'status':
    				$text = ucwords($post->post_status);
    				break;
    			case 'name':
    				$text = $post->post_name;
    				break;
    			case 'comments':
    				$text = $post->comment_count;
    				break;
    			case 'type':
    				$text = ucwords($post->post_type);
    				break;
    			default:
    				$text = get_post_meta($post->ID,$t,true);
    				if(!$text) $text = '';		
    				break;
    		}
			$terms = wp_get_post_terms($post->ID,$t);
			if(is_array($terms) && sizeof($terms) > 0){
				$text = $terms[0]->name;
			}
			
    		if(preg_match('/\s/',$tag)){
    			$att = end(explode(' ',$tag));
    			$a = current(explode('=',$att));
    			$v = str_replace("'",'',end(explode('=',$att)));
    			$v = str_replace('"','',$v);/*
    			var_dump($atts);
    			if(is_array($atts)){
    				foreach($atts as $att){
    				}
    			}*/
    			switch($a){
    				case 'format':
    					$text = date($v,strtotime($text));
    					break;
    				case 'case':
    					switch($v){
    						case 'upper':
    							$text = strtoupper($text);
    							break;
    						case 'lower':
    							$text = strtolower($text);
    							break;
    						case 'first':
    							$text = ucfirst($text);
    							break;
    						case 'words':
    							$text = ucwords($text);
    							break;
						}
    					break;
    				default:
    					break;
    			}
    		}
    		$new_title = str_replace('['.$tag.']',$text,$new_title);
    	}
    }else {
    	$new_title = $title;
    }
    return $new_title;
   }
?>