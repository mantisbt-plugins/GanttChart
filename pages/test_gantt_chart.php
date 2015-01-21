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
	
    $t_today = date( config_get( 'short_date_format' ) );//string
    $t_now = time();//timestamp
    
    #TODO:
    #Should add max width and min width according to jpgraph_gantt capabilities (MAX_GANTTIMG_SIZE)
    # and performance. From an empiric way I figured out that on my config, It would be wise to limit to a width range of 80 weeks max limit
    # and height of 90 rows max
    
    $t_week_in_seconds = 7 * 24 * 3600;
  	$t_metrics = array();
  	$t_i = 0;
  	
  	for($t_j=0;$t_j<2;$t_j++){
  	###ADD A ROW:
  	$t_actype = ACTYPE_NORMAL;
  	$t_bug_id = '1616';
  	$t_start_date = graph_date_format( $t_now );
  	$t_end_date = graph_date_format( $t_now + 86 * $t_week_in_seconds );
    $t_extra = "";
    $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, $t_start_date, $t_end_date, $t_extra );
    $t_i++;
    ###END OF ROW
    }
  	
  	$t_title = plugin_lang_get( 'gantt_bug_page_link' );
  	$t_subtitle = 'Test';
  	
  	gantt_chart( $t_metrics, $t_title, $t_subtitle );
?>
