<?php
class WORD_BALLOON_ASSETS {

    private static $balloons = array();
    private static $icon_type = false;
    private static $effects = array();
    private static $filters = array();
    private static $in_views = array();
    private static $quote_effects = array();
    private static $detected = false;

    
    public static function detect_from_posts( $posts ) {

        if ( empty( $posts ) ) {
            return $posts;
        }

        foreach ( $posts as $post ) {
            if ( isset( $post->post_content ) ) {
                self::detect_from_content( $post->post_content );
            }
        }

        return $posts;
    }

    
    public static function detect_from_content( $content ) {

        if ( self::$detected || ! is_string( $content ) ) {
            return;
        }

        if ( has_shortcode( $content, 'word_balloon' ) ) {

            preg_match_all(
                '/\[word_balloon\s+([^\]]+)\]/',
                $content,
                $matches
            );

            if ( ! empty( $matches[1] ) ) {
                foreach ( $matches[1] as $attr_string ) {
                    $atts = shortcode_parse_atts( $attr_string );
                    if ( isset( $atts['balloon'] ) ) {
                        self::$balloons[ basename( (string) $atts['balloon'] ) ] = true;
                    }
                    if ( isset( $atts['icon_type'] ) ) {
                       self::$icon_type = true;
                   }
                   if ( isset( $atts['balloon_effect'] ) ) {
                    self::$effects[ basename( (string) $atts['balloon_effect'] ) ] = true;
                }
                if ( isset( $atts['icon_effect'] ) ) {
                    self::$effects[ basename( (string) $atts['icon_effect'] ) ] = true;
                }
                if ( isset( $atts['avatar_effect'] ) ) {
                    self::$effects[ basename( (string) $atts['avatar_effect'] ) ] = true;
                }
                if ( isset( $atts['balloon_filter'] ) ) {
                    self::$filters[ basename( (string) $atts['balloon_filter'] ) ] = true;
                }
                if ( isset( $atts['icon_filter'] ) ) {
                    self::$filters[ basename( (string) $atts['icon_filter'] ) ] = true;
                }
                if ( isset( $atts['avatar_filter'] ) ) {
                    self::$filters[ basename( (string) $atts['avatar_filter'] ) ] = true;
                }
                if ( isset( $atts['balloon_in_view'] ) ) {
                    self::$in_views[ basename( (string) $atts['balloon_in_view'] ) ] = true;
                }
                if ( isset( $atts['icon_in_view'] ) ) {
                    self::$in_views[ basename( (string) $atts['icon_in_view'] ) ] = true;
                }
                if ( isset( $atts['avatar_in_view'] ) ) {
                    self::$in_views[ basename( (string) $atts['avatar_in_view'] ) ] = true;
                }
                if ( isset( $atts['quote_effect'] ) && $atts['quote_effect'] !== '' ) {
                    self::$quote_effects[ basename( (string) $atts['quote_effect'] ) ] = true;
                }
            }
        }

        self::$detected = true;
    }
}


public static function content_filter( $content ) {

    self::detect_from_content( $content );
    return $content; 
}


public static function enqueue() {

    if ( empty( self::$balloons ) ) {
        return;
    }

    
    word_balloon_user_styles();

    
    foreach ( self::$balloons as $balloon => $true ) {
        wp_enqueue_style(
            'word_balloon_skin_' . $balloon,
            WORD_BALLOON_URI . 'css/skin/word_balloon_' . $balloon . '.min.css',
            array( 'word_balloon_user_style' ),
            WORD_BALLOON_VERSION
        );
    }


    
    if ( self::$icon_type ) {
        wp_enqueue_style('word_balloon_icon', WORD_BALLOON_URI . 'css/word_balloon_icon.min.css',array('word_balloon_user_style'),WORD_BALLOON_VERSION);
    }

    
    foreach ( self::$effects as $effect => $true ) {
        wp_enqueue_style('word_balloon_effect_'.$effect, WORD_BALLOON_URI . 'css/effect/word_balloon_'.$effect.'.min.css',array('word_balloon_user_style'),WORD_BALLOON_VERSION);
    }

    
    foreach ( self::$filters as $filter => $true ) {
        wp_enqueue_style('word_balloon_filter_'.$filter, WORD_BALLOON_URI . 'css/filter/word_balloon_'.$filter.'.min.css',array('word_balloon_user_style'),WORD_BALLOON_VERSION);
    }

    
    $settings = get_option( 'word_balloon_post_settings' );

    if ( is_array( $settings ) && isset( $settings['inview'] ) && $settings['inview'] === 'true' ) {

        wp_enqueue_style(
            'word_balloon_inview_style',
            WORD_BALLOON_URI . 'css/word_balloon_inview.min.css',
            array(),
            WORD_BALLOON_VERSION
        );

        wp_enqueue_script(
            'word_balloon_inview_script',
            WORD_BALLOON_URI . 'js/word_balloon_inview.min.js',
            array(),
            WORD_BALLOON_VERSION,
            true
        );

    }
    if(function_exists('word_balloon_pro_in_view_enqueue')){
        word_balloon_pro_in_view_enqueue(self::$in_views);
    }

    

    if ( function_exists( 'word_balloon_pro_quote_effect_enqueue' ) ){
        word_balloon_pro_quote_effect_enqueue(self::$quote_effects);
    }
}
}


add_filter( 'the_posts', array( 'WORD_BALLOON_ASSETS', 'detect_from_posts' ), 99 );

add_filter( 'the_content', array( 'WORD_BALLOON_ASSETS', 'content_filter' ), 99 );
add_action( 'wp_enqueue_scripts', array( 'WORD_BALLOON_ASSETS', 'enqueue' ), 10 );

