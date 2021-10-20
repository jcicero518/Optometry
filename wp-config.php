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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'wordpress');

/** MySQL hostname */
define('DB_HOST', 'database');

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
define('AUTH_KEY',         '*4bH7SE,|;4LJo*~4dJ,qoQpN<`|8Ty:T.,x7,Zp<aSk h;^-TW`_E4~)~EKuvSa');
define('SECURE_AUTH_KEY',  '0<zb<F=<|MHS m`j[D&oa.[9%S(u=M#wCH`y|#)nY.MYB7iiNXqI;^E+*rzBTms?');
define('LOGGED_IN_KEY',    'VI1|9J7uleyou_yI`5l1(o]DpiUxy=$?TEGZ~-~0[02cCs||Iq3;5vj:!F*~=JGu');
define('NONCE_KEY',        '~Czk|%]k=*y--mkmqI @=?~6<oU|H:zTFF6P2_O-=s^AgC]R@uG3P{>?_l.}|GX#');
define('AUTH_SALT',        ',6kO/VR;91NBIkh{^P15xK0%v(z!X+z0Qs.o+w@%63Z-2@J#jd[ex*Ke}x.R(`*-');
define('SECURE_AUTH_SALT', '=YbA<Fn0 Tt2+Vr3]Pm&2}9Tpz]LaTzyE0|U,=<8f=gm*DQt6xIcoqGQxg2CTN+<');
define('LOGGED_IN_SALT',   'iC2=Dp<Uo@z1}>ehy=p^~*i[M]ym?0TLAl$r5Xh2i4UCA8gW6C9 `v,:oA7P2<I!');
define('NONCE_SALT',       '7M|Z;-()_%e|>(L-UC`d=G/%:~[A6|.d$B^isX4$X}-B]1J3Ku&ZH8?0SYd/@tji');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'sopt_';

//define('ACF_EARLY_ACCESS', '5');

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
define('WP_DEBUG_LOG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
