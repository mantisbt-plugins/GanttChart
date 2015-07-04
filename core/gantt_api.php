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
 * @package CoreAPI
 * @subpackage GraphAPI
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2011  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 */
require_once( 'core.php' );
if ( plugin_config_get( 'eczlibrary' ) != config_get( 'plugin_MantisGraph_eczlibrary' ) ) {
  plugin_config_set( 'eczlibrary',  config_get('plugin_MantisGraph_eczlibrary') );
}
if ( plugin_config_get( 'jpgraph_path' ) != config_get( 'plugin_MantisGraph_jpgraph_path' ) ) {
  plugin_config_set( 'jpgraph_path',  config_get('plugin_MantisGraph_jpgraph_path') );
}
plugin_require_api( 'core/graph_api.php', 'MantisGraph' );
require_api( 'version_api.php' );
require_api( 'history_api.php' );

 
if( OFF == plugin_config_get( 'eczlibrary' ) ) {
  $t_font_path = get_font_path();
  if( $t_font_path !== '' && !defined('TTF_DIR') ) {
    define( 'TTF_DIR', $t_font_path );
  }
  $t_jpgraph_path = plugin_config_get( 'jpgraph_path' );
  if( $t_jpgraph_path !== '' ) {
    set_include_path(get_include_path() . PATH_SEPARATOR . $t_jpgraph_path );
    $ip = get_include_path();
    require_once( 'jpgraph_gantt.php' );
    require_once( 'jpgraph_mgraph.php' );
  } else {
    require_lib( 'jpgraph/jpgraph_gantt.php' );
    require_lib( 'jpgraph/jpgraph_mgraph.php' );
  }
} else {
  require_lib( 'ezc/Base/src/base.php' );
}

// function gantt_get_font() {
//  $t_font = gantt_plugin_config_get_from_other( 'font', 'MantisGraph', 'arial' );
// 
//  if ( plugin_config_get( 'eczlibrary' ) == ON ) {
//    $t_font_map = array(
//      'arial' => 'arial.ttf',
//      'verdana' => 'verdana.ttf',
//      'trebuchet' => 'trebuc.ttf',
//      'verasans' => 'Vera.ttf',
//      'times' => 'times.ttf',
//      'georgia' => 'georgia.ttf',
//      'veraserif' => 'VeraSe.ttf',
//      'courier' => 'cour.ttf',
//      'veramono' => 'VeraMono.ttf',
//    );
// 
//    if( isset( $t_font_map[$t_font] ) ) {
//      $t_font = $t_font_map[$t_font];
//    } else {
//      $t_font = 'arial.ttf';
//    }
//    $t_font_path = get_font_path();
//    if( empty($t_font_path) ) {
//      error_text('Unable to read/find font', 'Unable to read/find font');
//    }
//    $t_font_file = $t_font_path . $t_font;
//    if( file_exists($t_font_file) === false || is_readable($t_font_file) === false ) {
//      error_text('Unable to read/find font', 'Unable to read/find font');
//    }
//    return $t_font_file;
//  } else {
//    $t_font_map = array(
//      'arial' => FF_ARIAL,
//      'verdana' => FF_VERDANA,
//      'trebuchet' => FF_TREBUCHE,
//      'verasans' => FF_VERA,
//      'times' => FF_TIMES,
//      'georgia' => FF_GEORGIA,
//      'veraserif' => FF_VERASERIF,
//      'courier' => FF_COURIER,
//      'veramono' => FF_VERAMONO,
//    );
// 
//    if( isset( $t_font_map[$t_font] ) ) {
//      return $t_font_map[$t_font];
//    } else {
//      return FF_FONT1;
//    }
//  }
// }

# ## Gantt API ###
# --------------------
function gantt_table( $p_metrics, $p_title, $p_subtitle, $p_graph_width = 300, $p_graph_height = 380 ) {
    $t_metrics = $p_metrics['metrics'];
    $t_range = $p_metrics['range'];
?>
<table>
  <tr>
    <td class="form-title" >Row</td>
    <td class="form-title" >Issue</td>
    <td class="form-title" >Start Date</td>
    <td class="form-title" >End Date</td>
    <td class="form-title" >Comment</td>
  </tr>
<?php
    foreach ( $t_metrics as $t_metric_row ) {
      

        $t_row            = $t_metric_row[0];
        $t_activity_type  = $t_metric_row[1];
        $t_bug_id         = $t_metric_row[2];
        $t_start_date     = $t_metric_row[3];
        $t_end_date       = $t_metric_row[4];
        $t_extra          = $t_metric_row[5];
        $t_level          = $t_metric_row[6];
        $t_constraints    = $t_metric_row[7];

        if( $t_start_date < $t_range['min'] ) {
            $t_extra = "(<-- " . graph_date_format( $t_start_date ) . ") " . $t_extra;
            $t_start_date = $t_range['min'];
        } else if( $t_range['max'] < $t_start_date ) {
            $t_extra = "(--> " . graph_date_format( $t_start_date ) . ") " . $t_extra;
            $t_start_date = $t_range['max'];
        }
      
      
        switch ( $t_activity_type ) {
            case ACTYPE_NORMAL:
                if( $t_end_date < $t_range['min'] ) {
                    $t_extra = "(<-- " . graph_date_format( $t_end_date ) . ") " . $t_extra;
                    $t_end_date = $t_range['min'];
                } else if( $t_range['max'] < $t_end_date ) {
                    $t_extra = "(--> " . graph_date_format( $t_end_date ) . ") " . $t_extra;
                    $t_end_date = $t_range['max'];
                }
                $t_activity = new GanttBar( $t_row, bug_format_summary( $t_bug_id, SUMMARY_FIELD ), graph_date_format( $t_start_date ), graph_date_format( $t_end_date ), " $t_bug_id" . $t_extra );
?>
  <tr>
    <td><?php echo $t_row;?></td>
    <td><?php echo bug_format_summary( $t_bug_id, SUMMARY_FIELD );?></td>
    <td><?php echo graph_date_format( $t_start_date );?></td>
    <td><?php echo graph_date_format( $t_end_date );?></td>
    <td><?php echo " $t_bug_id" . $t_extra;?></td>
  </tr>
<?php
                break;
            case ACTYPE_MILESTONE:
                $t_milestone = new MileStone( $t_row, bug_format_summary( $t_bug_id, SUMMARY_FIELD ), graph_date_format( $t_start_date ), " $t_bug_id (not started)" . $t_extra );
?>
  <tr>
    <td><?php echo $t_row;?></td>
    <td><?php echo bug_format_summary( $t_bug_id, SUMMARY_FIELD );?></td>
    <td><?php echo graph_date_format( $t_start_date );?></td>
    <td><?php echo "";?></td>
    <td><?php echo " $t_bug_id (not started)" . $t_extra;?></td>
  </tr>
<?php
                break;
        }
    }
?>
</table>
<?php
}

/**
 * Print the color legend for the status colors
 * @param string
 * @return null
 */
