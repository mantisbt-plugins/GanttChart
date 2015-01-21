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

	plugin_require_api( 'core/gantt_api.php' );
	require_api( 'version_api.php' );

	access_ensure_project_level( config_get( 'view_summary_threshold' ) );
	
	$t_user_id = auth_get_current_user_id();
	$f_user_id = gpc_get_int( 'user_id', $t_user_id );
	
	
	
	$f_project_id = gpc_get_int( 'project_id', null );
	$f_version_id = gpc_get_int( 'version_id', null );
	$f_inherited = gpc_get_bool( 'inherited', true );
	
  $t_metrics = gantt_create_summary_by_user( $f_user_id, $f_project_id, $f_version_id, $f_inherited );
  $t_chart_title = gantt_create_user_chart_title( $f_user_id );
  $t_chart_subtitle = gantt_create_user_chart_subtitle( $f_user_id, $f_project_id, $f_version_id, $f_inherited );
  
  gantt_chart( $t_metrics, $t_chart_title, $t_chart_subtitle );
?>
