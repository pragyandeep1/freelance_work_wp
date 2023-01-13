<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'freelance_portal' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'rjfom.|hOpg}J+gMl*B!s7E9gxED9citqS+e5eX,3_~l)% <e!oW}I(rs=gIS^tX' );
define( 'SECURE_AUTH_KEY',  '5*qy,rm=!}nS%esV$n%2yS`(;@!rr ;M*>o!|EkZ@}/t,JCU1,V1aIH#m}gpDV!l' );
define( 'LOGGED_IN_KEY',    '{@X;+vDbv9>w.-!O@i#*{]KSPEiN%7YR;46%j7nX)OHC?zO540;iP3mR6Q>es8~V' );
define( 'NONCE_KEY',        'O_3pU*@j?T8MM+@Ezfpl3U,d/|WizAN-=;f1;~A3=P^d)Pkqe#Y05pXp}:K!^j8d' );
define( 'AUTH_SALT',        'm];P{qeQO.iIXbIc3.SL6aw96UN~l%|qFO;3ys.V%~)`.n)rAmbz o/JDCV-m&V(' );
define( 'SECURE_AUTH_SALT', '#>xi1lE[IXgyNJjv 3V]bd!O5u M.d+~@=:|;|_jCYdGMP6r}Td:J1._1j=Xf|d~' );
define( 'LOGGED_IN_SALT',   'r[E#i:.u3)O!WSkNW8P+9qe!kh3apF+1t_&g4wPF;0gkFYdvD~0B0&`OiD<g~uvP' );
define( 'NONCE_SALT',       '9^)R+/01x4B I8NZZiPR);%LO)/?rnzcG0*]eKa-[FXn*^#&NK<2*V&3xCccnQ#p' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