function gantt_chart_legend( $p_show = true ){
    
    $graph = new Graph( 800, 40, 'auto' );
    $graph->SetScale('textlin');
    $graph->SetMarginColor('white');
    $graph->SetBox(false);
    $graph->ygrid->SetFill(false);
    $graph->yaxis->Hide(true);
    $graph->xaxis->Hide(true);
    $graph->yaxis->HideLine(true);
    $graph->yaxis->HideTicks(true,true);

    $t_bplot_array = array();
  
  
    $t_status_array = MantisEnum::getAssocArrayIndexedByValues( config_get( 'status_enum_string' ) );
    $t_status_names = MantisEnum::getAssocArrayIndexedByValues( lang_get( 'status_enum_string' ) );
    $enum_count = count( $t_status_array );

    # read through the list and eliminate unused ones for the selected project
    # assumes that all status are are in the enum array
    $t_workflow = config_get( 'status_enum_workflow' );
    if( !empty( $t_workflow ) ) {
        foreach( $t_status_array as $t_status => $t_name ) {
            if( !isset( $t_workflow[$t_status] ) ) {

                # drop elements that are not in the workflow
                unset( $t_status_array[$t_status] );
            }
        }
    }

    # draw the status bar
    $width = (int)( 100 / count( $t_status_array ) );
    foreach( $t_status_array as $t_status => $t_name ) {
        $t_val = $t_status_names[$t_status];
        $t_color = get_status_color( $t_status );
        // Create the bars
        $lplot = new LinePlot( array( 0 ) );
        $lplot->SetFillColor( $t_color );
        //Legend
        $lplot->SetLegend( $t_val );

        $graph->Add($lplot);
    }
  
    $graph->legend->SetFrameWeight(0);
    $graph->legend->SetColumns( $enum_count );
    $graph->legend->SetColor('#4E4E4E','#000000');
    $graph->legend->SetAbsPos(2,2,'left','top');
    $graph->legend->SetHColMargin(2);  
//    $graph->title->Set("Legend");
  
    if ( $p_show ){
        // Display the graph
        $graph->Stroke();
    } else {
        // Return the graph
        return $graph;
    }
}

function gantt_chart_get_height( $p_gantt_chart ){
  $tm=0;//Top Margin
  $bm=0;//Bottom Margin
  // First find out the height
  $n=$p_gantt_chart->GetBarMaxLineNumber()+1;
  $m=max($p_gantt_chart->GetMaxLabelHeight(),$p_gantt_chart->GetMaxBarAbsHeight());
//   $height=$n*((1+$p_gantt_chart->iLabelVMarginFactor)*$m);
  $height=$n*((1+0.4)*$m);

  // Add the height of the scale titles
  $h=$p_gantt_chart->scale->GetHeaderHeight();
  $height += $h;

  // Calculate the top margin needed for title and subtitle
  if( $p_gantt_chart->title->t != "" ) {
      $tm += $p_gantt_chart->title->GetFontHeight($p_gantt_chart->img);
  }
  if( $p_gantt_chart->subtitle->t != "" ) {
      $tm += $p_gantt_chart->subtitle->GetFontHeight($p_gantt_chart->img);
  }

  // ...and then take the bottom and top plot margins into account
  $height += $tm + $bm + $p_gantt_chart->scale->iTopPlotMargin + $p_gantt_chart->scale->iBottomPlotMargin;
  
  return $height;
  
  
}


