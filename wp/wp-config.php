<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'babystyle');

/** MySQL database username */
define('DB_USER', 'babystyle');

/** MySQL database password */
define('DB_PASSWORD', 'jHjGVupWz6rf93mT');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'jcLnn<_#D6dqoNOdZ`,=Q;o6m*IhP>0fs!{G)ghK?I+)yG{d.azdi&tjg,Rv0I4a');
define('SECURE_AUTH_KEY',  'aXS0RK3-DSna[RQAgF40^7aQ+DERyVYb~*|rVi9aUdApe9~U=zX/o+g5m^-OKv9u');
define('LOGGED_IN_KEY',    'qT<@:wqXb!Jrod:/]ZG/%NWp`qqg:TPfFW/=.]?5z?7dN5G4/S?~QpPqC90AiDhh');
define('NONCE_KEY',        ',;K@|X4xa 6&kLX>hR1`:Na7Tl]b ~BKi~o@Ju@dz8}OW|m,pzm8tbKwX{1lLF0H');
define('AUTH_SALT',        'z9lIwW JU&,4<h%dbE3Xs!,3?K A1Jr_uB5ip?p,*fAMVEfD&[}N>SxYuMh@g]0[');
define('SECURE_AUTH_SALT', '^jI!Q3.CW e`|{ WSknwdW}EC8`#F~rR<;-eui]$eS]eSe[htAQF*sW?GnlQWyR4');
define('LOGGED_IN_SALT',   'u6ti%5|=S/v)>#?$@2u]g>Ov(yGO% HEA!u~rI,M.Wd^#00{9ca&mW4Ku3b=PIB ');
define('NONCE_SALT',       '9Wcm4IDdy+oIa|nQpmhLFz.tux ,u@`Oqc0aes@/9DD<z1KOwDV(`}L]9b<0m!7Z');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
