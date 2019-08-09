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

# Copyright (c) 2019 Scott Meesseman
# Licensed under GPL2 

class GanttChartPlugin extends MantisPlugin
{
    public function register()
    {
        $this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->page = 'config';

		$this->version = '2.0.4';
		$this->requires = array(
			'MantisCore' => '2.0.1',
			'MantisGraph' => '2.0.1',
		);

		$this->author = 'Scott Meesseman';
		$this->contact = 'spmeesseman@gmail.com';
		$this->url = 'https://github.com/mantisbt-plugins/GanttChart';
    }

    function init() 
    {
        $t_path = config_get_global('plugin_path' ). plugin_get_current() . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;

		set_include_path(get_include_path() . PATH_SEPARATOR . $t_path);
    }

	public function events() {
		return array(
	    # Allow plugins to display stuff in roadmap and changelog
			'EVENT_VIEW_ROADMAP_EXTRA'   => EVENT_TYPE_CHAIN,
			'EVENT_VIEW_CHANGELOG_EXTRA' => EVENT_TYPE_CHAIN,
		);
	}

    public function hooks()
    {
        return array(
            'EVENT_MENU_MAIN' => 'menu',
            'EVENT_VIEW_ROADMAP_EXTRA' => 'view_gantt_chart',
			'EVENT_VIEW_CHANGELOG_EXTRA' => 'view_gantt_chart',
			'EVENT_MENU_FILTER' => 'ganttchart_filter_menu'
        );
    }

    public function menu()
    {
        if (plugin_config_get('show_gantt_roadmap_link') == ON) {
			return array(
				'title'=> plugin_lang_get("title"),
				'url'=> plugin_page('summary_gantt_chart_page', false, 'GanttChart'),
				'access_level'=> plugin_config_get('view_threshold_level', REPORTER),
				'icon'=> 'fa-bar-chart'
			);
		}
        return '';
    }

    function config() {
        return array(
			'show_gantt_roadmap_link'	=> OFF,
			'custom_field_id_for_duration'	=> -1,
			'use_due_date_field'	=> ON,
			'use_start_date_field'	=> OFF,
			'custom_field_id_for_start_date' => -1,
			'eczlibrary'	=> OFF,
			'jpgraph_path' => '',
			'rows_max' => 85,
			'weeks_max' => 42,
			'label_max' => 80,
			'default_duration_unit' => 'd',
			'working_hours_in_a_day' => 8,
			'view_threshold_level' => REPORTER
		);
    }

	function view_gantt_chart( $p_event, $p_project_id, $p_version_id ) 
	{
        $t_page = plugin_page( 'summary_gantt_chart.php' ) . "&project_id=$p_project_id&version_id=$p_version_id&inherited=";
        $t_lang = plugin_lang_get( 'title' );
        return array("<a href=\"$t_page\">$t_lang</a>");
 	}
  
  
	function ganttchart_filter_menu( ) 
	{
		$f_page_number = gpc_get_int( 'page_number', 1 );
		if( access_has_project_level( config_get( 'view_threshold_level' ) ) ) 
		{
			if (plugin_is_installed("IFramed"))
			{
				return array( '<a class="btn btn-sm btn-primary btn-white btn-round" href="' .
								'plugin.php?page=IFramed/main&title=Gantt%20Chart&url=' . plugin_page('filter_gantt_chart.php&page_number=' . $f_page_number)
								. '">' . plugin_lang_get( 'gantt_bug_page_link' ) . '</a>');
			}
			return array( '<a class="btn btn-sm btn-primary btn-white btn-round" href="' .
				plugin_page('filter_gantt_chart.php&page_number=' . $f_page_number) . '" target="_blank">' . plugin_lang_get( 'gantt_bug_page_link' ) . '</a>', );
		} 
		return '';
	}	

}