# --------------------
function gantt_chart( $p_metrics, $p_title, $p_subtitle, $p_graph_width = 300, $p_graph_height = 380 ) {
    $t_graph_font = graph_get_font();
    $t_metrics = $p_metrics['metrics'];
    $t_range = $p_metrics['range'];
     
    
    // Diff in weeks of the range:
    $t_60s = 60; // 1 minute
    $t_60min = 60; // 1 hour
    $t_24h = 24; // 1 day
    $t_7d = 7; // 1 week
    $t_minute = $t_60s;
    $t_hour = $t_60min * $t_minute;
    $t_day = $t_24h * $t_hour;
    $t_week = $t_7d * $t_day;

    $t_gantt_chart_max_rows = plugin_config_get( 'rows_max' );
    error_check( is_array( $t_metrics ) ? count( $t_metrics ) : 0, $p_title . " (" . $p_subtitle . ")" );
  
    if ( plugin_config_get( 'eczlibrary' ) == ON ) {
        // DO NOTHING SINCE eczlibrary DOES NOT SUPPORT GANTT CHART
    } else {
  
        // A new graph with automatic size
        $graph = new GanttGraph (0, 0, "auto");

        $graph->SetShadow();
        // Add title and subtitle
        $graph->title-> Set($p_title);
        $graph->title-> SetFont( $t_graph_font, FS_BOLD, 12);
        $graph->subtitle-> Set( $p_subtitle );

        // Show day, week and month scale
        $graph->ShowHeaders( GANTT_HDAY | GANTT_HWEEK | GANTT_HMONTH);

        // Instead of week number show the date for the first day in the week
        // on the week scale
        $graph->scale-> week->SetStyle(WEEKSTYLE_FIRSTDAY);

        // Make the week scale font smaller than the default
        $graph->scale-> week->SetFont($t_graph_font, FS_NORMAL, 8 );

        // Use the short name of the month together with a 2 digit year
        // on the month scale
        $graph->scale->month->SetStyle( MONTHSTYLE_SHORTNAMEYEAR4);
        $graph->scale->month->SetFontColor("white");
        $graph->scale->month->SetBackgroundColor("blue");

        // Setup a horizontal grid
        $graph->hgrid->Show();
        $graph->hgrid->SetRowFillColor('darkblue@0.9');
        // Setup a vertical grid
        //   $graph->vgrid->Show();

        //Setup the divider display
        $graph->scale->divider->SetWeight(3);
        $graph->scale->divider->SetColor("darkblue");
        $graph->scale->dividerh->SetWeight(3);
        $graph->scale->dividerh->SetColor("darkblue");
        $graph->scale->dividerh->Show();

        $graph->scale->actinfo->vgrid->SetStyle('solid');
        $graph->scale->actinfo->vgrid->SetColor('darkblue');
        $graph->scale->actinfo->vgrid->Show();

        //   // Set the column headers and font
        //   $graph->scale->actinfo->SetColTitles( array('Task','Start','End'),array(100));
        //   $graph->scale->actinfo->SetFont( $t_graph_font, FS_BOLD, 10 );

        //Adding columns:
        //The following is an example: 1st element, an array of the columns,
        //  2nd element an optional array of min width of the columns (here the min width of the 2 first columns)
        //$graph->scale->actinfo->SetColTitles(
        //  array('Note','Task','Duration','Start','Finish'),array(30,100));

        //Adding a table title
        $graph->scale->tableTitle->Set( "$p_subtitle" );
        $graph->scale->tableTitle->SetFont( $t_graph_font, FS_NORMAL, 8 );
        $graph->scale->SetTableTitleBackground( 'darkblue@0.6' );
        $graph->scale->tableTitle->Show();

        //           if ( null != $t_constrain ){
        //           $t_activity->SetConstrain( $t_constrain, CONSTRAIN_ENDSTART );
        //         }
        //     if ( null != $t_constrain ){
        //       $t_activity->SetConstrain( $t_constrain['row'], $t_constrain['type'] );
        //     }
        
        // We first need to get the list of rows, in order to know whether to
        // display the constraint or not (in case of missing referenced row)
        $t_row_list = array();
        foreach ( $t_metrics as $t_metric_row ) {
            $t_row_list[] = $t_metric_row[0];
        }
        
        foreach ( $t_metrics as $t_metric_row ) {

            $t_row            = $t_metric_row[0] % $t_gantt_chart_max_rows;
            $t_activity_type  = $t_metric_row[1];
            $t_bug_id         = $t_metric_row[2];
            $t_start_date     = $t_metric_row[3];
            $t_end_date       = $t_metric_row[4];
            $t_extra          = " $t_bug_id" . $t_metric_row[5];
            $t_level          = $t_metric_row[6];
            $t_constraints    = $t_metric_row[7];
            
            if( isset ( $t_level ) ){
                $t_row_label = utf8_str_pad( '', $t_level * 2, ' ' ) . htmlspecialchars_decode( bug_format_summary( $t_bug_id, SUMMARY_FIELD ) );
            } else {
                $t_row_label = htmlspecialchars_decode( bug_format_summary( $t_bug_id, SUMMARY_FIELD ) );
            }

            // Limit the label to max defined
            $t_row_label = ( strlen( $t_row_label ) > plugin_config_get( 'label_max' ) ) ? substr( $t_row_label, 0, plugin_config_get( 'label_max' ) - 3 ) . '...' : $t_row_label;
            
            
            $t_activity_arr = array(
                'left'  => null,
                'main'  => array(
                        'row'   => $t_row,
                        'label' => $t_row_label,
                        'start' => $t_start_date,
                        'end'   => $t_end_date,
                        'info'  => $t_extra
                    ),
                'right' => null
            );
            
            if( $t_end_date < $t_range['min'] ) {
                // complete left bar
                //   **  | o[ ]-[ ]o
                $t_activity_arr = array(
                    'left'  => array(
                        'row'   => $t_row,
                        'label' => $t_row_label,
                        'start' => $t_range['min'],
                        'end'   => $t_range['min'],// + $t_day,
                        'info'  => "<- " . graph_date_format( $t_start_date )
                    ),
                    'main'  => null,
                    'right' => array(
                        'row'   => $t_row,
                        'label' => "",
                        'start' => $t_range['min'] + $t_day,//4 * $t_day,
                        'end'   => $t_range['min'] + $t_day,//5 * $t_day,
                        'info'  => "<<- [" . graph_date_format( $t_start_date ) . " / " . graph_date_format( $t_end_date ) . "]" . $t_extra
                    )
                );
                
            } else if( $t_range['max'] < $t_start_date ) {
                // complete right bar
                //   o[ ]-[ ]o | **
                $t_activity_arr = array(
                    'left'  => array(
                        'row'   => $t_row,
                        'label' => $t_row_label,
                        'start' => $t_range['max'] - $t_day,//5 * $t_day,
                        'end'   => $t_range['max'] - $t_day,//4 * $t_day,
                        'info'  => ""
                    ),
                    'main'  => null,
                    'right' => array(
                        'row'   => $t_row,
                        'label' => "",
                        'start' => $t_range['max'],// - $t_day,
                        'end'   => $t_range['max'],
                        'info'  => "[" . graph_date_format( $t_start_date ) . " / " . graph_date_format( $t_end_date ) . "] ->>" . $t_extra
                    )
                );
            } else {
                if( $t_start_date < $t_range['min'] ) {
                    // left bar
                    //   *  | o[ ]-[    ]
                    $t_activity_arr['left'] = array(
                        'row'   => $t_row,
                        'label' => '',
                        'start' => $t_range['min'],
                        'end'   => $t_range['min'],// + $t_day,
                        'info'  => "<- " . graph_date_format( $t_start_date )
                    );
                    $t_activity_arr['main']['start'] = $t_range['min'] + $t_day;//4 * $t_day;// @TODO: what happens if duration is less than that
                }
                if( $t_range['max'] < $t_end_date ) {
                    // right bar
                    //  [     ]-[ ]o | *
                    $t_activity_arr['main']['end'] = $t_range['max'] - $t_day;//4 * $t_day;
                    $t_activity_arr['main']['info'] = "";
                    $t_activity_arr['right'] = array(
                        'row'   => $t_row,
                        'label' => "",
                        'start' => $t_range['max'],// - 2 * $t_day,
                        'end'   => $t_range['max'],
                        'info'  => graph_date_format( $t_end_date ) . " ->" . $t_extra
                    );
                }
            }
            
            switch ( $t_activity_type ) {
                case ACTYPE_NORMAL:
                    if ( null != $t_activity_arr['left'] ){
                        $t_activity_left = new GanttBar( $t_activity_arr['left']['row'], $t_activity_arr['left']['label'], graph_date_format( $t_activity_arr['left']['start'] ), graph_date_format( $t_activity_arr['left']['end'] ), $t_activity_arr['left']['info'] );
                        // Add a left marker
                        $t_activity_left->leftMark->Show();
                        $t_activity_left->leftMark->SetType( MARK_FILLEDCIRCLE );
                        $t_activity_left->leftMark->SetWidth( 8 );
//                        $t_activity_left->leftMark->SetColor( 'red' );
                        $t_activity_left->leftMark->SetFillColor( 'red' );
                        $t_activity_left->leftMark->title->Set( '' );
                        $t_activity_left->leftMark->title->SetFont( $t_graph_font, FS_NORMAL, 8 );
                        $t_activity_left->leftMark->title->SetColor( 'white' );
                        if ( null != gantt_get_resolution_date( $t_bug_id ) ){
                            $t_activity_left->SetPattern( BAND_RDIAG, get_status_color( bug_get_field( $t_bug_id, 'status' ) ) );
                        }
                        $t_activity_left->SetFillColor( get_status_color( bug_get_field( $t_bug_id, 'status' ) ) );
                        
                        
                    }
                    if ( null != $t_activity_arr['main'] ){
                        $t_activity_main = new GanttBar( $t_activity_arr['main']['row'], $t_activity_arr['main']['label'], graph_date_format( $t_activity_arr['main']['start'] ), graph_date_format( $t_activity_arr['main']['end'] ), $t_activity_arr['main']['info'] );
                        if ( null != gantt_get_resolution_date( $t_bug_id ) ){
                            $t_activity_main->SetPattern( BAND_RDIAG, get_status_color( bug_get_field( $t_bug_id, 'status' ) ) );
                        }
                        $t_activity_main->SetFillColor( get_status_color( bug_get_field( $t_bug_id, 'status' ) ) );
                        $t_activity_main->title->SetFont( $t_graph_font, FS_NORMAL, 8 );
                        // Set the constraint if any...
                        foreach( $t_constraints as $t_constraint ){
                            // ... and if possible
                            if ( in_array( $t_constraint['row'], $t_row_list ) ){
                                $t_activity_main->SetConstrain( $t_constraint['row'], $t_constraint['type'] );
                            }
                        }
                        
                        $graph->add( $t_activity_main );
                    }
                    if ( null != $t_activity_arr['right'] ){
                        $t_activity_right = new GanttBar( $t_activity_arr['right']['row'], $t_activity_arr['right']['label'], graph_date_format( $t_activity_arr['right']['start'] ), graph_date_format( $t_activity_arr['right']['end'] ), $t_activity_arr['right']['info'] );
                        // Add a left marker
                        $t_activity_right->rightMark->Show();
                        $t_activity_right->rightMark->SetType( MARK_FILLEDCIRCLE );
                        $t_activity_right->rightMark->SetWidth( 8 );
                        $t_activity_right->rightMark->SetColor( 'red' );
                        $t_activity_right->rightMark->SetFillColor( 'red' );
                        $t_activity_right->rightMark->title->Set( '' );
                        $t_activity_right->rightMark->title->SetFont( $t_graph_font, FS_NORMAL, 8 );
                        $t_activity_right->rightMark->title->SetColor( 'white' );
                        if ( null != gantt_get_resolution_date( $t_bug_id ) ){
                            $t_activity_right->SetPattern( BAND_RDIAG, get_status_color( bug_get_field( $t_bug_id, 'status' ) ) );
                        }
                        $t_activity_right->SetFillColor( get_status_color( bug_get_field( $t_bug_id, 'status' ) ) );
                        
                        
                    }
                    
                    if( isset( $t_activity_left ) ){
                        $graph->add( $t_activity_left );
                    }
                    if( isset( $t_activity_right ) ){
                        $graph->add( $t_activity_right );
                    }
  
                    break;
                case ACTYPE_MILESTONE:
                    $t_size = 5;
                    if( $t_start_date < $t_range['min'] ) {
                        $t_extra = "(<-- " . graph_date_format( $t_start_date ) . ")" . $t_extra;
                        $t_start_date = $t_range['min'];
                        $t_size = 8;
                    } else if( $t_range['max'] < $t_start_date ) {
                        $t_extra = "(--> " . graph_date_format( $t_start_date ) . ")" . $t_extra;
                        $t_start_date = $t_range['max'];
                        $t_size = 8;
                    }

                    $t_milestone = new MileStone( $t_row, $t_row_label, graph_date_format( $t_start_date ), $t_extra );
                    $t_milestone->title->SetFont( $t_graph_font, FS_NORMAL, 8);
                    $t_milestone->mark->SetType( MARK_FILLEDCIRCLE );
                    $t_milestone->mark->SetWidth( $t_size );
                    if( 5 != $t_size ){
                        $t_milestone->mark->SetFillColor( 'red' );
                    }

                //         foreach( $t_constraints as $t_constraint){
                //           $t_milestone->SetConstrain( $t_constraint['row'], $t_constraint['type'] );
                //         }

                    $graph->add( $t_milestone );
                    break;
            }
        }
  
        // Setting the min and max date:
        $t_minmax = $graph->GetBarMinMax();
        $t_week_in_seconds = 7 * 24 * 3600;
        // 1 week offset min:
        if ( ( $t_minmax[0] - $t_week_in_seconds ) > 0 ){
            $t_graph_offset_min = $t_minmax[0] - $t_week_in_seconds;
        } else {
            $t_graph_offset_min = $t_minmax[0];
        }
        // 2 weeks offset max:
        $t_graph_offset_max = $t_minmax[1] + (3 * $t_week_in_seconds);
        $graph->SetDateRange( graph_date_format( $t_graph_offset_min ), graph_date_format( $t_graph_offset_max ) );

        // Add a vertical line for today if in the range of GetBarMinMax() (retruns an arry ($min, $max) ):
        $t_minmax = $graph->GetBarMinMax();
        $t_now = date( config_get( 'short_date_format' ) );
        if ( $t_now >= graph_date_format( $t_graph_offset_min ) && $t_now <= graph_date_format( $t_graph_offset_max ) ){
            $t_today = new GanttVLine( $t_now , "Today" , "darkred", 2, "solid");
            $t_today->SetDayOffset(0.5);
            $graph->add( $t_today );
        }
    
//       $t_today = new GanttVLine( "2011-03-01" , "" , "darkred", 2, "solid");//
//       $t_today->SetDayOffset(0.5);
//       $graph->add( $t_today );
  
        $t_gantt_chart_height = gantt_chart_get_height( $graph );
        $t_legend = gantt_chart_legend( false );
        $t_legend_height = 60;
    
        // Display the Gantt chart
    //     $graph->Stroke();
        //--------------------------------------
        // Create a combined graph
        //--------------------------------------
        $mgraph = new MGraph();
        $mgraph->Add($graph, 0, 0);
        $mgraph->Add($t_legend, 0, $t_gantt_chart_height + $t_legend_height );
        $mgraph->Stroke();
    }
}

