<?php 
/**
 * Perform all main WooCommerce configurations for this theme
 *
 * @package  Open Shop WordPress theme
 */
// If plugin - 'WooCommerce' not exist then return.
if ( ! class_exists( 'WooCommerce' ) ){
	   return;
}
/*******************************/
/** Sidebar Add Cart Product **/
/*******************************/
if ( ! function_exists( 'open_shop_cart_total_item' ) ){
  /**
   * Cart Link
   * Displayed a link to the cart including the number of items present and the cart total
   */
 function open_shop_cart_total_item(){
   global $woocommerce; 
  ?>
  <a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart','open-shop' ); ?>"><i class="fa fa-shopping-basket"></i> <span class="cart-content"><?php echo sprintf ( _n( '<span class="count-item">%d <span class="item">'.__('item','open-shop').'</span></span>', '<span class="count-item">%d <span class="item">'.__('items','open-shop').'</span></span>', WC()->cart->get_cart_contents_count() ,'open-shop'), WC()->cart->get_cart_contents_count(),'open-shop' ); ?><?php echo WC()->cart->get_cart_total(); ?></span></a>
  <?php }
}
//cart view function
function open_shop_menu_cart_view($cart_view){
	global $woocommerce;
    $cart_view= open_shop_cart_total_item();
    return $cart_view;
}
add_action( 'open_cart_count','open_shop_menu_cart_view');

function open_shop_woo_cart_product(){
global $woocommerce;
?>
<div id="open-cart" class="open-cart">
<div class="open-quickcart-dropdown">
<?php 
woocommerce_mini_cart(); 
?>
</div>
</div>
    <?php
}
add_action( 'open_shop_woo_cart', 'open_shop_woo_cart_product' );
add_filter('woocommerce_add_to_cart_fragments', 'open_shop_add_to_cart_dropdown_fragment');
function open_shop_add_to_cart_dropdown_fragment( $fragments ){
   global $woocommerce;
   ob_start();
   ?>
   <div class="open-quickcart-dropdown">
       <?php woocommerce_mini_cart(); ?>
   </div>
   <?php $fragments['div.open-quickcart-dropdown'] = ob_get_clean();
   return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'open_shop_add_to_cart_fragment');
function open_shop_add_to_cart_fragment($fragments){
        ob_start();?>

          <a class="cart-contents" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e( 'View your shopping cart','open-shop' ); ?>"><i class="fa fa-shopping-basket"></i> <span class="cart-content"><?php echo sprintf ( _n( '<span class="count-item">%d <span class="item">'.__('item','open-shop').'</span></span>', '<span class="count-item">%d <span class="item">'.__('items','open-shop').'</span></span>', WC()->cart->get_cart_contents_count(),'open-shop' ), WC()->cart->get_cart_contents_count() ); ?><?php echo WC()->cart->get_cart_total(); ?></span></a>

       <?php  $fragments['a.cart-contents'] = ob_get_clean();

        return $fragments;
    }
/***********************************************/
//Sort section Woocommerce category filter show
/***********************************************/
function open_shop_add_to_cart_url($product){
 $cart_url =  apply_filters( 'woocommerce_loop_add_to_cart_link',
    sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button th-button %s %s"><span>%s</span></a>',
        esc_url( $product->add_to_cart_url() ),
        esc_attr( $product->get_id() ),
        esc_attr( $product->get_sku() ),
        esc_attr( isset( $quantity ) ? $quantity : 1 ),
        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
        $product->is_purchasable() && $product->is_in_stock() && $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
        esc_html( $product->add_to_cart_text() )
    ),$product );
 return $cart_url;
}

/**********************************/
//Shop Product Markup
/**********************************/
if ( ! function_exists( 'open_shop_product_meta_start' ) ){
  /**
   * Thumbnail wrap start.
   */
  function open_shop_product_meta_start(){
    echo '<div class="thunk-product-wrap"><div class="thunk-product">';
  }
}
if ( ! function_exists( 'open_shop_product_meta_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function open_shop_product_meta_end(){

    echo '</div></div>';
  }
}
/**********************************/
//Shop Product Image Markup
/**********************************/
if ( ! function_exists( 'open_shop_product_image_start' ) ){
  /**
   * Thumbnail wrap start.
   */
  function open_shop_product_image_start(){
    echo '<div class="thunk-product-image">';
  }
}
if ( ! function_exists( 'open_shop_product_image_end' ) ){

  /**
   * Thumbnail wrap start.
   */
    function open_shop_product_image_end(){
    do_action('quickview');
    echo '</div>';
  }
}

