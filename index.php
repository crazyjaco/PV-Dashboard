<?php
// $path = '../../';
/**
 * Create an array of the hosts from all of the VVV host files
 *
 * @author         Jeff Behnke <code@validwebs.com>
 * @copyright  (c) 2014 ValidWebs.com
 *
 * Created:    5/23/14, 12:57 PM
 *
 * @param $path
 *
 * @return array
 */
function get_hosts() {
	// Get Vhost conf files.
	$path         = '/etc/apache2/sites-available';
	$a_directory  = scandir( $path );
	$a_conf_files = array_diff( $a_directory, array( '..', '.' ) );
	$info = array();
	$x = 0;

	foreach ( $a_conf_files as $conf_file ) {
		error_log( 'FILE: ' . $conf_file );
		$Thisfile   = fopen( $path . '/' . $conf_file, 'r' ) or die( 'No open ups..' );
		while ( ! feof( $Thisfile ) ) {
			$line = fgets( $Thisfile );
			$line = trim( $line );

			// Break out useful information from conf file.
			$tokens = explode( ' ', $line );

			if ( ! empty( $tokens ) ) {
				if ( strtolower( $tokens[0] ) == 'servername' ) {
					$info[ $x ]['ServerName'] = $tokens[1];
				}
				if ( strtolower( $tokens[0] ) == 'documentroot' ) {
					$info[ $x ]['DocumentRoot'] = $tokens[1];
				}
				if ( strtolower( $tokens[0] ) == 'errorlog' ) {
					$info[ $x ]['ErrorLog'] = $tokens[1];
				}
				if ( strtolower( $tokens[0] ) == 'serveralias' ) {
					$info[ $x ]['ServerAlias'] = $tokens[1];
				}
			} else {
			    echo 'Puked...';
			}
		}

		fclose( $Thisfile );
		$x++;
	}
	return $info;
}

/**
 * Get site information based on DocumentRoot
 * @param  array $vhosts Array of site config data for all hosts.
 * @return array  Modified version of parameter array with attnl info.
 */
function get_host_info( $vhosts = array() ) {
	if ( is_array( $vhosts ) && ! empty( $vhosts ) ) {
		// Loop through each DocumentRoot and check for wp-config.php.
		foreach ( $vhosts as $key => $vhost ) {
			$path = $vhost['DocumentRoot'];
			$dir  = scandir( $path );
			$intersect = array_intersect( array( 'wp-config.php' ), $dir );
			if ( ! empty( $intersect ) ) {
				$config_lines = file( $path . '/wp-config.php', FILE_SKIP_EMPTY_LINES );
				foreach ( $config_lines as $num => $line ) {
					// Skip comment lines.
					if ( strstr( $line, "define('WP_DEBUG', true);" )
						|| strstr( $line, 'define("WP_DEBUG", true);' )
						|| strstr( $line, 'define( "WP_DEBUG", true );' )
						|| strstr( $line, "define( 'WP_DEBUG', true );" )
					) {
						$vhosts[ $key ]['debug'] = 'true';
					} else {
						$vhosts[ $key ]['debug'] = 'false';
					}
				}
				$vhosts[ $key ]['is_wp'] = 'true';
			} else {
				$vhosts[ $key ]['debug'] = '';
				$vhosts[ $key ]['is_wp'] = 'false';
			}
		}
	}
	//error_log( print_r( $vhosts, true ) );
	return $vhosts;
}

$hosts = get_hosts();
$hosts_info = get_host_info( $hosts );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Primary Vagrant Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="style.css?ver=5" />
	<script type="text/JavaScript" src="bower_components/jquery/dist/jquery.min.js"></script>

	<script type="text/javascript" src="src/js/scripts.js"></script>
</head>
<body>
<div style="display: none;"><?php print_r( $hosts_info ); ?></div>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="./">Dashboard</a>
		</div>

		<ul class="nav navbar-nav">
			<li><a href="/database-admin/" target="_blank">phpMyAdmin</a></li>
			<li><a href="/memcached-admin/" target="_blank">phpMemcachedAdmin</a></li>
			<li><a href="/opcache-status/opcache.php" target="_blank">Opcache Status</a></li>
			<li><a href="/webgrind/" target="_blank">Webgrind</a></li>
			<li><a href="/phpinfo/" target="_blank">PHP Info</a></li>
		</ul>
	</div>
</div>