function gantt_simple_example(){
$data = array(
  array(0,ACTYPE_GROUP,    "Phase 1",        "2001-10-26","2001-11-23",""),
  array(1,ACTYPE_NORMAL,   "  Label 2",      "2001-10-26","2001-11-13","[KJ]"),
  array(2,ACTYPE_NORMAL,   "  Label 3",      "2001-11-20","2001-11-22","[EP]"),
  array(3,ACTYPE_MILESTONE,"  Phase 1 Done", "2001-11-23","M2") );

// Create the basic graph
$graph = new GanttGraph();
$graph->title->Set("Gantt Graph using CreateSimple()");

// Setup scale
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

// Add the specified activities
$graph->CreateSimple($data);

// .. and stroke the graph
$graph->Stroke(); 
}

function gantt_simple_example2( $p_metrics, $p_title = "", $p_subtitle = "" ){
// $data = array(
//   array(0,ACTYPE_GROUP,    "Phase 1",        "2001-10-26","2001-11-23",""),
//   array(1,ACTYPE_NORMAL,   "  Label 2",      "2001-10-26","2001-11-13","[KJ]"),
//   array(2,ACTYPE_NORMAL,   "  Label 3",      "2001-11-20","2001-11-22","[EP]"),
//   array(3,ACTYPE_MILESTONE,"  Phase 1 Done", "2001-11-23","M2") );

// Create the basic graph
$graph = new GanttGraph();
$graph->title->Set($p_title);
$graph->subtitle-> Set("($p_subtitle)");

// Setup scale
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

// Add the specified activities
$graph->CreateSimple($p_metrics);

// .. and stroke the graph
$graph->Stroke(); 
}


function gantt_create_user_chart_title( $p_user_id ){
  return user_get_name( $p_user_id );
}

function gantt_create_user_chart_subtitle( $p_user_id, $p_project_id = null, $p_version_id = null, $p_inherited = true ){
  $t_subtitle = '';
  
  if ( $p_project_id != null ){
    $t_subtitle .= project_get_name( $p_project_id );
    
    if ( $p_version_id != null ){
      $t_subtitle .= " - " . version_full_name( $p_version_id, /* showProject */ $p_inherited, $p_project_id );
    }
    
  }
  
  return $t_subtitle;
}

# --------------------
# Data Extractions
# --------------------

function gantt_count_summary( $p_project_id, $p_version_name ){
    return count( gantt_get_issues_and_related_in_version( $p_project_id, $p_version_name ) );
}

