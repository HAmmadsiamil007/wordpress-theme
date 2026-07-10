<?php
/**
 * Advanced Search Module — Singleton
 *
 * AJAX-powered search with type tabs, search history,
 * keyboard navigation, and Customizer controls.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Advanced_Search class.
 */
class Opulentia_Advanced_Search {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Returns the singleton instance.
     *
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor — registers hooks.
     */
    private function __construct() {
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ) );
        add_shortcode( 'op_search', array( $this, 'render_shortcode' ) );
        add_action( 'wp_ajax_op_advanced_search', array( $this, 'ajax_search' ) );
        add_action( 'wp_ajax_nopriv_op_advanced_search', array( $this, 'ajax_search' ) );
    }

    // -------------------------------------------------------------------------
    // Customizer Controls
    // -------------------------------------------------------------------------

    /**
     * Register customizer controls for the Advanced Search section.
     *
     * @param WP_Customize_Manager $wp_customize Customizer manager instance.
     */
    public function customize_register( $wp_customize ) {
        $wp_customize->add_section( 'Opulentia_advanced_search', array(
            'title'    => __( 'Advanced Search', 'opulentia' ),
            'panel'    => 'Opulentia_header_nav',
            'priority' => 55,
        ) );

        $wp_customize->add_setting( 'advanced_search_placeholder', array(
            'default'           => __( 'Search...', 'opulentia' ),
            'sanitize_callback' => 'sanitize_text_field',
            'type'              => 'theme_mod',
        ) );

        $wp_customize->add_control( 'advanced_search_placeholder', array(
            'label'    => __( 'Search Placeholder Text', 'opulentia' ),
            'section'  => 'Opulentia_advanced_search',
            'type'     => 'text',
            'priority' => 10,
        ) );

        $wp_customize->add_setting( 'advanced_search_max_results', array(
            'default'           => 5,
            'sanitize_callback' => 'absint',
            'type'              => 'theme_mod',
        ) );

        $wp_customize->add_control( 'advanced_search_max_results', array(
            'label'       => __( 'Max Results Per Type', 'opulentia' ),
            'section'     => 'Opulentia_advanced_search',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 1, 'max' => 20, 'step' => 1 ),
            'priority'    => 20,
        ) );

        $wp_customize->add_setting( 'advanced_search_products_tab', array(
            'default'           => true,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'type'              => 'theme_mod',
        ) );

        $wp_customize->add_control( 'advanced_search_products_tab', array(
            'label'    => __( 'Show Products Tab (WooCommerce)', 'opulentia' ),
            'section'  => 'Opulentia_advanced_search',
            'type'     => 'checkbox',
            'priority' => 30,
        ) );

        $wp_customize->add_setting( 'advanced_search_history', array(
            'default'           => true,
            'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            'type'              => 'theme_mod',
        ) );

        $wp_customize->add_control( 'advanced_search_history', array(
            'label'    => __( 'Enable Search History', 'opulentia' ),
            'section'  => 'Opulentia_advanced_search',
            'type'     => 'checkbox',
            'priority' => 40,
        ) );
    }

    /**
     * Sanitize checkbox value.
     *
     * @param mixed $value Input value.
     * @return bool
     */
    public function sanitize_checkbox( $value ) {
        return (bool) $value;
    }

    // -------------------------------------------------------------------------
    // Inline CSS
    // -------------------------------------------------------------------------

    /**
     * Output inline CSS for the advanced search dropdown.
     */
    public function inline_css() {
        $css = '
.op-search-wrap {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
    font-family: var(--font-body, Inter, sans-serif);
}

.op-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.op-search-input {
    width: 100%;
    padding: 14px 20px;
    padding-right: 50px;
    background: var(--color-secondary-dark, #111);
    border: 1px solid var(--color-border, #333);
    border-radius: 4px;
    color: var(--color-text, #f5f5f5);
    font-size: 16px;
    font-family: inherit;
    outline: none;
    transition: border-color 0.25s ease;
    box-sizing: border-box;
}

.op-search-input:focus {
    border-color: var(--color-gold, #c9a96e);
}

.op-search-input::placeholder {
    color: var(--color-text-muted, #999);
}

.op-search-icon {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-muted, #999);
    pointer-events: none;
    width: 20px;
    height: 20px;
}

.op-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--color-primary-dark, #1a1a1a);
    border: 1px solid var(--color-border, #333);
    border-top: none;
    border-radius: 0 0 4px 4px;
    z-index: 10000;
    max-height: 480px;
    overflow-y: auto;
    display: none;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
}

.op-search-dropdown.active {
    display: block;
}

.op-search-tabs {
    display: flex;
    border-bottom: 1px solid var(--color-border, #333);
    padding: 0;
    margin: 0;
    list-style: none;
}

.op-search-tab {
    flex: 1;
    text-align: center;
    padding: 10px 8px;
    cursor: pointer;
    font-size: 13px;
    font-family: var(--font-body, Inter, sans-serif);
    color: var(--color-text-muted, #999);
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
    user-select: none;
}

.op-search-tab:hover {
    color: var(--color-text, #f5f5f5);
}

.op-search-tab.active {
    color: var(--color-gold, #c9a96e);
    border-bottom-color: var(--color-gold, #c9a96e);
}

.op-search-results {
    padding: 0;
    margin: 0;
    list-style: none;
}

.op-search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid var(--color-border, #333);
    transition: background 0.15s ease;
}

.op-search-result-item:last-child {
    border-bottom: none;
}

.op-search-result-item:hover,
.op-search-result-item.highlighted {
    background: var(--color-secondary-dark, #111);
}

.op-search-result-item.highlighted {
    background: rgba(201, 169, 110, 0.08);
}

.op-search-thumb {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    border-radius: 3px;
    object-fit: cover;
    background: var(--color-border, #333);
}

.op-search-thumb-placeholder {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    border-radius: 3px;
    background: var(--color-border, #333);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-text-muted, #999);
    font-size: 18px;
}

.op-search-result-info {
    flex: 1;
    min-width: 0;
}

.op-search-result-title {
    font-size: 14px;
    font-weight: 500;
    color: var(--color-text, #f5f5f5);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0 0 4px;
    font-family: var(--font-body, Inter, sans-serif);
}

.op-search-result-excerpt {
    font-size: 12px;
    color: var(--color-text-muted, #999);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
}

.op-search-result-type {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gold, #c9a96e);
    flex-shrink: 0;
}

.op-search-no-results {
    padding: 32px 16px;
    text-align: center;
    color: var(--color-text-muted, #999);
    font-size: 14px;
}

.op-search-history {
    padding: 12px 16px;
    border-bottom: 1px solid var(--color-border, #333);
}

.op-search-history-title {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-text-muted, #999);
    margin: 0 0 8px;
    font-family: var(--font-body, Inter, sans-serif);
}

.op-search-history-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.op-search-history-tag {
    font-size: 12px;
    padding: 4px 10px;
    background: var(--color-secondary-dark, #111);
    border: 1px solid var(--color-border, #333);
    border-radius: 3px;
    cursor: pointer;
    color: var(--color-text-muted, #999);
    transition: all 0.2s ease;
    font-family: var(--font-body, Inter, sans-serif);
}

.op-search-history-tag:hover {
    color: var(--color-gold, #c9a96e);
    border-color: var(--color-gold, #c9a96e);
}

.op-search-history-clear {
    font-size: 11px;
    color: var(--color-accent, #b8860b);
    cursor: pointer;
    float: right;
    text-decoration: none;
}

.op-search-history-clear:hover {
    color: var(--color-gold, #c9a96e);
}

.op-search-loader {
    display: flex;
    justify-content: center;
    padding: 24px 16px;
}

.op-search-loader::after {
    content: "";
    width: 24px;
    height: 24px;
    border: 2px solid var(--color-border, #333);
    border-top-color: var(--color-gold, #c9a96e);
    border-radius: 50%;
    animation: op-search-spin 0.6s linear infinite;
}

@keyframes op-search-spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 576px) {
    .op-search-wrap {
        max-width: 100%;
    }

    .op-search-dropdown {
        max-height: 70vh;
    }

    .op-search-tab {
        font-size: 12px;
        padding: 8px 4px;
    }

    .op-search-result-item {
        padding: 10px 12px;
    }
}
';
        wp_add_inline_style( 'opulentia-style', $css );
    }

    // -------------------------------------------------------------------------
    // Shortcode
    // -------------------------------------------------------------------------

    /**
     * Render the [op_search] shortcode.
     *
     * @param array  $atts Shortcode attributes.
     * @param string $content Shortcode content.
     * @return string
     */
    public function render_shortcode( $atts, $content = '' ) {
        $atts = shortcode_atts( array(
            'placeholder' => get_theme_mod( 'advanced_search_placeholder', __( 'Search...', 'opulentia' ) ),
            'post_types'  => 'post,page',
        ), $atts, 'op_search' );

        $placeholder = esc_attr( $atts['placeholder'] );
        $post_types  = array_map( 'trim', explode( ',', $atts['post_types'] ) );

        $show_products = get_theme_mod( 'advanced_search_products_tab', true );
        $show_history  = get_theme_mod( 'advanced_search_history', true );
        $max_results   = absint( get_theme_mod( 'advanced_search_max_results', 5 ) );

        $tabs = array(
            'all'  => __( 'All', 'opulentia' ),
            'post' => __( 'Posts', 'opulentia' ),
            'page' => __( 'Pages', 'opulentia' ),
        );

        if ( class_exists( 'WooCommerce' ) && $show_products && in_array( 'product', $post_types, true ) ) {
            $tabs['product'] = __( 'Products', 'opulentia' );
        }

        ob_start();
        ?>
        <div class="op-search-wrap" data-max-results="<?php echo esc_attr( $max_results ); ?>" data-show-history="<?php echo esc_attr( $show_history ? '1' : '0' ); ?>">
            <div class="op-search-input-wrap">
                <input
                    type="text"
                    class="op-search-input"
                    placeholder="<?php echo esc_attr( $placeholder ); ?>"
                    autocomplete="off"
                    aria-label="<?php esc_attr_e( 'Search', 'opulentia' ); ?>"
                />
                <svg class="op-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="op-search-dropdown">
                <ul class="op-search-tabs" role="tablist">
                    <?php foreach ( $tabs as $key => $label ) : ?>
                        <li
                            class="op-search-tab<?php echo 'all' === $key ? ' active' : ''; ?>"
                            data-type="<?php echo esc_attr( $key ); ?>"
                            role="tab"
                            tabindex="0"
                        ><?php echo esc_html( $label ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if ( $show_history ) : ?>
                    <div class="op-search-history" style="display:none;">
                        <p class="op-search-history-title">
                            <?php esc_html_e( 'Recent Searches', 'opulentia' ); ?>
                            <span class="op-search-history-clear"><?php esc_html_e( 'Clear', 'opulentia' ); ?></span>
                        </p>
                        <ul class="op-search-history-list"></ul>
                    </div>
                <?php endif; ?>
                <div class="op-search-results-wrap">
                    <ul class="op-search-results"></ul>
                    <div class="op-search-no-results" style="display:none;"><?php esc_html_e( 'No results found.', 'opulentia' ); ?></div>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        $this->enqueue_search_script( $post_types );

        return $html;
    }

    /**
     * Enqueue the advanced search JavaScript — inline in page.
     *
     * @param array $post_types Allowed post types.
     */
    private function enqueue_search_script( $post_types ) {
        wp_register_script(
            'opulentia-advanced-search',
            '',
            array(),
            Opulentia_VERSION,
            true
        );
        wp_enqueue_script( 'opulentia-advanced-search' );

        $max_results = absint( get_theme_mod( 'advanced_search_max_results', 5 ) );
        $show_history = (bool) get_theme_mod( 'advanced_search_history', true );

        $script = '
(function() {
    var opSearch = {
        ajaxUrl: ' . wp_json_encode( admin_url( 'admin-ajax.php' ) ) . ',
        nonce: ' . wp_json_encode( wp_create_nonce( 'op_advanced_search_nonce' ) ) . ',
        postTypes: ' . wp_json_encode( $post_types ) . ',
        maxResults: ' . absint( $max_results ) . ',
        showHistory: ' . ( $show_history ? 'true' : 'false' ) . ',
        timer: null,
        currentIndex: -1,
        currentType: "all",

        init: function() {
            var wrap = document.querySelector(".op-search-wrap");
            if ( ! wrap ) return;
            this.wrap = wrap;
            this.input = wrap.querySelector(".op-search-input");
            this.dropdown = wrap.querySelector(".op-search-dropdown");
            this.resultsList = wrap.querySelector(".op-search-results");
            this.noResults = wrap.querySelector(".op-search-no-results");
            this.tabs = wrap.querySelectorAll(".op-search-tab");
            this.historyWrap = wrap.querySelector(".op-search-history");

            this.bindEvents();
            if ( this.showHistory ) {
                this.renderHistory();
            }
        },

        bindEvents: function() {
            var self = this;

            this.input.addEventListener("input", function() {
                clearTimeout(self.timer);
                var term = self.input.value.trim();
                if ( term.length < 2 ) {
                    self.closeDropdown();
                    return;
                }
                self.timer = setTimeout(function() {
                    self.search(term);
                }, 300);
            });

            this.input.addEventListener("keydown", function(e) {
                if ( e.key === "ArrowDown" || e.key === "ArrowUp" ) {
                    e.preventDefault();
                    self.navigate(e.key);
                }
                if ( e.key === "Enter" ) {
                    e.preventDefault();
                    self.selectCurrent();
                }
                if ( e.key === "Escape" ) {
                    self.closeDropdown();
                    self.input.blur();
                }
            });

            this.tabs.forEach(function(tab) {
                tab.addEventListener("click", function() {
                    self.switchTab(this);
                    var term = self.input.value.trim();
                    if ( term.length >= 2 ) {
                        self.search(term);
                    }
                });
                tab.addEventListener("keydown", function(e) {
                    if ( e.key === "Enter" || e.key === " " ) {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            document.addEventListener("click", function(e) {
                if ( ! self.wrap.contains(e.target) ) {
                    self.closeDropdown();
                }
            });

            var clearBtn = this.wrap.querySelector(".op-search-history-clear");
            if ( clearBtn ) {
                clearBtn.addEventListener("click", function() {
                    self.clearHistory();
                });
            }

            var historyList = this.wrap.querySelector(".op-search-history-list");
            if ( historyList ) {
                historyList.addEventListener("click", function(e) {
                    var tag = e.target.closest(".op-search-history-tag");
                    if ( tag ) {
                        self.input.value = tag.textContent;
                        self.search(tag.textContent);
                    }
                });
            }
        },

        search: function(term) {
            var self = this;
            this.showLoader();
            this.currentIndex = -1;

            var formData = new FormData();
            formData.append("action", "op_advanced_search");
            formData.append("nonce", this.nonce);
            formData.append("search", term);
            formData.append("type", this.currentType);

            fetch(this.ajaxUrl, {
                method: "POST",
                body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(resp) {
                self.hideLoader();
                if ( resp.success ) {
                    self.renderResults(resp.data.results);
                    self.saveHistory(term);
                } else {
                    self.renderResults([]);
                }
            })
            .catch(function() {
                self.hideLoader();
                self.renderResults([]);
            });
        },

        renderResults: function(results) {
            this.noResults.style.display = "none";
            this.resultsList.innerHTML = "";
            this.currentIndex = -1;

            if ( ! results || results.length === 0 ) {
                this.noResults.style.display = "block";
                this.dropdown.classList.add("active");
                return;
            }

            var self = this;
            results.forEach(function(item) {
                var li = document.createElement("li");
                li.className = "op-search-result-item";
                li.dataset.url = item.permalink;

                var thumbHtml = item.thumbnail
                    ? "<img class=\"op-search-thumb\" src=\"" + self.escapeAttr(item.thumbnail) + "\" alt=\"\" loading=\"lazy\" />"
                    : "<span class=\"op-search-thumb-placeholder\">&#x1f4c4;</span>";

                var excerptHtml = item.excerpt
                    ? "<p class=\"op-search-result-excerpt\">" + self.escapeHtml(item.excerpt) + "</p>"
                    : "";

                li.innerHTML = thumbHtml + "<div class=\"op-search-result-info\">" +
                    "<p class=\"op-search-result-title\">" + self.escapeHtml(item.title) + "</p>" +
                    excerptHtml +
                    "</div>" +
                    "<span class=\"op-search-result-type\">" + self.escapeHtml(item.type_label) + "</span>";

                li.addEventListener("click", function() {
                    if ( item.permalink ) {
                        window.location.href = item.permalink;
                    }
                });

                self.resultsList.appendChild(li);
            });

            this.dropdown.classList.add("active");
        },

        switchTab: function(tab) {
            this.tabs.forEach(function(t) { t.classList.remove("active"); });
            tab.classList.add("active");
            this.currentType = tab.dataset.type;
        },

        navigate: function(key) {
            var items = this.resultsList.querySelectorAll(".op-search-result-item");
            if ( items.length === 0 ) return;

            if ( key === "ArrowDown" ) {
                this.currentIndex = Math.min(this.currentIndex + 1, items.length - 1);
            } else {
                this.currentIndex = Math.max(this.currentIndex - 1, -1);
            }

            items.forEach(function(item, i) {
                item.classList.toggle("highlighted", i === this.currentIndex);
            }, this);

            if ( this.currentIndex >= 0 && items[this.currentIndex] ) {
                items[this.currentIndex].scrollIntoView({ block: "nearest" });
            }
        },

        selectCurrent: function() {
            var items = this.resultsList.querySelectorAll(".op-search-result-item");
            if ( this.currentIndex >= 0 && items[this.currentIndex] ) {
                var url = items[this.currentIndex].dataset.url;
                if ( url ) {
                    window.location.href = url;
                }
            }
        },

        showLoader: function() {
            this.resultsList.innerHTML = "<div class=\"op-search-loader\"></div>";
            this.noResults.style.display = "none";
            this.dropdown.classList.add("active");
        },

        hideLoader: function() {
            var loader = this.resultsList.querySelector(".op-search-loader");
            if ( loader ) loader.remove();
        },

        closeDropdown: function() {
            this.dropdown.classList.remove("active");
        },

        saveHistory: function(term) {
            if ( ! this.showHistory ) return;
            try {
                var history = JSON.parse(localStorage.getItem("opSearchHistory") || "[]");
                history = history.filter(function(h) { return h !== term; });
                history.unshift(term);
                if ( history.length > 5 ) {
                    history = history.slice(0, 5);
                }
                localStorage.setItem("opSearchHistory", JSON.stringify(history));
                this.renderHistory();
            } catch(e) {}
        },

        renderHistory: function() {
            try {
                var history = JSON.parse(localStorage.getItem("opSearchHistory") || "[]");
                var list = this.wrap.querySelector(".op-search-history-list");
                if ( ! list ) return;

                if ( history.length === 0 ) {
                    this.historyWrap.style.display = "none";
                    return;
                }

                this.historyWrap.style.display = "block";
                list.innerHTML = "";
                history.forEach(function(term) {
                    var li = document.createElement("li");
                    li.className = "op-search-history-tag";
                    li.textContent = term;
                    list.appendChild(li);
                });
            } catch(e) {}
        },

        clearHistory: function() {
            try {
                localStorage.removeItem("opSearchHistory");
                this.renderHistory();
            } catch(e) {}
        },

        escapeHtml: function(str) {
            var div = document.createElement("div");
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        },

        escapeAttr: function(str) {
            return String(str).replace(/&/g, "&amp;").replace(/"/g, "&quot;");
        }
    };

    if ( document.readyState === "loading" ) {
        document.addEventListener("DOMContentLoaded", function() { opSearch.init(); });
    } else {
        opSearch.init();
    }
})();';

        wp_add_inline_script( 'opulentia-advanced-search', $script );
    }

    // -------------------------------------------------------------------------
    // AJAX Handler
    // -------------------------------------------------------------------------

    /**
     * Handle AJAX search request.
     */
    public function ajax_search() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'op_advanced_search_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'opulentia' ) ) );
        }

        $search_term = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
        $type        = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'all';
        $max_results = absint( get_theme_mod( 'advanced_search_max_results', 5 ) );

        if ( empty( $search_term ) || mb_strlen( $search_term ) < 2 ) {
            wp_send_json_error( array( 'message' => __( 'Search term too short.', 'opulentia' ) ) );
        }

        $post_types = array( 'post', 'page' );

        if ( class_exists( 'WooCommerce' ) && get_theme_mod( 'advanced_search_products_tab', true ) ) {
            $post_types[] = 'product';
        }

        if ( 'all' !== $type && in_array( $type, $post_types, true ) ) {
            $query_post_types = array( $type );
        } elseif ( 'all' === $type ) {
            $query_post_types = $post_types;
        } else {
            $query_post_types = $post_types;
        }

        $query = new WP_Query( array(
            's'              => $search_term,
            'post_type'      => $query_post_types,
            'posts_per_page' => $max_results,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ) );

        $results = array();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $thumbnail = '';
                if ( has_post_thumbnail() ) {
                    $thumb_id  = get_post_thumbnail_id();
                    $thumb_src = wp_get_attachment_image_src( $thumb_id, array( 44, 44 ) );
                    if ( $thumb_src ) {
                        $thumbnail = $thumb_src[0];
                    }
                }

                $excerpt = get_the_excerpt();
                if ( ! empty( $excerpt ) ) {
                    $excerpt = wp_trim_words( $excerpt, 15, '' );
                }

                $results[] = array(
                    'id'        => get_the_ID(),
                    'title'     => get_the_title(),
                    'excerpt'   => $excerpt,
                    'permalink' => get_permalink(),
                    'thumbnail' => $thumbnail,
                    'type'      => get_post_type(),
                    'type_label' => $this->get_post_type_label( get_post_type() ),
                );
            }
        }

        wp_reset_postdata();

        wp_send_json_success( array(
            'results' => $results,
            'count'   => count( $results ),
        ) );
    }

    /**
     * Get human-readable post type label.
     *
     * @param string $post_type Post type slug.
     * @return string
     */
    private function get_post_type_label( $post_type ) {
        $labels = array(
            'post'    => __( 'Post', 'opulentia' ),
            'page'    => __( 'Page', 'opulentia' ),
            'product' => __( 'Product', 'opulentia' ),
        );

        return isset( $labels[ $post_type ] ) ? $labels[ $post_type ] : $post_type;
    }
}