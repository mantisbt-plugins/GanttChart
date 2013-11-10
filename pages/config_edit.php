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

form_security_validate( 'plugin_gantt_chart_config_edit' );

auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$f_show_gantt_roadmap_link = gpc_get_int( 'show_gantt_roadmap_link', ON );
$f_use_due_date_field = gpc_get_int( 'use_due_date_field', OFF );
$f_custom_field_id_for_duration = gpc_get_int( 'custom_field_id_for_duration', -1 );
$f_use_start_date_field = gpc_get_int( 'use_start_date_field', ON );
$f_custom_field_id_for_start_date = gpc_get_int( 'custom_field_id_for_start_date', -1 );

if ( plugin_config_get( 'show_gantt_roadmap_link' ) != $f_show_gantt_roadmap_link ) {
	plugin_config_set( 'show_gantt_roadmap_link', $f_show_gantt_roadmap_link );
}

if ( plugin_config_get( 'use_due_date_field' ) != $f_use_due_date_field ) {
	plugin_config_set( 'use_due_date_field', $f_use_due_date_field );
}

if ( plugin_config_get( 'custom_field_id_for_duration' ) != $f_custom_field_id_for_duration ) {
	plugin_config_set( 'custom_field_id_for_duration', $f_custom_field_id_for_duration );
}

if ( plugin_config_get( 'use_start_date_field' ) != $f_use_start_date_field ) {
	plugin_config_set( 'use_start_date_field', $f_use_start_date_field );
}

if ( plugin_config_get( 'custom_field_id_for_start_date' ) != $f_custom_field_id_for_start_date ) {
	plugin_config_set( 'custom_field_id_for_start_date', $f_custom_field_id_for_start_date );
}

if ( plugin_config_get( 'eczlibrary' ) != config_get( 'plugin_MantisGraph_eczlibrary' ) ) {
  plugin_config_set( 'eczlibrary',  config_get('plugin_MantisGraph_eczlibrary') );
}

if ( plugin_config_get( 'jpgraph_path' ) != config_get( 'plugin_MantisGraph_jpgraph_path' ) ) {
  plugin_config_set( 'jpgraph_path',  config_get('plugin_MantisGraph_jpgraph_path') );
}

form_security_purge( 'plugin_gantt_chart_config_edit' );

print_successful_redirect( plugin_page( 'config', true ) );
?>