function gantt_get_issues_and_related_in_version( $p_project_id, $p_version_name ){
    $t_bug_table = db_get_table( 'bug' );
    $t_relation_table = db_get_table( 'bug_relationship' );
    $t_bug_datas = array();
    
    $t_project_id = db_prepare_int( $p_project_id );
    $t_version_name = db_prepare_string( $p_version_name );
    
    $t_can_view_private = access_has_project_level( config_get( 'private_bug_threshold' ), $t_project_id );
    $t_limit_reporters = config_get( 'limit_reporters' );
    $t_user_access_level_is_reporter = ( REPORTER == access_get_project_level( $t_project_id ) );
    $t_user_id = auth_get_current_user_id();

    $query = "SELECT sbt.*, $t_relation_table.source_bug_id as parent_issue, dbt.target_version as parent_version FROM $t_bug_table AS sbt
              LEFT JOIN $t_relation_table ON sbt.id=$t_relation_table.destination_bug_id AND $t_relation_table.relationship_type=2
              LEFT JOIN $t_bug_table AS dbt ON dbt.id=$t_relation_table.source_bug_id
              WHERE sbt.project_id=" . db_param() . " AND sbt.target_version=" . db_param() . " ORDER BY sbt.status ASC, sbt.last_updated DESC";
    $t_result = db_query_bound( $query, array( $t_project_id, $t_version_name ) );

    // Filter ids according to level access
    while ( $t_row = db_fetch_array( $t_result ) ) {
        # hide private bugs if user doesn't have access to view them.
        if ( !$t_can_view_private && ( $t_row['view_state'] == VS_PRIVATE ) ) {
            continue;
        }

        bug_cache_database_result( $t_row );

        # check limit_Reporter (Issue #4770)
        # reporters can view just issues they reported
        if ( ON === $t_limit_reporters && $t_user_access_level_is_reporter &&
             !bug_is_user_reporter( $t_row['id'], $t_user_id )) {
            continue;
        }

        $t_issue_id = $t_row['id'];
        if ( !helper_call_custom_function( 'roadmap_include_issue', array( $t_issue_id ) ) ) {
            continue;
        }

        if( !isset( $t_bug_datas[$t_issue_id] ) ){
            $t_bug_datas[$t_issue_id] = $t_row;
        }
    }
    return $t_bug_datas;
}

# --------------------
# Gives the durations of all the issues concerned by a couple project/version:
function gantt_create_summary( $p_project_id, $p_version_id, $p_inherited, $p_start_index = -1, $p_length = null ){
    $t_metrics = array();
    $t_dates_arr = array();
    $t_i = 0;
    $t_version_name = version_full_name( $p_version_id, /* showProject */ $p_inherited, $p_project_id );
    $t_bug_datas = gantt_get_issues_and_related_in_version( $p_project_id, $t_version_name );
    $t_issue_ids = array();
    $t_issue_parents = array();
    
    // If there is only a part of the range to deal with, slice the array:
    if ( $p_start_index != -1 && $p_length != null ){
        $t_bug_datas = array_slice( $t_bug_datas, $p_start_index, $p_length, TRUE );
    }
 
    // Check whether the parent issue is in the current list of issue
    foreach ( $t_bug_datas as $t_issue_data ) {
        $t_issue_id = $t_issue_data['id'];
        $t_issue_parent = $t_issue_data['parent_issue'];
        $t_parent_version = $t_issue_data['parent_version'];
        
        if ( 0 === strcasecmp( $t_parent_version, $t_version_name ) ) {
            $t_issue_ids[] = $t_issue_id;
            // In case it is a partial slice of all the issues
            if ( key_exists( $t_issue_parent, $t_bug_datas ) ){
                $t_issue_parents[] = $t_issue_parent;
                $t_bug_datas[$t_issue_id]['parents'][] = $t_issue_parent;
            } else {
                $t_issue_parents[] = null;
            }
        } else if ( !in_array( $t_issue_id, $t_issue_ids ) ) {
            $t_issue_ids[] = $t_issue_id;
            $t_issue_parents[] = null;
        }
    }
  

    $t_issue_set_ids = array();
    $t_issue_set_levels = array();
    $k = 0;

    $t_cycle = false;
    $t_cycle_ids = array();

    // Set Parents and levels
    while ( 0 < count( $t_issue_ids ) ) {
        $t_issue_id = $t_issue_ids[$k];
        $t_issue_parent = $t_issue_parents[$k];

        if ( in_array( $t_issue_id, $t_cycle_ids ) && in_array( $t_issue_parent, $t_cycle_ids ) ) {
            $t_cycle = true;
        } else {
            $t_cycle = false;
            $t_cycle_ids[] = $t_issue_id;
        }

        if ( $t_cycle || !in_array( $t_issue_parent, $t_issue_ids ) ) {
            $l = array_search( $t_issue_parent, $t_issue_set_ids );
            if ( $l !== false ) {
                for ( $m = $l+1; $m < count( $t_issue_set_ids ) && $t_issue_set_levels[$m] > $t_issue_set_levels[$l]; $m++ ) {
                    #do nothing
                }
                $t_issue_set_ids_end = array_splice( $t_issue_set_ids, $m );
                $t_issue_set_levels_end = array_splice( $t_issue_set_levels, $m );
                $t_issue_set_ids[] = $t_issue_id;
                $t_issue_set_levels[] = $t_issue_set_levels[$l] + 1;
                $t_issue_set_ids = array_merge( $t_issue_set_ids, $t_issue_set_ids_end );
                $t_issue_set_levels = array_merge( $t_issue_set_levels, $t_issue_set_levels_end );
            } else {
                $t_issue_set_ids[] = $t_issue_id;
                $t_issue_set_levels[] = 0;
            }
            array_splice( $t_issue_ids, $k, 1 );
            array_splice( $t_issue_parents, $k, 1 );

            $t_cycle_ids = array();
        } else {
            $k++;
        }
        if ( count( $t_issue_ids ) <= $k ) {
            $k = 0;
        }
    }

    $t_constraint = array();
    $t_constraint_parent = array();

    $t_count_ids = count( $t_issue_set_ids );
    for ( $j = 0; $j < $t_count_ids; $j++ ) {
        $t_issue_set_id = $t_issue_set_ids[$j];
        $t_issue_set_level = $t_issue_set_levels[$j];

    //     helper_call_custom_function( 'roadmap_print_issue', array( $t_issue_set_id, $t_issue_set_level ) );
        $t_bug_id = $t_bug_datas[$t_issue_set_id]['id'];
        $t_title_id = $t_bug_datas[$t_issue_set_id]['summary'];
        $t_handler_id = $t_bug_datas[$t_issue_set_id]['handler_id'];
        $t_extra = "";
        $t_actype = ACTYPE_NORMAL;
        $t_start_date = gantt_get_start_date( $t_bug_id );
        $t_due_date = gantt_get_due_date( $t_bug_id, $t_start_date );

        if ( null == gantt_get_resolution_date( $t_bug_id )
             && null != gantt_get_assigned_date( $t_bug_id ) ) {
            $t_extra = " (" . user_get_name( $t_handler_id ) . ")";
        }

        if ( null == $t_due_date ) {
            $t_actype = ACTYPE_MILESTONE;
        }
    
        $t_constraint_parent[$t_issue_set_id] = $j;
    
        if ( isset ( $t_bug_datas[$t_issue_set_id]['parents'] ) ){
            for ( $o = 0; $o < count( $t_bug_datas[$t_issue_set_id]['parents'] ); $o++){
                $t_constraint[$t_issue_set_id][] = array(
                    'parent' => $t_bug_datas[$t_issue_set_id]['parents'][$o],
                    'type'   => CONSTRAIN_ENDEND
                );
            }
        } else {
//           echo 'DEBUG:1- constraint_parent(<pre>';print_r($t_constraint_parent);echo ')</pre><br/>'."\n";
        }
    
    
        $t_issue_constraint = array();
        if ( isset( $t_constraint[$t_issue_set_id] ) ){
          foreach ( $t_constraint[$t_issue_set_id] as $t_my_constraint ){
            $t_issue_constraint[] = array(
              'row'  => $t_constraint_parent[$t_my_constraint['parent']],
              'type' => CONSTRAIN_ENDEND
            );
          }
        }
        
        switch ( $t_actype ) {
            case ACTYPE_NORMAL:
                $t_dates_arr[] = $t_start_date;
                $t_dates_arr[] = $t_due_date;
                $t_metrics[] = array( $j, $t_actype, $t_bug_id, $t_start_date, $t_due_date, $t_extra, $t_issue_set_level, $t_issue_constraint );
                break;
            case ACTYPE_MILESTONE:
                $t_dates_arr[] = $t_start_date;
                $t_metrics[] = array( $j, $t_actype, $t_bug_id, $t_start_date, null, $t_extra, $t_issue_set_level, $t_issue_constraint );
                break;
        }
//        $t_i++;
    }
    
//  plugin_config_set( 'DEBUG_plugin', '
//    ### Min(date,issue) = ('. graph_date_format( $t_min_creation_date ) . ',' . $t_min_creation_date_issue . ')
//    ### Max(date,issue) = ('. graph_date_format( $t_max_creation_date ) .','.$t_max_creation_date_issue.')
//    ### Min - Max = ('. ( $t_max_creation_date - $t_min_creation_date ) / 3600/24/7 .' Weeks)
//    ### Limit allowed = ('. plugin_config_get( 'weeks_max' ) .' Weeks)
//    ### DEBUG: ' . $t_debug );
    
    $t_dates_range = gantt_graph_best_dates_range( $t_dates_arr );
    return array(
      'metrics' => $t_metrics,
      'range' => $t_dates_range
    );
}

