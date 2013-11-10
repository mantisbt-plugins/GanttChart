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
	$t_bugslist = gpc_get_cookie( config_get( 'bug_list_cookie' ), false );
	$t_project_id = helper_get_current_project();
  
	
	if ( $t_bugslist ) {
  	# gather the data for the gantt chart
  	$t_metrics = gantt_create_summary_from_bug_list( $t_bugslist );
  	$t_gantt_chart_title = project_get_name( $t_project_id );
  	$t_gantt_chart_subtitle = plugin_lang_get( 'filter' );
  	
  	{
      $t_filter = current_user_get_bug_filter();
    	# NOTE: this check might be better placed in current_user_get_bug_filter()
    	if ( $t_filter === false ) {
    		$t_filter = filter_get_default();
    	}
    	$t_per_page = null;
    	$t_bug_count = null;
    	$t_page_count = null;
      $t_page_number = 0;
      do {
        $t_page_number++;
    	  $rows = filter_get_bug_rows( $t_page_number, $t_per_page, $t_page_count, $t_bug_count, null, null, null, true );
      } while( $t_metrics[0][2] != $rows[0]->id );
  	
        	
			$v_start = 0;
			$v_end   = 0;

			if ( count( $rows ) > 0 ) {
				$v_start = $t_filter['per_page'] * ($t_page_number - 1) + 1;
				$v_end = $v_start + count( $rows ) - 1;
			}
			$t_gantt_chart_subtitle .= " ($v_start - $v_end / $t_bug_count)";
    }

  	gantt_chart( $t_metrics, $t_gantt_chart_title, $t_gantt_chart_subtitle );
  }
?>
