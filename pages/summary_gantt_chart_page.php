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
 * @copyright Copyright (C) 2002 - 2010  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 */
 /**
  * MantisBT Core API's
  */
require_once( 'core.php' );
require_once( 'gantt_api.php' );

require_once( 'version_api.php' );
require_once( 'history_api.php' );

$f_project_id = gpc_get_int( 'project_id', helper_get_current_project() );
# Override the current page to make sure we get the appropriate project-specific configuration
$g_project_override = $f_project_id;

access_ensure_project_level( config_get( 'view_summary_threshold' ) );

$t_user_id = auth_get_current_user_id();

$t_project_ids = user_get_all_accessible_projects( $t_user_id, $f_project_id);

html_page_top( plugin_lang_get( 'menu', 'GanttChart' ) );
?>

<br />
        <table class="width100" cellspacing="1">
            <tr valign="top">
                <td class="form-title" colspan="1">
                    <?php echo lang_get( 'email_project' ); ?>
                </td>
                <td class="form-title" colspan="3">
                    <?php echo lang_get( 'versions' ) ?>
                </td>
                    <?php //echo $t_orcttab; ?>
            </tr>
<?php
//PARAMS
$p_projects = null;
$p_level = 0;
$p_cache = null;

if( null == $p_projects ) {
    $t_project_id = helper_get_current_project();
    if( ALL_PROJECTS == $t_project_id ) {
        $p_projects = current_user_get_accessible_projects();
    } else {
        $p_projects = Array(
            $t_project_id,
        );
    }
}

foreach ($p_projects as $t_project) {
?>
            <!-- PROJECTS -->
<?php
    $t_project_name = str_repeat( "&raquo; ", $p_level ) . project_get_name( $t_project );
?>
            <tr valign="top">
                <td class="category"><?php echo $t_project_name; ?></td>
                <td>
                    <table>
                    <!-- PROJECT VERSIONS -->

<?php
    $t_versions = version_get_all_rows( $t_project, /* released = */ null, /* obsolete = */ null );

    if ( count( $t_versions ) > 0 ) {
        foreach ( $t_versions as $t_version ) {

            if ( $t_version['project_id'] != $t_project ) {
                $t_inherited = true;
            } else {
                $t_inherited = false;
            }

            $t_version_name = version_full_name( $t_version['id'], /* showProject */ $t_inherited, $t_project );

            $t_released = $t_version['released'];
            $t_obsolete = $t_version['obsolete'];
            if ( !$t_obsolete ){
                $t_released_formatted = '';
                if( $t_released ){
                    $t_released_formatted = lang_get( 'released' );
                }
                if( !date_is_null( $t_version['date_order'] ) ) {
                    $t_date_formatted = ' ' . date( config_get( 'short_date_format' ), $t_version['date_order'] );		
                } else {
                    $t_date_formatted = '';
                }

                $t_version_count_issues = gantt_count_summary( $t_project, $t_version_name );
                $t_gantt_chart_max_rows = plugin_config_get( 'rows_max' );
                $t_gantt_chart_number_of_slices = ceil( ( $t_version_count_issues / $t_gantt_chart_max_rows ) );
                if ( 0 == $t_gantt_chart_number_of_slices ){
?>
                        <tr <?php echo helper_alternate_class() ?>>
                            <td><?php echo $t_released_formatted . $t_date_formatted;?></td>
                            <td><?php echo string_display( $t_version_name . " (" . plugin_lang_get( 'no_data' ) . ")" ); ?></td>
                        </tr>
<?php
                } elseif ( 1 == $t_gantt_chart_number_of_slices ) {
?>
                        <tr <?php echo helper_alternate_class() ?>>
                            <td><?php echo $t_released_formatted . $t_date_formatted;?></td>
                            <td>
                                <a href="<?php echo plugin_page( 'summary_gantt_chart.php' );?>&project_id=<?php echo $t_project; ?>&version_id=<?php echo $t_version['id']; ?>&inherited=<?php echo $t_inherited; ?>" target="_blank" alt="Gantt chart for <?php echo $t_project_name . " (" . $t_version_name . ")" ;?>"><?php echo string_display( $t_version_name ); ?></a>
                            </td>
                        </tr>
<?php
                } else {
?>
                        <tr <?php echo helper_alternate_class() ?>>
                            <td><?php echo $t_released_formatted . $t_date_formatted;?></td>
                            <td>
<?php
                    $t_display_string = string_display( $t_version_name );
                    for ( $t_i = 0; $t_i < $t_gantt_chart_number_of_slices; $t_i++ ){
                        $t_index_start = $t_i * $t_gantt_chart_max_rows;
                        $t_index_length = min( $t_gantt_chart_max_rows, ( $t_version_count_issues - $t_index_start ) );
                        $t_display_string .= " (<a href=\"" . plugin_page( 'summary_gantt_chart.php' ) . "&project_id=" . $t_project . "&version_id=" . $t_version['id'] . "&inherited=" . $t_inherited . "&start_index=" . $t_index_start . "&length=" . $t_index_length . "&slice=" . ($t_i + 1) . "\" target=\"_blank\" alt=\"Gantt chart for $t_project_name ($t_version_name)\">" . string_display( plugin_lang_get( 'part' ) . ($t_i + 1) ) . "</a>)";
                    }
                    echo $t_display_string;
?>
                            </td>
                        </tr>
<?php
                }
?>
<?php   
            }
        }
             
    }
?>
                        <!-- PROJECT VERSIONS: END -->
                    </table>
                </td>
            </tr>
<?php
}
?>
            <!-- PROJECTS: END -->
        </table>
<?php
html_page_bottom();
?>
