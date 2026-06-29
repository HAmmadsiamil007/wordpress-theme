<?php
/**
 * SoleOrigine — WooCommerce Product Seeder
 *
 * Drop this file into your WordPress root (next to wp-config.php).
 * Visit it ONCE in your browser: https://your-site.com/seed-products.php
 * After products are created, DELETE this file immediately for security.
 *
 * @package SoleOrigine
 */

// Load WordPress
if ( file_exists( dirname( __FILE__ ) . '/wp-load.php' ) ) {
    require_once dirname( __FILE__ ) . '/wp-load.php';
} else {
    echo '<h1>Error: wp-load.php not found. Place this file in your WordPress root.</h1>';
    exit;
}

// Check WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
    echo '<h1>Error: WooCommerce is not active. Please activate WooCommerce first.</h1>';
    exit;
}

// Check user is logged in as admin
if ( ! current_user_can( 'manage_options' ) ) {
    echo '<h1>Error: You must be logged in as an administrator.</h1>';
    exit;
}

// Prevent re-running
if ( get_option( 'soleorigine_products_seeded' ) ) {
    echo '<h1>Products already seeded! Delete this file.</h1>';
    echo '<p>If you want to re-seed, delete the <code>soleorigine_products_seeded</code> option from wp_options and re-upload this file.</p>';
    exit;
}

echo '<h1>SoleOrigine — Creating Products...</h1>';
echo '<style>body{font-family:sans-serif;padding:40px;background:#1a1a1a;color:#f5f5f5;} h1{color:#c9a96e;} .ok{color:#4caf50;} .err{color:#f44336;} pre{background:#111;padding:15px;border-radius:4px;overflow-x:auto;}</pre></style>';

// ──────────────────────────────────────────────
// Categories
// ──────────────────────────────────────────────
$categories = array(
    'Oxfords'    => 'Classic formal lace-up shoes with closed lacing.',
    'Derbies'    => 'Versatile open-lacing shoes for formal and smart-casual wear.',
    'Loafers'    => 'Slip-on shoes embodying effortless Italian elegance.',
    'Monk Straps' => 'Buckle-strap shoes blending tradition with modern style.',
    'Boots'      => 'Premium leather boots for every occasion.',
);

$cat_ids = array();
foreach ( $categories as $name => $desc ) {
    $term = term_exists( $name, 'product_cat' );
    if ( ! $term ) {
        $term = wp_insert_term( $name, 'product_cat', array( 'description' => $desc ) );
    }
    $cat_ids[ $name ] = is_wp_error( $term ) ? 0 : (int) $term['term_id'];
    echo '<p class="ok">✓ Category: ' . esc_html( $name ) . '</p>';
}

