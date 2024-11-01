<?php
/*
Plugin Name: WP ajax collapsing categories
Plugin URI: http://html5beta.com/wordpress/wp-ajax-collapsing-categories/
Description: Uses jQuery&ajax to expand and collapse categories to show the posts that belong to the category,nice and clean,more friendly to search engine 
Author: ZHAO Xudong
Version: 1.3
Author URI: http://html5beta.com
Tags: sidebar, widget, categories, menu, navigation, posts
*/

/*
    Copyright 2011  ZHAO Xudong  (email : zxdong@gmail.com)
	
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
* a function to check if post_is_in_descendant_category
* @param $cats the category object to check
* @return true if post_is_in_descendant_category or false if not in
*/
    function zxd_post_is_in_descendant_category( $cats, $_post = null ){
		foreach ( (array) $cats as $cat ) {
			$descendants = get_term_children( (int) $cat, 'category');
			if ( $descendants && in_category( $descendants, $_post ) ) return true;
		}
		return false;
	}
	/**
	* get all top level categories
	* @return top level categories objects array
	*/
	function getTopLevelCats() {
		$allCats = get_all_category_ids();
		$cats = array();
		foreach ($allCats as $id){
			$obj = get_category($id);
			$name = get_cat_name($id);
			if (!$obj -> parent) array_push($cats,$obj);
		}
		return $cats;
	}
	/**
	* wp ajax collapsing categories class
	*/
	class ajax_cc_Widget extends WP_Widget{
		function ajax_cc_Widget() {
			$widget_ops = array('classname' => 'widget_ajax_cc', 'description' => 'WP ajax Collapsing Categories' );
			$this->WP_Widget('ajax_cc_widget','ajax_cc_widget',$widget_ops);
		}
		/**
		* implent widget fucntion
		*/
		function widget($args, $instance) {
			extract($args, EXTR_SKIP);
			echo $before_widget;
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
			echo '<ul class="zxd_ajax_cc"' ;
			echo ' zid="';
			the_ID();
			echo '">';
			if(!$instance['zxdIncludeCats']) {
				$zxdCatArrayPool = getTopLevelCats();
			}
			else {
				$zxdCatArrayPool = array();
				$zxdCatTempPool = preg_split('/[,]+/',$instance['zxdIncludeCats']);
				sort($zxdCatTempPool);
				foreach($zxdCatTempPool as $a) {
					if(!get_category_by_slug($a)) {
						$zxdCatArrayPool = getTopLevelCats();
						break;
					}
					$obj = get_category_by_slug($a);
					array_push($zxdCatArrayPool,$obj);
				}
			}
			$zxdPostId = is_single()?get_the_ID():0;
			foreach ($zxdCatArrayPool as $zxdCatObj){
				$zxdCatid = $zxdCatObj -> term_id;
				$zxdCatdesc = $zxdCatObj->description;
				$zxdCatCount = $zxdCatObj->count; 
				echo '<li class="zxd_ajax_cc_li"><span class="zxd_expand">[+]</span>'.'<a '; 
				if(in_category( $zxdCatid,$zxdPostId)||zxd_post_is_in_descendant_category($zxdCatid,$zxdPostId)) {
					echo 'class="zxd_current_cat" '; 
				}
				echo 'href="'.get_category_link($zxdCatid).'" '.'title="'.$zxdCatdesc.'">'.get_cat_name( $zxdCatid ).'</a>'.'('.$zxdCatCount.')'.'</li>';
			}
			echo '</ul>' ;
			echo $after_widget;
		}
		/**
		* implent widget fucntion
		*/
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$title = strip_tags($new_instance['title']);
			$zxdIncludeCats=$new_instance['zxdIncludeCats'];
			$instance = compact('title','zxdIncludeCats');
			return $instance;
		}
		/**
		* implent widget fucntion
		*/
		function form($instance) {
			$defaults=array(
			'title' => __('ajax Categories', 'WP ajax collapsing categories'),
			'zxdIncludeCats' => '',
			);
			$options = wp_parse_args( $instance,$defaults );
			extract($options);  
			?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
            <p>include these categories (category slugs,separated by commas): </p>
            <p><input type="text" name="<?php echo $this->get_field_name('zxdIncludeCats'); ?>" value="<?php echo $zxdIncludeCats ?>" id="<?php echo $this->get_field_id('zxdIncludeCats') ?>"</input> </p>
			<?php
        }
    }
	/**
	* register_widget
	*/
	function zxd_widgets_init() {
		register_widget('ajax_cc_Widget');
	}
	add_action( 'widgets_init', 'zxd_widgets_init' );
	if ( !is_admin() ) {
		function zxd_init_method() {
			wp_register_script( 'wp-ajax-collapsing-categories', plugins_url('/wp-ajax-collapsing-categories.js',__FILE__),array('jquery'),'1.0',true); 
			wp_enqueue_script( 'wp-ajax-collapsing-categories');
			wp_localize_script( 'wp-ajax-collapsing-categories', 'ajaxCC', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )) );
		}
		add_action('init', 'zxd_init_method'); 
	}
	add_action( 'wp_ajax_nopriv_zxd_cc_submit', 'zxd_cc_submit' );
	add_action( 'wp_ajax_zxd_cc_submit', 'zxd_cc_submit');
	function zxd_cc_submit(){ 
	    $zxdCatUrl = $_POST['catZxd'];
		$zxdCurrentPostID =  (int)$_POST['currentID'];
		$items1 = explode('/',$zxdCatUrl);
		$zxdCatSlug =  $items1[count($items1)-1]?$items1[count($items1)-1]:$items1[count($items1)-2];
		$items2 = explode('=',$zxdCatSlug);
		if($items2[0] == '?cat') $zxdCatId = (int)$items2[1];
		else{
			$zxdCatObj = get_category_by_slug($zxdCatSlug);
			$zxdCatId = $zxdCatObj->term_id;
		}
		$zxdArgs = array(
		'parent'                   => $zxdCatId,
		'orderby'                  => 'slug',
		'order'                    => 'ASC',
		'hide_empty'               => 1,
		);
		$zxdChildCats = get_categories( $zxdArgs );
		echo '<ul class="child_cat hide">' ;
		if(!empty($zxdChildCats)){
			$zxdChildCatsIdArray =array();
			foreach($zxdChildCats as $zxdtemp1){
				array_push($zxdChildCatsIdArray, $zxdtemp1->term_id);
			}
		$zxdArgs2 = array(
		'post_type' => 'post',
		'category__in'  => array($zxdCatId),
		'category__not_in' => $zxdChildCatsIdArray,
		);
		foreach ($zxdChildCats as $zxdEachCatChild){
			$zxdCatChildId =  $zxdEachCatChild ->term_id;
			$zxdCatChildDesc = $zxdEachCatChild->description;
			$zxdCatChildCount = $zxdEachCatChild->count;
			if ( in_category( $zxdCatChildId,$zxdCurrentPostID )||zxd_post_is_in_descendant_category( $zxdCatChildId,$zxdCurrentPostID )) {
				echo '<li class="zxd_ajax_cc_li"><span class="zxd_expand">[+]</span>'.'<a class="zxd_current_cat" href="'.get_category_link($zxdCatChildId).'" '.'title="'.$zxdCatChildDesc.'">'.get_cat_name( $zxdCatChildId ).'</a>'.'('.$zxdCatChildCount.')'.'</li>';
			}
			else{
				echo '<li class="zxd_ajax_cc_li"><span class="zxd_expand">[+]</span>'.'<a href="'.get_category_link($zxdCatChildId).'" '.'title="'.$zxdCatChildDesc.'">'.get_cat_name( $zxdCatChildId ).'</a>'.'('.$zxdCatChildCount.')'.'</li>';
			} 
		};
	}
	else{
		$zxdArgs2 = array(
		'post_type' => 'post',
		'category__in'  => array($zxdCatId),
		);   
	};
	$zxdQuery = new WP_Query($zxdArgs2);
	if($zxdQuery->have_posts()){
		while ($zxdQuery->have_posts()) { 
		    $zxdQuery->the_post();
			echo '<li class="zxd_ajax_cc_li"><a';
			if  ($zxdQuery->post->ID==$zxdCurrentPostID) echo ' class="zxd_current_cat"';
			echo ' href="';
			echo the_permalink();
			echo '" rel="bookmark" title="';
			echo  the_title_attribute();
			echo '">';
			echo  the_title();
			echo '</a></li>';
		}
	}
	echo '</ul>' ; 
	exit;
}
?>