# --------------------
# Gives the durations of all the issues assigned to a user:
function gantt_create_summary_by_user( $p_user_id, $p_project_id, $p_version_id, $p_inherited ){
    $t_bug_table = db_get_table( 'mantis_bug_table' );
  
    $t_project_id = db_prepare_int( $p_project_id );
    $t_user_id = db_prepare_int( $p_user_id );
    $t_version_name = version_full_name( $p_version_id, /* showProject */ $p_inherited, $t_project_id );
    $t_version_name = db_prepare_string( $t_version_name );
    
    $query = "SELECT id, summary, date_submitted, handler_id, due_date
               FROM $t_bug_table
               WHERE handler_id='" . db_param() . "'";
               
    if ( null != $p_project_id && ALL_PROJECTS != $p_project_id ) {
        $query .= " AND target_version='" . db_param() . "'";
        $result = db_query_bound( $query, array( $t_user_id, $t_version_name ) );
    } else {
        $result = db_query_bound( $query, array( $t_user_id ) );
    }
    
    while( $row = db_fetch_array( $result ) ) {
        $t_results[] = $row;
    }
    
    
    $t_metrics = array();
    $t_dates_arr = array();
    $t_i = 0;
    
    foreach ($t_results as $t_bug_data) {
      $t_bug_id = $t_bug_data['id'];
      $t_title_id = $t_bug_data['summary'];
      $t_creation_date = $t_bug_data['date_submitted'];
      $t_handler_id = $t_bug_data['handler_id'];
      $t_extra = "";
      $t_actype = ACTYPE_NORMAL;
      $t_start_date = gantt_get_start_date( $t_bug_id );
      $t_due_date = gantt_get_due_date( $t_bug_id, $t_start_date );
      
      if ( null == gantt_get_resolution_date( $t_bug_id )
           && null != gantt_get_assigned_date( $t_bug_id ) ) {
          $t_extra = " (" . user_get_name( $t_handler_id ) . ")";
      }
  
      if ( null == $t_due_date ) {
          $t_actype = ACTYPE_MILESTONE;
      }
      
  
      switch ( $t_actype ) {
          case ACTYPE_NORMAL:
              $t_dates_arr[] = $t_start_date;
              $t_dates_arr[] = $t_due_date;
              $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, $t_start_date, $t_due_date, $t_extra );
              break;
          case ACTYPE_MILESTONE:
              $t_dates_arr[] = $t_start_date;
              $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, $t_start_date, null, $t_extra );
              break;
      }
      $t_i++;
    }
    $t_dates_range = gantt_graph_best_dates_range( $t_dates_arr );
    return array(
        'metrics' => $t_metrics,
        'range' => $t_dates_range
    );
}


# --------------------
# Gives the durations of all the issues provided by the filter:
function gantt_create_summary_from_bug_list( $p_bugslist ){
    $t_metrics = array();
    $t_dates_arr = array();

    if ( $p_bugslist ) {
        $t_bugslist = explode( ',', $p_bugslist );
        $t_i = 0;
        
        // The list comes from the filter, which is already filtered with authorized access
        // Besides, we only display the gantt chart without relationship and levels, as it is in the filter result in fact.
        $t_issue_set_level = 0; // Always level 0
        $t_issue_constraint = array(); // No constraint
        foreach ( $t_bugslist as $t_bug_id ) {
            $t_title_id = bug_get_field( $t_bug_id, 'summary' );
            $t_handler_id = bug_get_field( $t_bug_id, 'handler_id' );
            $t_extra = "";
            $t_actype = ACTYPE_NORMAL;
            $t_start_date = gantt_get_start_date( $t_bug_id );
            $t_due_date = gantt_get_due_date( $t_bug_id, $t_start_date );

            if ( null == gantt_get_resolution_date( $t_bug_id )
                 && null != gantt_get_assigned_date( $t_bug_id ) ) {
                $t_extra = " (" . user_get_name( $t_handler_id ) . ")";
            }
    
            if ( null == $t_due_date ) {
                $t_actype = ACTYPE_MILESTONE;
            }
            
            switch ( $t_actype ) {
                case ACTYPE_NORMAL:
                    $t_dates_arr[] = $t_start_date;
                    $t_dates_arr[] = $t_due_date;
                    $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, $t_start_date, $t_due_date, $t_extra, $t_issue_set_level, $t_issue_constraint );
                    break;
                case ACTYPE_MILESTONE:
                    $t_dates_arr[] = $t_start_date;
                    $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, $t_start_date, null, $t_extra, $t_issue_set_level, $t_issue_constraint );
                    break;
            }
            $t_i++;
        }
    }

    $t_dates_range = gantt_graph_best_dates_range( $t_dates_arr );
    return array(
        'metrics' => $t_metrics,
        'range' => $t_dates_range
    );
}



# --------------------
# Gives the resolution date of an issue:
function gantt_get_resolution_date( $p_bug_id ){
  if ( bug_is_resolved( $p_bug_id ) ) {
    $t_history = history_get_raw_events_array( $p_bug_id );
    $t_resolution_date = null;
    foreach ( $t_history as $t_item ) {
      if ( 'status' == $t_item['field'] ) {
        if ( $t_item['new_value'] >= config_get( 'bug_resolved_status_threshold' ) ) {
          $t_resolution_date = $t_item['date'];
        }
      }
    }
    return $t_resolution_date;
  }
  // The issue is not resolved yet
  return null;
}

# --------------------
# Gives the end_date of an issue according to the plugin settings:
function gantt_get_assigned_date( $p_bug_id ){
  //TRY TO GET THE ASSIGNMENT DATE
  $t_history = history_get_raw_events_array( $p_bug_id );
  foreach ( $t_history as $t_item ) {
    if ( 'handler_id' == $t_item['field'] ) {
      // Use the date of the first assignment date if any found:
      return $t_item['date'];
    }
  }
  //END OF GETTING ASSIGNMENT DATE
 return null;
}


