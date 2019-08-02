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

$f_project_id = gpc_get_int( 'project_id', null );
$f_version_id = gpc_get_int( 'version_id', null );
$f_version_string  = gpc_get_string( 'v_str', 'target_version' );
$f_inherited  = gpc_get_bool( 'inherited', true );
$f_start_index = gpc_get_int( 'start_index', -1 );
$f_length      = gpc_get_int( 'length', null );
$f_slice       = gpc_get_int( 'slice', null );

if ( null != $f_project_id && null != $f_version_id ) {
    $t_metrics = gantt_create_summary( $f_project_id, $f_version_id, $f_inherited, $f_start_index, $f_length, $f_version_string );
    $t_gantt_chart_title = project_get_name( $f_project_id );
    $t_gantt_chart_subtitle = version_full_name( $f_version_id, /* showProject */ $f_inherited, $f_project_id );

    if ( $f_start_index != -1 && $f_length != null ){
        $t_gantt_chart_subtitle .= " (" . plugin_lang_get ( 'part' ) . $f_slice . ")";
    }
    gantt_chart( $t_metrics, $t_gantt_chart_title, $t_gantt_chart_subtitle );
  }
?>