if ( ! function_exists( 'open_shop_product_content_start' ) ){
  /**
   * Thumbnail wrap start.
   */
  function open_shop_product_content_start(){
    echo '<div class="thunk-product-content">';
  }
}
if ( ! function_exists( 'open_shop_product_content_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function open_shop_product_content_end(){

    echo '</div>';
  }
}
 /**
   * Thunk-product-hover start.
   */
 if ( ! function_exists( 'open_shop_product_hover_start' ) ){
  function open_shop_product_hover_start(){

    echo'<div class="thunk-product-hover">';
    // do_action('open_shop_wishlist');
    // do_action('open_shop_compare');
      
  }
}
if ( ! function_exists( 'open_shop_product_hover_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function open_shop_product_hover_end(){
    
    echo '</div>';
  }
}

if ( ! function_exists( 'open_shop_shop_content_start' ) ){

  /**
   * Thumbnail wrap start.
   */
  function open_shop_shop_content_start(){
    
    $viewshow = get_theme_mod('open_shop_prd_view','grid-view');
    if($viewshow == 'grid-view'){
    echo '<div id="shop-product-wrap" class="thunk-grid-view">';
    }else{
    echo '<div id="shop-product-wrap" class="thunk-list-view">';
    }
  }
}

if ( ! function_exists( 'open_shop_shop_content_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function open_shop_shop_content_end(){
    
    echo '</div>';
  }
}
/**
* Shop customization.
*
* @return void
*/
add_action( 'woocommerce_before_shop_loop_item', 'open_shop_product_meta_start', 10);
add_action( 'woocommerce_after_shop_loop_item', 'open_shop_product_meta_end', 12 );
add_action( 'woocommerce_before_shop_loop_item_title', 'open_shop_product_content_start',20);
add_action( 'woocommerce_after_shop_loop_item_title', 'open_shop_product_content_end', 20 );
add_action( 'woocommerce_after_shop_loop_item_title', 'open_shop_product_hover_start',50);
add_action( 'woocommerce_after_shop_loop_item', 'open_shop_product_hover_end',20);