<div class="container-fluid">
	<div class="col-sm-4 col-md-3 sidebar">

		<p class="sidebar-title">Useful Commands</p>
		<ul class="nav">
			<li><a href="https://github.com/varying-vagrant-vagrants/vvv/#now-what" target="_blank">Commands Link</a>
			</li>
			<li><code>vagrant up</code></li>
			<li><code>vagrant halt</code></li>
			<li><code>vagrant ssh</code></li>
			<li><code>vagrant suspend</code></li>
			<li><code>vagrant resume</code></li>
			<li><code>xdebug_on</code>
				<a href="https://github.com/Varying-Vagrant-Vagrants/VVV/wiki/Code-Debugging#turning-on-xdebug" target="_blank">xDebug Link</a>
			</li>
		</ul>


		<p class="sidebar-title">References &amp; Extras</p>
		<ul class="nav">
			<li><a target="_blank" href="https://github.com/bradp/vv">Variable VVV (newest)</a></li>
			<li><a target="_blank" href="https://github.com/aliso/vvv-site-wizard">VVV Site Wizard (old)</a></li>
			<li><a href="https://github.com/varying-vagrant-vagrants/vvv/" target="_blank">Varying Vagrant Vagrants</a>
			</li>
			<li><a href="https://github.com/topdown/VVV-Dashboard" target="_blank">VVV Dashboard Repo</a></li>
			<li><a href="https://github.com/topdown/VVV-Dashboard/issues" target="_blank">VVV Dashboard Issues</a></li>
			<li>
				<a href="https://github.com/aubreypwd/wordpress-themereview-vvv" target="_blank">VVV WordPress ThemeReview</a>
			</li>
		</ul>
	</div>
	<div class="col-sm-8 col-sm-offset-4 col-md-9 col-md-offset-3 main">
		<h1 class="page-header">VVV Dashboard</h1>

		<div class="row">
			<div class="col-sm-12 hosts">
				<p>
					<strong>Current Hosts = <?php echo isset( $hosts['site_count'] ) ? $hosts['site_count'] : ''; ?></strong>
				</p>
				<small>Note: To profile, <code>xdebug_on</code> must be set.</small>

				<p class="search-box">Live Search: <input type="text" id="text-search" />
					<!--<input id="search" type="button" value="Search" />
					<input id="back" type="button" value="Search Up" /> &nbsp;
					<small>Enter, Up and Down keys are bound.</small>-->
				</p>

				<table class="sites table table-responsive table-striped">
					<thead>
					<tr>
						<th>Debug Mode</th>
						<th>Sites</th>
						<th>Actions</th>
					</tr>
					</thead>
					<?php
					foreach ( $hosts_info as $key => $array ) {
						if ( 'site_count' != $key ) { ?>
							<tr>
								<?php if ( 'true' == $array['debug'] ) { ?>
									<td><span class="label label-success">Debug On</span></td>
								<?php } else { ?>
									<td><span class="label label-danger">Debug Off</span></td>
								<?php } ?>
								<td><?php echo $array['ServerName']; ?></td>

								<td>
									<a class="btn btn-primary btn-xs" href="http://<?php echo $array['ServerName']; ?>/" target="_blank">Visit Site</a>

									<?php if ( 'true' == $array['is_wp'] ) { ?>
										<a class="btn btn-warning btn-xs" href="http://<?php echo $array['ServerName']; ?>/wp-admin" target="_blank">Admin/Login</a>
									<?php } ?>
									<a class="btn btn-success btn-xs" href="http://<?php echo $array['ServerName']; ?>/?XDEBUG_PROFILE" target="_blank">Profiler</a>
								</td>
							</tr>
							<?php
						}
					}
					unset( $array ); ?>
				</table>
			</div>
		</div>

		<h1>To easily spin up new WordPress sites</h1>

		<p>Use <a target="_blank" href="https://github.com/bradp/vv">Variable VVV (newest)</a></p>

		<h2>Variable VVV Commands</h2>

		<table class="table table-responsive table-bordered table-striped">
			<thead>
			<tr>
				<th>Command</th>
				<th>Description</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td>
					list or --list or -l
				</td>
				<td>
					List all VVV sites
				</td>
			</tr>
			<tr>
				<td>
					create or --create or -c
				</td>
				<td>
					Create a new site
				</td>
			</tr>
			<tr>
				<td>
					remove or --remove or -r
				</td>
				<td>
					Remove a site
				</td>
			</tr>
			<tr>
				<td>
					deployment-create or --deployment-create
				</td>
				<td>
					Set up deployment for a site
				</td>
			</tr>
			<tr>
				<td>
					deployment-remove or --deployment-remove
				</td>
				<td>
					Remove deployment for a site
				</td>
			</tr>
			<tr>
				<td>
					deployment-config or --deployment-config
				</td>
				<td>
					Manually edit deployment configuration
				</td>
			</tr>
			<tr>
				<td>
					blueprint-init or --blueprint-init
				</td>
				<td>
					Initialize blueprint file
				</td>
			</tr>
			<tr>
				<td>
					vagrant v --vagrant -v
				</td>
				<td>
					Pass vagrant command through to VVV
				</td>
			</tr>

			</tbody>
		</table>

		<p>This bash script makes it easy to spin up a new WordPress site using
			<a href="https://github.com/Varying-Vagrant-Vagrants/VVV">Varying Vagrant Vagrants</a>.</p>

		<p>You can also use the old script If Using
			<a href="https://github.com/aliso/vvv-site-wizard" target="_blank">VVV Site Wizard</a>
			<strong>But it is no longer maintained!</strong></p>

		<p>
			<strong>NOTE: </strong>This Dashboard project has no affiliation with Varying Vagrant Vagrants or any other components listed here.
		</p>

		<p>
			<small>VVV Dashboard Version: 0.0.5</small>
		</p>
	</div>
</div>
</body>
</html>