# --------------------
# Gives the due date of an issue according to the plugin settings:
function gantt_get_due_date( $p_bug_id, $p_start_date ){
    $t_due_date = gantt_get_resolution_date( $p_bug_id );
    if ( null == $t_due_date ) {
      //The issue is still opened:
      $t_duration_in_seconds = gantt_get_duration_in_seconds($p_bug_id);
      
      //GET THE END DATE:
      if ( null != gantt_get_assigned_date( $p_bug_id ) ) {
        $t_due_date = gantt_get_end_date( $p_bug_id, $p_start_date, $t_duration_in_seconds );
      } else {
        $t_creation_date = bug_get_field( $p_bug_id, 'date_submitted' );
        $t_due_date = gantt_get_end_date( $p_bug_id, $t_creation_date, $t_duration_in_seconds );
      }
      
    }
    
    return $t_due_date;
}

# --------------------
# Gives the duration in seconds according to the plugin settings:
function gantt_get_duration_in_seconds( $p_bug_id ){
    $t_duration_in_seconds = null;
    if ( OFF == plugin_config_get( 'use_due_date_field' ) ) {
        // i.e. Custom field is used:
        //TRY TO GET THE DURATION
        $t_field_id = plugin_config_get( 'custom_field_id_for_duration' ); // Duration
        $t_duration = custom_field_get_value( $t_field_id, $p_bug_id );

        if( !is_blank( $t_duration ) ){
            preg_match( '/(?P<duration>\d+)(?P<unit>[dh]?)/', $t_duration, $t_matches );
            // get the unit
            if( !isset( $t_matches['unit'] ) ){
                $t_unit = plugin_config_get( 'default_duration_unit' );
            } else {
                $t_unit = $t_matches['unit'];
            }
            // get the value
            $t_value = $t_matches['duration'];
            // convert it in seconds:
            switch ( $t_unit ) {
                case 'h':
                    // transform the current duration in terms of working days for the gantt chart,
                    // as the smallest unit for gantt chart is day
                    $t_working_hours_in_a_day = plugin_config_get( 'working_hours_in_a_day' );
                    $t_gantt_chart_days = ceil( $t_value / $t_working_hours_in_a_day );
                    $t_duration_in_seconds = $t_gantt_chart_days * 24 * 3600;
                    break;
                case 'd':
                default:
                    $t_duration_in_seconds = $t_value * 24 * 3600;
                    break;
            }
        }
    }

    //END OF GETTING DAYS FOR CORRECTION
    return $t_duration_in_seconds;
}

# --------------------
# Gives the end_date of an issue according to the plugin settings:
function gantt_get_start_date( $p_bug_id ){
  $t_assignment_date = gantt_get_assigned_date( $p_bug_id );
  $t_creation_date = bug_get_field( $p_bug_id, 'date_submitted' );
  
  if ( plugin_config_get ( 'use_start_date_field' ) && ( plugin_config_get ( 'custom_field_id_for_start_date' ) > 0 ) ){
    $t_custom_start_date = custom_field_get_value( plugin_config_get ( 'custom_field_id_for_start_date' ), $p_bug_id );
    if ( !is_blank( $t_custom_start_date ) ){
      //Start date has been set for the issue: we can use its value.
      //We don't care if the Start Date is greater than the creation date or not!
      $t_start_date = $t_custom_start_date;
    } else {
      //Start date has not been filled yet. Use the assignement date instead.
      if ( null == $t_assignment_date ) {
        $t_start_date = $t_creation_date;
      } else {
        $t_start_date = $t_assignment_date;
      }
    }
  } else {
    //use only the assignment date
    if ( null == $t_assignment_date ) {
      $t_start_date = $t_creation_date;
    } else {
      $t_start_date = $t_assignment_date;
    }
  }
  
  return gantt_adjust_working_day( $t_start_date );
}

# --------------------
# Gives the end_date of an issue according to the plugin settings:
function gantt_get_end_date( $p_bug_id, $p_start_date, $p_duration ){
  if ( null != gantt_get_assigned_date( $p_bug_id ) ) {
    //COMPUTING THE DUE DATE:
    if ( OFF == plugin_config_get( 'use_due_date_field' ) ) {
      // i.e. Custom field is used:
      if ( !is_blank( $p_duration ) ) {
        return gantt_adjust_working_day( ( $p_start_date + $p_duration ) );
      }
    } else {
      // due_date field is used:
      if ( 1 != bug_get_field( $p_bug_id, 'due_date' ) )
        return gantt_adjust_working_day( bug_get_field( $p_bug_id, 'due_date' ) );
    }
  }
  // Return null if end date is not defined yet
  return null;
}


# --------------------
# Adjust the given date to be a working day.
# If the date is during the week end, adjust it the next work day
# 0==Sun, 1==Monday, 2==Tuesday etc
function gantt_adjust_working_day( $p_date ){
//   define ( 'GANTT_CHART_DAY_SUNDAY', 0);
//   define ( 'GANTT_CHART_DAY_SATURDAY', 6);
  $t_one_day_in_seconds = 1 * 24 * 3600;
  $t_day = strftime( "%w", $p_date );
  
  switch ( $t_day ){
//     case GANTT_CHART_DAY_SUNDAY:
    case 0:
      $t_adjusted_days = 1;
      break;
//     case GANTT_CHART_DAY_SATURDAY:
    case 6:
      $t_adjusted_days = 2;
      break;
    default:
      $t_adjusted_days = 0;
      break;
  }
  
  return $p_date + ($t_adjusted_days * $t_one_day_in_seconds);
}



function gantt_utils_update_max_in_array( $p_array, $p_key, $p_new_max_value ){
  if ( 0 == count( $p_array ) ){
    // It is the first value to populate
    $p_array[$p_key] = $p_new_max_value;
  } else {
    // We just need the first entry to compare:
    $t_key_arr = array_keys( $p_array );
    if( $p_array[$t_key_arr[0]] == $p_new_max_value ){
      // if new max is equal we add a new entry
      $p_array[$p_key] = $p_new_max_value;
    } else if( $p_array[$t_key_arr[0]] < $p_new_max_value ){
      // if new max is greater we first remove the previous one,
      unset( $p_array );
      $p_array = array();
      // then add the new one
      $p_array[$p_key] = $p_new_max_value;
    } else {
      // if new max is lower, then we do nothing
    }
  }  
  return $p_array;
}

function gantt_utils_get_closest_value( $p_value, $p_array ) {
   $t_closest_value = null;
   foreach( $p_array as $t_value ) {
      if( $t_closest_value == null || abs( $p_value - $t_closest_value ) > abs( $t_value - $p_value ) ) {
         $t_closest_value = $t_value;
      }
   }
   return $t_closest_value;
}

function gantt_utils_get_values_in_range( $p_array, $p_min, $p_max ){
    $t_min_value = gantt_utils_get_closest_value( $p_min, $p_array );
    $t_max_value = gantt_utils_get_closest_value( $p_max, $p_array );
    $t_min_key = array_search( $t_min_value, $p_array );
    $t_max_key = array_search( $t_max_value, $p_array );
    
    return array_slice( $p_array, $t_min_key, $t_max_key - $t_min_key + 1  );
}


