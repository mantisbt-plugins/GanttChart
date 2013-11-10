<?php
# MantisBT - a php based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

	/**
	 * @package MantisBT
	 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	 * @copyright Copyright (C) 2002 - 2011  MantisBT Team - mantisbt-dev@lists.sourceforge.net
	 * @link http://www.mantisbt.org
	 */
	 /**
	  * MantisBT Core API's
	  */
	require_once( 'core.php' );

	require_once( 'gantt_api.php' );
	require_once( 'version_api.php' );

	access_ensure_project_level( config_get( 'view_summary_threshold' ) );
	
	$f_project_id = gpc_get_int( 'project_id', null );
	$f_version_id = gpc_get_int( 'version_id', null );
	$f_inherited = gpc_get_bool( 'inherited', true );
	
	if ( null != $f_project_id && null != $f_version_id ) {
  	# gather the data for the graphs
  	$t_metrics = gantt_create_summary( $f_project_id, $f_version_id, $f_inherited );
//   	$t_token = token_set( TOKEN_GANTT, serialize( $t_metrics ) );
//   
//   	$t_token = token_get_value( TOKEN_GANTT );
//   	if ( $t_token == null ) {
//   		$t_metrics = gantt_create_summary( $f_project_id, $f_version_id, $f_inherited );
//   	} else {
//   		$t_metrics = unserialize( $t_token );
//   	}
//    
//   	gantt_chart_simple_example();//DEBUG: This one is OK
//   	gantt_chart_simple_example2( $t_metrics, $f_project_id, version_full_name( $f_version_id, /* showProject */ $f_inherited, $f_project_id ) );//DEBUG: This is OK
  	gantt_table( $t_metrics, project_get_name( $f_project_id ), version_full_name( $f_version_id, /* showProject */ $f_inherited, $f_project_id ) );
  }
?>
