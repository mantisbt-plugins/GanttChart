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

require_once( 'core.php' );
require_api( 'custom_field_api.php' );

auth_reauthenticate( );
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

html_page_top( plugin_lang_get( 'title' ) );

print_manage_menu( );
?>
<?php
// Get the path to php.ini using the php_ini_loaded_file() 
// @TODO: function available as of PHP 5.2.4
$t_ini_path = php_ini_loaded_file();

// Parse php.ini
$t_ini = parse_ini_file( $t_ini_path );

// Print and compare the values, note that using get_cfg_var()
//if (array_key_exists( 'memory_limit', $t_ini ) ){
//    echo '(parsed) memory_limit = yes' . "<br />";
//    echo '(loaded) memory_limit = ' . get_cfg_var( 'memory_limit' ) . "<br />";
//}
?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th colspan="2"><?php echo plugin_lang_get( 'title', 'MantisGraph' ) . ': ' . plugin_lang_get( 'config' )?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th class="category"><?php echo plugin_lang_get( 'library', 'MantisGraph' ); ?></th>
                <td><?php echo ( plugin_config_get( 'eczlibrary' ) ) ? plugin_lang_get( 'bundled', 'MantisGraph' ) : "JpGraph";?></td>
            </tr>
            <tr>
                <th class="category"><?php echo plugin_lang_get( 'jpgraph_path', 'MantisGraph' ); ?><br /><span class="small"><?php echo plugin_lang_get( 'jpgraph_path_default' )?></span></th>
                <td><?php echo plugin_config_get( 'jpgraph_path' );?></td>
            </tr>
            <tr>
                <th class="category"><?php echo plugin_lang_get( 'font', 'MantisGraph' ); ?></th>
                <td><?php echo config_get( 'plugin_' . 'MantisGraph' . '_' . 'font' );?></td>
            </tr>
        </tbody>
    </table>
</div><!-- /.table-container -->