add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open',15);
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_product_link_close',1);
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price',0);
add_action( 'woocommerce_before_shop_loop_item_title', 'open_shop_product_image_start', 0);
add_action( 'woocommerce_before_shop_loop_item_title', 'open_shop_product_image_end',10 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_show_product_sale_flash',4);
add_action( 'woocommerce_after_shop_loop_item', 'open_shop_whish_list',11);
add_action( 'woocommerce_after_shop_loop_item', 'open_shop_add_to_compare_fltr',11);

add_action( 'woocommerce_before_shop_loop', 'open_shop_shop_content_start',1);
add_action( 'woocommerce_after_shop_loop', 'open_shop_shop_content_end',1);

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open');

/***************/
// single page
/***************/
if ( ! function_exists( 'open_shop_single_summary_start' ) ){

  /**
   * Thumbnail wrap start.
   */
  function open_shop_single_summary_start(){
    
    echo '<div class="thunk-single-product-summary-wrap">';
  }
}
if( ! function_exists( 'open_shop_single_summary_end' ) ){

  /**
   * Thumbnail wrap start.
   */
  function open_shop_single_summary_end(){
    
    echo '</div>';
  }
}
add_action( 'woocommerce_before_single_product_summary', 'open_shop_single_summary_start',0);
add_action( 'woocommerce_after_single_product_summary', 'open_shop_single_summary_end',0);


/****************/
// add to compare
/****************/
function open_shop_add_to_compare_fltr(){
  global $product;
      $product_id = $product->get_id();
        if( is_plugin_active('yith-woocommerce-compare/init.php') ){
          echo '<div class="thunk-compare"><span class="compare-list"><div class="woocommerce product compare-button"><a href="'.home_url().'?action=yith-woocompare-add-product&id='.$product_id.'" class="compare button" data-product_id="'.$product_id.'" rel="nofollow">Compare</a></div></span></div>';

           }
        }
/**********************/
/** wishlist **/
/**********************/
function open_shop_whish_list(){
       if( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ){
        echo '<div class="thunk-wishlist"><span class="thunk-wishlist-inner">'.do_shortcode('[yith_wcwl_add_to_wishlist icon="fa fa-heart" label="wishlist" already_in_wishslist_text="Already" browse_wishlist_text="Added"]' ).'</span></div>';
       }
 } 

function open_shop_whishlist_url(){
$wishlist_page_id =  get_option( 'yith_wcwl_wishlist_page_id' );
$wishlist_permalink = get_the_permalink( $wishlist_page_id );
return $wishlist_permalink ;
}
// shop open
/** My Account Menu **/
function open_shop_account(){
 if ( is_user_logged_in() ) {
  $return = '<a class="account" href="'.esc_url(get_permalink( get_option('woocommerce_myaccount_page_id') )).'"><i class="fa fa-user-o" aria-hidden="true"></i></a>';
  } 
 else {
  $return = '<span><a href="'.esc_url(get_permalink( get_option('woocommerce_myaccount_page_id') )).'"><i class="fa fa-lock" aria-hidden="true"></i></a></span>';
}
 echo $return;
}
 // Plus Minus Quantity Buttons @ WooCommerce Single Product Page
add_action( 'woocommerce_before_add_to_cart_quantity', 'open_shop_display_quantity_plus',10,2 );
function open_shop_display_quantity_plus(){
    echo '<div class="open-shop-quantity"><button type="button" class="plus" >+</button>';
}
add_action( 'woocommerce_after_add_to_cart_quantity', 'open_shop_display_quantity_minus',10,2 );
function open_shop_display_quantity_minus(){
    echo '<button type="button" class="minus" >-</button></div>';
}

//Woocommerce: How to remove page-title at the home/shop page but not category pages
add_filter( 'woocommerce_show_page_title', 'open_shop_not_a_shop_page' );
function open_shop_not_a_shop_page() {
    return boolval(!is_shop());
}
//************************************************************************************************//
// *****************************HOME PAGE WOO FUNCTION****************************************//
//************************************************************************************************//
//***********************/
// product category list
//************************/
function open_shop_product_list_categories( $args = '' ){
    $defaults = array(
        'child_of'            => 0,
        'current_category'    => 0,
        'depth'               => 0,
        'echo'                => 1,
        'exclude'             => '',
        'exclude_tree'        => '',
        'feed'                => '',
        'feed_image'          => '',
        'feed_type'           => '',
        'hide_empty'          => 1,
        'hide_title_if_empty' => false,
        'hierarchical'        => true,
        'order'               => 'ASC',
        'orderby'             => 'menu_order',
        'separator'           => '<br />',
        'show_count'          => 0,
        'show_option_all'     => '',
        'show_option_none'    => __( 'No categories','open-shop' ),
        'style'               => 'list',
        'taxonomy'            => 'product_cat',
        'title_li'            => '',
        'use_desc_for_title'  => 1,
    );
 
    $r = wp_parse_args( $args, $defaults );
 
    if ( ! isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
        $r['pad_counts'] = true;
    }
 
    // Descendants of exclusions should be excluded too.
    if ( true == $r['hierarchical'] ) {
        $exclude_tree = array();
 
        if ( $r['exclude_tree'] ) {
            $exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $r['exclude_tree'] ) );
        }
 
        if ( $r['exclude'] ) {
            $exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $r['exclude'] ) );
        }
 
        $r['exclude_tree'] = $exclude_tree;
        $r['exclude']      = '';
    }
 
    if ( ! isset( $r['class'] ) ) {
        $r['class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];
    }
 
    if ( ! taxonomy_exists( $r['taxonomy'] ) ) {
        return false;
    }
 
    $show_option_all  = $r['show_option_all'];
    $show_option_none = $r['show_option_none'];
 
    $categories = get_categories( $r );
 
    $output = '';
    if ( $r['title_li'] && 'list' == $r['style'] && ( ! empty( $categories ) || ! $r['hide_title_if_empty'] ) ) {
        $output = '<li class="' . esc_attr( $r['class'] ) . '">' . $r['title_li'] . '<ul>';
    }
    if ( empty( $categories ) ) {
        if ( ! empty( $show_option_none ) ) {
            if ( 'list' == $r['style'] ) {
                $output .= '<li class="cat-item-none">' . $show_option_none . '</li>';
            } else {
                $output .= $show_option_none;
            }
        }
    } else {
        if ( ! empty( $show_option_all ) ) {
 
            $posts_page = '';
 
            // For taxonomies that belong only to custom post types, point to a valid archive.
            $taxonomy_object = get_taxonomy( $r['taxonomy'] );
            if ( ! in_array( 'post', $taxonomy_object->object_type ) && ! in_array( 'page', $taxonomy_object->object_type ) ) {
                foreach ( $taxonomy_object->object_type as $object_type ) {
                    $_object_type = get_post_type_object( $object_type );
 
                    // Grab the first one.
                    if ( ! empty( $_object_type->has_archive ) ) {
                        $posts_page = get_post_type_archive_link( $object_type );
                        break;
                    }
                }
            }
 
            // Fallback for the 'All' link is the posts page.
            if ( ! $posts_page ) {
                if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) {
                    $posts_page = get_permalink( get_option( 'page_for_posts' ) );
                } else {
                    $posts_page = home_url( '/' );
                }
            }
 
            $posts_page = esc_url( $posts_page );
            if ( 'list' == $r['style'] ) {
                $output .= "<li class='cat-item-all'><a href='$posts_page'>$show_option_all</a></li>";
            } else {
                $output .= "<a href='$posts_page'>$show_option_all</a>";
            }
        }
 
        if ( empty( $r['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
            $current_term_object = get_queried_object();
            if ( $current_term_object && $r['taxonomy'] === $current_term_object->taxonomy ) {
                $r['current_category'] = get_queried_object_id();
            }
        }
 
        if ( $r['hierarchical'] ) {
            $depth = $r['depth'];
        } else {
            $depth = -1; // Flat.
        }
        $output .= walk_category_tree( $categories, $depth, $r );
    }
 
    if ( $r['title_li'] && 'list' == $r['style'] && ( ! empty( $categories ) || ! $r['hide_title_if_empty'] ) ) {
        $output .= '</ul></li>';
    }
 
    /**
     * Filters the HTML output of a taxonomy list.
     *
     * @since 2.1.0
     *
     * @param string $output HTML output.
     * @param array  $args   An array of taxonomy-listing arguments.
     */
    $html = apply_filters( 'open_shop_product_list_categories', $output, $args );
 
    if ( $r['echo'] ) {
        echo '<ul class="product-cat-list thunk-product-cat-list" data-menu-style="vertical">'.$html.'</ul>';
    } else {
        return $html;
    }
  }




/*****************************/
// Product show function
/****************************/
function  open_shop_widget_product_query($query){
$productType = $query['prd-orderby'];
$count = $query['count'];
$cat_slug = $query['cate'];
global $th_cat_slug;
$th_cat_slug = $cat_slug;
        $args = array(
            'hide_empty' => 1,
            'posts_per_page' => $count,        
            'post_type' => 'product',
            'orderby' => 'date',
            'order' => 'DESC',
        );
       if($productType == 'featured'){
       // featured product
            $args['meta_query'] =  array(
                'key'   => '_featured',
                'value' => 'yes'
            );
        } 
        elseif($productType == 'random'){
            //random product
          $args['orderby'] = 'rand';
        }
        elseif($productType == 'sale') {
          //sale product
        $args['meta_query']     = array(
        'relation' => 'OR',
        array( // Simple products type
            'key'           => '_sale_price',
            'value'         => 0,
            'compare'       => '>',
            'type'          => 'numeric'
        ),
        array( // Variable products type
            'key'           => '_min_variation_sale_price',
            'value'         => 0,
            'compare'       => '>',
            'type'          => 'numeric'
        )
    );
}
$args['meta_key'] = '_thumbnail_id';
if($cat_slug != '0'){
                //$args['product_cat'] = $cat_slug;
                $args['tax_query'] = array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => $cat_slug,
                            )
                         );
     }
$return = new WP_Query($args);
return $return;
}
/*****************************/
// Product show function
/****************************/
function open_shop_post_query($query){

       $args = array(
            'orderby' => $query['orderby'],
            'order' => 'DESC',
            'ignore_sticky_posts' => $query['sticky'],
            'post_type' => 'post',
            'posts_per_page' => $query['count'], 
            'cat' => $query['cate'],
            'meta_key'     => '_thumbnail_id',
           
        );

       if($query['thumbnail']){
          $args['meta_key'] = '_thumbnail_id';
       }

            $return = new WP_Query($args);

            return $return;
}