# --------------------
# Limit the graph to a range of date, in case the dates are too far away form
#  each other.
function gantt_graph_best_dates_range( $p_dates_arr ){
    $t_dates_range = array();
    sort($p_dates_arr);
    
    $t_min_max_dates = array(
      'min' => null,
      'max' => null
    );
    $t_compute_average = array();
    $t_heaviest_year_arr = array();
    $t_selected_year = null;
    $t_heaviest_week_in_year_arr = array();
    $t_selected_week = null;
    $t_selected_date = null;
    $t_heaviest_range_arr = array();
    $t_ranges_arr = array();
    
    $t_60s = 60; // 1 minute
    $t_60min = 60; // 1 hour
    $t_24h = 24; // 1 day
    $t_7d = 7; // 1 week
    $t_minute = $t_60s;
    $t_hour = $t_60min * $t_minute;
    $t_day = $t_24h * $t_hour;
    $t_week = $t_7d * $t_day;
    
    foreach ( $p_dates_arr as $t_date ) {
        $t_year = strftime( "%Y", $t_date );
        if( !isset($t_compute_average[$t_year]) ) $t_compute_average[$t_year]=array(
            'count' => 0,
            'weeks' => array()
        );
        $t_compute_average[$t_year]['count']++;
        $t_week_nb = strftime( "%W", $t_date );
        if( !isset($t_compute_average[$t_year]['weeks'][$t_week_nb]) ) $t_compute_average[$t_year]['weeks'][$t_week_nb]=array(
            'count' => 0,
            'dates' => array()
        );
        $t_compute_average[$t_year]['weeks'][$t_week_nb]['count']++;
        $t_compute_average[$t_year]['weeks'][$t_week_nb]['dates'][] = $t_date;
        if ( !isset( $t_min_max_dates['min'] ) ) $t_min_max_dates['min'] = $t_date;
        if ( !isset( $t_min_max_dates['max'] ) ) $t_min_max_dates['max'] = $t_date;
        if ( $t_min_max_dates['max'] < $t_date ) $t_min_max_dates['max'] = $t_date;
        if ( $t_min_max_dates['min'] > $t_date ) $t_min_max_dates['min'] = $t_date;
    }
    
    // It is possible that several years have the same number of dates, so we search for possible years
    foreach ( $t_compute_average as $t_year => $t_year_dates ) {
        $t_heaviest_year_arr = gantt_utils_update_max_in_array( $t_heaviest_year_arr, $t_year, $t_year_dates['count'] );
    }
    
    if ( count( $t_heaviest_year_arr ) ){
        // in case several years have the same number of dates, choose the best one
        #1- Calculate the weighted average:
        $t_year_weighted_sum = 0;
        $t_year_sum_of_weight = 0;
        foreach( $t_compute_average as $t_year => $t_year_dates ){
            $t_year_weighted_sum += $t_year*$t_year_dates['count'];
            $t_year_sum_of_weight += $t_year_dates['count'];
        }
        $t_year_average = ( 0 != $t_year_sum_of_weight) ? ( $t_year_weighted_sum / $t_year_sum_of_weight ) : null;
        $t_selected_year = gantt_utils_get_closest_value( $t_year_average, array_keys( $t_heaviest_year_arr ) );
  
        // Now get the heaviest week of the selected year:
        $t_year_dates = $t_compute_average[$t_selected_year]['weeks'];
        foreach ( $t_year_dates as $t_week_nb => $t_week_dates ) {
            $t_heaviest_week_in_year_arr = gantt_utils_update_max_in_array( $t_heaviest_week_in_year_arr, $t_week_nb, $t_week_dates['count'] );
        }
        if ( count( $t_heaviest_week_in_year_arr ) ){

            #1- Calculate the weighted average:
            $t_week_weighted_sum = 0;
            $t_week_sum_of_weight = 0;
            foreach( $t_year_dates as $t_week_nb => $t_week_dates ){
                $t_week_weighted_sum += $t_week_nb*$t_week_dates['count'];
                $t_week_sum_of_weight += $t_week_dates['count'];
            }
            $t_week_average = ( 0 != $t_week_sum_of_weight) ? ( $t_week_weighted_sum / $t_week_sum_of_weight ) : null;
            $t_selected_week = gantt_utils_get_closest_value( $t_week_average, array_keys( $t_heaviest_week_in_year_arr ) );

            #1- Calculate the weighted average:
            $t_date_weighted_sum = 0;
            foreach( $t_compute_average[$t_selected_year]['weeks'][$t_selected_week]['dates'] as $t_date ){
                $t_date_weighted_sum += $t_date;
            }
            $t_date_sum_of_weight = count( $t_compute_average[$t_selected_year]['weeks'][$t_selected_week]['dates'] );
            $t_date_average = ( 0 != $t_date_sum_of_weight) ? ( $t_date_weighted_sum / $t_date_sum_of_weight ) : null;
            $t_selected_date = gantt_utils_get_closest_value( $t_date_average, array_values( $t_compute_average[$t_selected_year]['weeks'][$t_selected_week]['dates'] ) );


        } else {
            return false;
        } 
  
        
    } else {
        return false;
    }
    
    
    // Now we display only 80 Weeks around this average week.
    // We do not just do 40w|40w, but do it a little more clever
    // We need min max dates for that

    $t_max_weeks_allowed = plugin_config_get( 'weeks_max' );
    $t_max_weeks_allowed_in_seconds = $t_max_weeks_allowed * $t_week ;

    // Select the best range around the average selected date:
    $t_min_boundary = max( $t_min_max_dates['min'], ( $t_selected_date - $t_max_weeks_allowed_in_seconds ) );
    $t_max_boundary = min( $t_min_max_dates['max'], ( $t_selected_date + $t_max_weeks_allowed_in_seconds ) );

    # We are now going to create a weigthed array of intervals of 80 weeks
    $t_range_duration = min( $t_max_weeks_allowed, ($t_max_boundary - $t_min_boundary)/$t_week);
    $t_max_date = $t_min_boundary;
    $t_i = 0;
    for ( $t_min_date = $t_min_boundary; $t_max_date <= $t_max_boundary; $t_min_date += $t_week ){
        $t_max_date = $t_min_date + $t_range_duration*$t_week;
        $t_range_info = array(
            'min' => $t_min_date,
            'max' => $t_max_date,
            'count' => count( gantt_utils_get_values_in_range( $p_dates_arr, $t_min_date, $t_max_date ) )
        );
        $t_ranges_arr[$t_i] = $t_range_info;
        $t_heaviest_range_arr = gantt_utils_update_max_in_array( $t_heaviest_range_arr, $t_i, $t_range_info['count'] );
        $t_i++;
    }

    #1- Calculate the weighted average:
    $t_range_weighted_sum = 0;
    $t_range_sum_of_weight = 0;
    foreach( $t_ranges_arr as $t_range => $t_range_dates ){
      $t_range_weighted_sum += $t_range * $t_range_dates['count'];
      $t_range_sum_of_weight += $t_range_dates['count'];
    }
    $t_range_average = ( 0 != $t_range_sum_of_weight) ? ( $t_range_weighted_sum / $t_range_sum_of_weight ) : null;
    $t_selected_range = gantt_utils_get_closest_value( $t_range_average, array_keys( $t_heaviest_range_arr ) );
   
    $t_dates_range['min'] = $t_ranges_arr[$t_selected_range]['min'];
    $t_dates_range['max'] = $t_ranges_arr[$t_selected_range]['max'];
    
    return $t_dates_range;
}
