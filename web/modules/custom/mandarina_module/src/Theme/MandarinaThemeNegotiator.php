<?php

/**
 * @file
 * Contains \Drupal\mandarina_module\Theme\MandarinaThemeNegotiator.
 */

namespace Drupal\mandarina_module\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

class MandarinaThemeNegotiator implements ThemeNegotiatorInterface {
    
    public function applies(RouteMatchInterface $route_match) {
        $applies = FALSE;
        if( $route_match->getRouteName() == 'mandarina.login_wifi' ){
            $applies = TRUE;
        }
        return $applies;
    }
    
    /**
     * {@inheritdoc}
     */
    public function determineActiveTheme(RouteMatchInterface $route_match) {
        return 'mandarina';
    }
}

?>