// ──────────────────────────────────────────────
// Products
// ──────────────────────────────────────────────
$products = array(

    // ── OXFORDS ──
    array(
        'name'        => 'Classic Oxford — Dark Brown',
        'slug'        => 'classic-oxford-dark-brown',
        'description' => '<p>The quintessential dress shoe. Our Classic Oxford features a closed lacing system, hand-stitched cap toe, and premium dark brown Italian calf leather. Built on a classic last for timeless silhouette.</p>
        <ul>
            <li><strong>Leather:</strong> Full-grain Italian calf, dark brown</li>
            <li><strong>Sole:</strong> Blake stitched leather sole</li>
            <li><strong>Lining:</strong> Natural calf leather</li>
            <li><strong>Care:</strong> Includes dust bags and care guide</li>
        </ul>',
        'short'       => 'Dark brown Italian calf leather with closed lacing and hand-stitched cap toe.',
        'price'       => '48500',
        'category'    => 'Oxfords',
        'image'       => 'https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=800&h=800&fit=crop',
        'gallery'     => array(
            'https://images.unsplash.com/photo-1611074022320-4bd919e44846?w=800&h=800&fit=crop',
        ),
    ),
    array(
        'name'        => 'Classic Oxford — Black',
        'slug'        => 'classic-oxford-black',
        'description' => '<p>The ultimate formal shoe in jet black. Hand-lasted from the finest black Italian calf leather with a sleek silhouette perfect for black-tie events and boardroom power.</p>
        <ul>
            <li><strong>Leather:</strong> Full-grain Italian calf, black</li>
            <li><strong>Sole:</strong> Blake stitched leather sole</li>
            <li><strong>Lining:</strong> Natural calf leather</li>
        </ul>',
        'short'       => 'Jet black Italian calf leather Oxford for the most formal occasions.',
        'price'       => '51000',
        'category'    => 'Oxfords',
        'image'       => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),

    // ── DERBIES ──
    array(
        'name'        => 'Cap Toe Derby — Dark Brown',
        'slug'        => 'cap-toe-derby-dark-brown',
        'description' => '<p>A versatile open-lacing shoe that bridges formal and smart-casual. The cap toe Derby features hand-burnished dark brown leather with meticulous brogue detailing along the cap.</p>
        <ul>
            <li><strong>Leather:</strong> Hand-burnished Italian calf, dark brown</li>
            <li><strong>Sole:</strong> Blake stitched leather sole with rubber heel</li>
            <li><strong>Lining:</strong> Natural calf leather</li>
        </ul>',
        'short'       => 'Hand-burnished dark brown leather with cap toe and open lacing.',
        'price'       => '46500',
        'category'    => 'Derbies',
        'image'       => 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),
    array(
        'name'        => 'Plain Toe Derby — Tan',
        'slug'        => 'plain-toe-derby-tan',
        'description' => '<p>Clean lines meet rich tan Italian leather in this plain toe Derby. Perfect for pairing with chinos or tailored trousers for a relaxed yet refined look.</p>
        <ul>
            <li><strong>Leather:</strong> Vegetable-tanned Italian calf, tan</li>
            <li><strong>Sole:</strong> Blake stitched leather sole</li>
            <li><strong>Lining:</strong> Natural calf leather</li>
        </ul>',
        'short'       => 'Vegetable-tanned tan Italian leather plain toe Derby.',
        'price'       => '44500',
        'category'    => 'Derbies',
        'image'       => 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),

    // ── LOAFERS ──
    array(
        'name'        => 'Penny Loafer — Tan Brown',
        'slug'        => 'penny-loafer-tan-brown',
        'description' => '<p>The iconic penny loafer reimagined in rich tan brown Italian leather. A slip-on masterpiece that exudes confidence, whether at a garden party or a weekend brunch.</p>
        <ul>
            <li><strong>Leather:</strong> Italian calf, tan brown</li>
            <li><strong>Sole:</strong> Blake stitched leather sole</li>
            <li><strong>Lining:</strong> Natural calf leather</li>
        </ul>',
        'short'       => 'Rich tan brown Italian leather penny loafer for effortless style.',
        'price'       => '43500',
        'category'    => 'Loafers',
        'image'       => 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),
    array(
        'name'        => 'Horsebit Loafer — Dark Brown',
        'slug'        => 'horsebit-loafer-dark-brown',
        'description' => '<p>Italian elegance personified. Our Horsebit Loafer features a polished gold-tone horsebit buckle on dark brown burnished leather. Hand-lasted for a sleek, comfortable fit.</p>
        <ul>
            <li><strong>Leather:</strong> Burnished Italian calf, dark brown</li>
            <li><strong>Hardware:</strong> Gold-tone horsebit buckle</li>
            <li><strong>Sole:</strong> Blake stitched leather sole</li>
        </ul>',
        'short'       => 'Dark brown burnished leather with gold-tone horsebit detail.',
        'price'       => '47000',
        'category'    => 'Loafers',
        'image'       => 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),

    // ── MONK STRAPS ──
    array(
        'name'        => 'Single Monk Strap — Dark Brown',
        'slug'        => 'single-monk-dark-brown',
        'description' => '<p>The single monk strap is the modern gentleman\'s alternative to lace-ups. Features a polished brass buckle on hand-burnished dark brown Italian calf leather.</p>
        <ul>
            <li><strong>Leather:</strong> Hand-burnished Italian calf, dark brown</li>
            <li><strong>Hardware:</strong> Polished brass single buckle</li>
            <li><strong>Sole:</strong> Blake stitched leather sole</li>
        </ul>',
        'short'       => 'Hand-burnished dark brown leather with polished brass buckle.',
        'price'       => '47500',
        'category'    => 'Monk Straps',
        'image'       => 'https://images.unsplash.com/photo-1611074022320-4bd919e44846?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),
    array(
        'name'        => 'Double Monk Strap — Burgundy',
        'slug'        => 'double-monk-burgundy',
        'description' => '<p>Make a statement with the double monk strap in rich burgundy Italian leather. Two polished buckles create a bold yet refined silhouette for the style-conscious gentleman.</p>
        <ul>
            <li><strong>Leather:</strong> Italian calf, burgundy</li>
            <li><strong>Hardware:</strong> Polished silver double buckles</li>
            <li><strong>Sole:</strong> Blake stitched leather sole</li>
        </ul>',
        'short'       => 'Rich burgundy Italian leather with polished silver double buckles.',
        'price'       => '52000',
        'category'    => 'Monk Straps',
        'image'       => 'https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),

    // ── BOOTS ──
    array(
        'name'        => 'Chelsea Boot — Black',
        'slug'        => 'chelsea-boot-black',
        'description' => '<p>Sleek and versatile, our Chelsea Boot in jet black Italian leather features elastic side panels and a pull tab for easy on/off. A must-have for any wardrobe.</p>
        <ul>
            <li><strong>Leather:</strong> Italian calf, black</li>
            <li><strong>Sole:</strong> Blake stitched leather sole with rubber heel</li>
            <li><strong>Feature:</strong> Elastic side panels, pull tab</li>
        </ul>',
        'short'       => 'Jet black Italian leather Chelsea boot with elastic side panels.',
        'price'       => '55000',
        'category'    => 'Boots',
        'image'       => 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),
    array(
        'name'        => 'Chelsea Boot — Dark Brown',
        'slug'        => 'chelsea-boot-dark-brown',
        'description' => '<p>The Chelsea Boot in rich dark brown Italian leather. Hand-burnished finish adds depth and character. Perfect with denim, chinos, or tailored trousers.</p>
        <ul>
            <li><strong>Leather:</strong> Hand-burnished Italian calf, dark brown</li>
            <li><strong>Sole:</strong> Blake stitched leather sole with rubber heel</li>
            <li><strong>Feature:</strong> Elastic side panels, pull tab</li>
        </ul>',
        'short'       => 'Hand-burnished dark brown Italian leather Chelsea boot.',
        'price'       => '55000',
        'category'    => 'Boots',
        'image'       => 'https://images.unsplash.com/photo-1611074022320-4bd919e44846?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),
    array(
        'name'        => 'Chukka Boot — Suede Tan',
        'slug'        => 'chukka-boot-suede-tan',
        'description' => '<p>Casual sophistication in premium suede. Our Chukka Boot features soft Italian suede in warm tan with a crepe sole for all-day comfort and effortless style.</p>
        <ul>
            <li><strong>Leather:</strong> Italian suede, tan</li>
            <li><strong>Sole:</strong> Crepe rubber sole</li>
            <li><strong>Lining:</strong> Natural calf leather</li>
        </ul>',
        'short'       => 'Premium Italian suede Chukka boot in warm tan with crepe sole.',
        'price'       => '48000',
        'category'    => 'Boots',
        'image'       => 'https://images.unsplash.com/photo-1473188588951-666fce8e7c68?w=800&h=800&fit=crop',
        'gallery'     => array(),
    ),
);

$count = 0;

foreach ( $products as $p ) {

    // Skip if product already exists
    $existing = get_page_by_path( $p['slug'], OBJECT, 'product' );
    if ( $existing ) {
        echo '<p>⏭ Already exists: ' . esc_html( $p['name'] ) . '</p>';
        continue;
    }

    $product = new WC_Product_Simple();
    $product->set_name( $p['name'] );
    $product->set_slug( $p['slug'] );
    $product->set_description( $p['description'] );
    $product->set_short_description( $p['short'] );
    $product->set_regular_price( $p['price'] );
    $product->set_status( 'publish' );
    $product->set_catalog_visibility( 'visible' );
    $product->set_manage_stock( false );
    $product->set_sold_individually( false );
    $product->set_weight( '' );
    $product->set_length( '' );
    $product->set_width( '' );
    $product->set_height( '' );

    // Category
    if ( ! empty( $cat_ids[ $p['category'] ] ) ) {
        $product->set_category_ids( array( $cat_ids[ $p['category'] ] ) );
    }

    $product->save();

    // Main image (set via WordPress media)
    $img_id = media_sideload_image( $p['image'], $product->get_id(), $p['name'], 'id' );
    if ( ! is_wp_error( $img_id ) ) {
        set_post_thumbnail( $product->get_id(), $img_id );
    }

    // Gallery images
    if ( ! empty( $p['gallery'] ) ) {
        $gallery_ids = array();
        foreach ( $p['gallery'] as $g_url ) {
            $g_id = media_sideload_image( $g_url, $product->get_id(), $p['name'] . ' gallery', 'id' );
            if ( ! is_wp_error( $g_id ) ) {
                $gallery_ids[] = $g_id;
            }
        }
        if ( ! empty( $gallery_ids ) ) {
            $product->set_gallery_image_ids( $gallery_ids );
            $product->save();
        }
    }

    $count++;
    echo '<p class="ok">✓ Created: ' . esc_html( $p['name'] ) . ' — PKR ' . esc_html( number_format( (int) $p['price'] ) ) . '</p>';
}

// Mark as seeded
update_option( 'soleorigine_products_seeded', true );

echo '<hr>';
echo '<h2 class="ok">Done! Created ' . esc_html( $count ) . ' products across ' . esc_html( count( $categories ) ) . ' categories.</h2>';
echo '<p><strong>Delete this file now!</strong> Visit <a href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '" style="color:#c9a96e;">WooCommerce → Products</a> to see your products.</p>';
echo '<p><a href="' . esc_url( home_url() ) . '" style="color:#c9a96e;">View Frontend →</a></p>';
