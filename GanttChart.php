<?php
# MantisBT - a php based bugtracking system
# Copyright (C) 2002 - 2011  MantisBT Team - mantisbt-dev@lists.sourceforge.net
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

if ( false === include_once( config_get( 'plugin_path' ) . 'MantisGraph/MantisGraph.php' ) ) {
	return;
}

class GanttChartPlugin extends MantisGraphPlugin  {

	/**
	 *  A method that populates the plugin information and minimum requirements.
	 */
	function register( ) {
		$this->name = lang_get( 'plugin_ganttchart_title' );
		$this->description = lang_get( 'plugin_ganttchart_description' );
		$this->page = 'config';

		$this->version = '1.2.1';
		$this->requires = array(
			'MantisCore' => '1.2.0',
			'MantisGraph' => '1.0',
		);

		$this->author = 'Alain D\'EURVEILHER';
		$this->contact = 'alain.deurveilher@gmail.com';
		$this->url = 'https://github.com/mantisbt-plugins/GanttChart';
	}

	/**
	 * Default plugin configuration.
	 */
	function config() {
		return array(
			'show_gantt_roadmap_link'	=> ON,
			'custom_field_id_for_duration'	=> -1,
			'use_due_date_field'	=> OFF,
			'use_start_date_field'	=> ON,
			'custom_field_id_for_start_date'	=> -1,
			'eczlibrary'	=> OFF,
			'jpgraph_path' => '',
			'rows_max' => 85,
			'weeks_max' => 42, // 80
			'label_max' => 120,
			'default_duration_unit' => 'd',
			'working_hours_in_a_day' => 8,
		);
	}
	
	function init() {
		//ganttchart_autoload();
		spl_autoload_register( array( 'GanttChartPlugin', 'autoload' ) );
		
		$t_path = config_get_global('plugin_path' ). plugin_get_current() . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;

		set_include_path(get_include_path() . PATH_SEPARATOR . $t_path);
	}
	
	
	function events() {
		return array(
	    # Allow plugins to display stuff in roadmap and changelog
			'EVENT_VIEW_ROADMAP_EXTRA'   => EVENT_TYPE_CHAIN,
			'EVENT_VIEW_CHANGELOG_EXTRA' => EVENT_TYPE_CHAIN,
		);
	}

	function hooks( ) {
		$hooks = array(
			'EVENT_MENU_MAIN' => 'menu_main',
			'EVENT_VIEW_ROADMAP_EXTRA' => 'view_gantt_chart',
			'EVENT_VIEW_CHANGELOG_EXTRA' => 'view_gantt_chart',
			'EVENT_MENU_FILTER' => 'ganttchart_filter_menu',
		);
		return $hooks;
	}

	function menu_main() {
		$t_links = array();

		if ( plugin_config_get( 'show_gantt_roadmap_link' ) && access_has_project_level( config_get( 'view_summary_threshold' ) ) ) {
			$t_page = plugin_page( 'summary_gantt_chart_page', false, 'GanttChart' );
			$t_lang = plugin_lang_get( 'menu', 'GanttChart' );
			$t_links[] = "<a href=\"$t_page\">$t_lang</a>";
		}

		return $t_links;
	}
	
	
	function view_gantt_chart( $p_event, $p_project_id, $p_version_id ) {
        $t_page = plugin_page( 'summary_gantt_chart.php' ) . "&project_id=$p_project_id&version_id=$p_version_id&inherited=";
        $t_lang = plugin_lang_get( 'title' );
        return array("<a href=\"$t_page\">$t_lang</a>");
  }
  
  
	function ganttchart_filter_menu( ) {
            $f_page_number = gpc_get_int( 'page_number', 1 );
            return array( '<a href="' . plugin_page( 'filter_gantt_chart.php&page_number=' . $f_page_number ) . '" target="_blank">' . plugin_lang_get( 'gantt_bug_page_link' ) . '</a>', );
	}	

}

function ganttchart_autoload() {
}
