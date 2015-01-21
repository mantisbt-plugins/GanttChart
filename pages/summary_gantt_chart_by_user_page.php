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

	require_api( 'version_api.php' );
	require_api( 'history_api.php' );
        
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
				<?php echo lang_get( 'username' ); ?>
			</td>
    	<td class="form-title" colspan="3">
    		<?php echo lang_get( 'email_project' ) ?>
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
  		//TODO
  		//$t_users = project_get_all_user_rows( $p_project_id = ALL_PROJECTS, $p_access_level = ANYBODY, $p_include_global_users = true );
  		$todo_project_id = ALL_PROJECTS;
  		$todo_version_id = null;
  		$todo_inherited = null;
  		$t_users = project_get_all_user_rows( $todo_project_id, ANYBODY );
  		//returns array of (id, username, realname, access_level)
  		
  		foreach ( $t_users as $t_user ){
?>
        <tr valign="top">
          <td class="category"><?php echo $t_user['username']; ?></td>
          <td>
            <table>
<?php
?>
  		
          		<tr <?php echo helper_alternate_class() ?>>
          			<td>
          				<a href="<?php echo plugin_page( 'summary_gantt_chart_by_user.php' );?>&user_id=<?php echo $t_user['id'];?>&project_id=<?php echo $todo_project_id; ?>&version_id=<?php echo $todo_version_id; ?>&inherited=<?php echo $todo_inherited; ?>" target="_blank" alt="Gantt chart for <?php echo $t_project_name . " (" . $t_version_name . ")" ;?>"><?php echo lang_get( 'all_projects' );/*string_display( $t_version_name )*/ ?></a>
          			</td>
          		</tr>
            </table>
          </td>
        </tr>
<?php
      }
?>
	</table>
<?php
	html_page_bottom();
?>