<div id="ganttchart-config-div" class="form-container">
    <form id="ganttchart-config-form" action="<?php echo plugin_page( 'config_edit' )?>" method="post">
        <fieldset>
            <legend><span><?php echo plugin_lang_get( 'title' ) . ': ' . plugin_lang_get( 'config' )?></span></legend>
            <?php echo form_security_field( 'plugin_gantt_chart_config_edit' ); ?>
            
            <div class="field-container">
                <label><span><?php echo plugin_lang_get( 'show_gantt_roadmap_link' )?></span></label>
                <span class="radio">
                    <input type="radio" id="show_enabled" name="show_gantt_roadmap_link" value="1" <?php echo( ON == plugin_config_get( 'show_gantt_roadmap_link' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="show_enabled"><?php echo plugin_lang_get('enabled')?></label>
                    <input type="radio" id="show_disabled" name="show_gantt_roadmap_link" value="0" <?php echo( OFF == plugin_config_get( 'show_gantt_roadmap_link' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="show_disabled"><?php echo plugin_lang_get('disabled')?></label>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="spacer">
            </div><!-- /.field-container -->
            <div class="field-container">
                <label><span><?php echo plugin_lang_get( 'use_start_date_field' )?></span></label>
                <span class="radio">
                    <input type="radio" id="use_start_field_enabled" name="use_start_date_field" value="1" <?php echo( ON == plugin_config_get( 'use_start_date_field' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="use_start_field_enabled"><?php echo plugin_lang_get('enabled')?></label>
                    <input type="radio" id="use_start_field_disabled" name="use_start_date_field" value="0" <?php echo( OFF == plugin_config_get( 'use_start_date_field' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="use_start_field_disabled"><?php echo plugin_lang_get('disabled')?></label>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="field-container">
                <label><span><?php echo plugin_lang_get( 'start_date_custom_field' )?></span></label>
                <span class="select">
                    <select name="custom_field_id_for_start_date">
                      <option value="-1"></option>
<?php
# You need either global permissions or project-specific permissions to link
#  custom fields
if ( count( custom_field_get_ids() ) > 0 ) {
  $t_custom_fields = custom_field_get_ids();

  foreach( $t_custom_fields as $t_field_id )
  {
    $t_desc = custom_field_get_definition( $t_field_id );
    $t_type = custom_field_type( $t_field_id );
    if ( CUSTOM_FIELD_TYPE_DATE == $t_type ){
      if ( plugin_config_get( 'custom_field_id_for_start_date' ) == $t_field_id ) {
        $t_selected = 'selected';
      } else {
        $t_selected = '';
      }
      echo "                      <option value=\"$t_field_id\" $t_selected>" . string_attribute( $t_desc['name'] ) . '</option>' ;
    }
  }
}
?>
                    </select>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="field-container">
                <label><span><?php echo plugin_lang_get( 'field_to_use' )?></span></label>
                <span class="radio">
                    <input type="radio" id="use_custom_field" name="use_due_date_field" value="0" <?php echo( OFF == plugin_config_get( 'use_due_date_field' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="use_custom_field"><?php echo plugin_lang_get('custom_field')?></label>
                    <input type="radio" id="use_due_date" name="use_due_date_field" value="1" <?php echo( ON == plugin_config_get( 'use_due_date_field' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="use_due_date"><?php echo lang_get('due_date')?></label>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="field-container">
                <label>
                    <span>
                        <?php echo plugin_lang_get( 'custom_field_id_for_duration' )?><br />
                        <span class="small"><?php echo plugin_lang_get( 'format_values_for_duration' )?></span><br /><br />
                        <span class="small"><?php echo plugin_lang_get( 'format_custom_field_for_duration' )?></span>
                    </span>
                </label>
                <span class="select">
                    <select name="custom_field_id_for_duration">
                      <option value="-1"></option>
<?php
# You need either global permissions or project-specific permissions to link
#  custom fields
/*
 * Custom field must be of type: 'String'
 * Regular expression must be: ^([1-9]\d*)(?(1)[dh])$ 
 */
if ( count( custom_field_get_ids() ) > 0 ) {
  $t_custom_fields = custom_field_get_ids();

  foreach( $t_custom_fields as $t_field_id )
  {
    $t_desc = custom_field_get_definition( $t_field_id );
    if ( plugin_config_get( 'custom_field_id_for_duration' ) == $t_field_id ) {
      $t_selected = 'selected';
    } else {
      $t_selected = '';
    }
    echo "                      <option value=\"$t_field_id\" $t_selected>" . string_attribute( $t_desc['name'] ) . '</option>' ;
  }
}
?>
                    </select>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="field-container">
                <label><span><?php echo plugin_lang_get( 'default_duration_unit' )?></span></label>
                <span class="radio">
                    <input type="radio" id="duration_day" name="default_duration_unit" value="d" <?php echo( 'd' === plugin_config_get( 'default_duration_unit' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="duration_day"><?php echo plugin_lang_get('days')?></label>
                    <input type="radio" id="duration_hour" name="default_duration_unit" value="h" <?php echo( 'h' === plugin_config_get( 'default_duration_unit' ) ) ? 'checked="checked" ' : ''?>/>
                    <label for="duration_hour"><?php echo plugin_lang_get('hours')?></label>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="field-container">
                <label>
                    <span>
                        <?php echo plugin_lang_get( 'working_hours_in_a_day' )?>
                        <span class="small"><?php echo  " [1-24]";?></span>
                    </span>
                </label>
                <span class="input">
                    <input type="text" name="working_hours_in_a_day" value="<?php echo plugin_config_get( 'working_hours_in_a_day' );?>" />
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="spacer">
            </div><!-- /.field-container -->
            <div class="field-container">
                <label><span><?php echo "Maximum rows to display"?></span></label>
                <span class="input">
                    <input type="text" name="rows_max" value="<?php echo plugin_config_get( 'rows_max' );?>" />
                    <label for="rows_max"><?php echo  " (Default: " . 85 . ")";?></label>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="field-container">
                <label><span><?php echo "Maximum weeks to display"?></span></label>
                <span class="input">
                    <input type="text" name="weeks_max" value="<?php echo plugin_config_get( 'weeks_max' );?>" />
                    <label for="weeks_max"><?php echo  " (Default: " . 42 . ")";?></label>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <div class="field-container">
                <label><span><?php echo "Maximum length of the labels"?></span></label>
                <span class="input">
                    <input type="text" name="label_max" value="<?php echo plugin_config_get( 'label_max' );?>" />
                    <label for="label_max"><?php echo  " (Default: " . 120 . ")";?></label>
                </span>
                <span class="label-style"></span>
            </div><!-- /.field-container -->
            <span class="submit-button"><input type="submit" class="button" value="<?php echo lang_get( 'change_configuration' )?>" /></span>
        </fieldset>
    </form>
</div><!-- /.form-container -->

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th colspan="2"><?php echo 'PHP' . ': ' . plugin_lang_get( 'config' )?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th class="category"><?php echo 'Loaded php.ini file' ?></th>
                <td><?php echo $t_ini_path;?></td>
            </tr>
            <tr>
                <th class="category"><?php echo 'memory_limit (minimum required 128M)' ?></th>
                <td><?php if ( array_key_exists( 'memory_limit', $t_ini ) ) echo get_cfg_var( 'memory_limit' );?></td>
            </tr>
        </tbody>
    </table>
</div><!-- /.table-container -->

<?php
html_page_bottom();
?>