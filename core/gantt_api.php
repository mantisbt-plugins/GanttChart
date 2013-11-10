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
require_once( 'graph_api.php' );
require_once( 'version_api.php' );
require_once( 'history_api.php' );

 
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
    require_once( 'jpgraph/jpgraph_gantt.php' );
    require_once( 'jpgraph/jpgraph_mgraph.php' );
  }
} else {
  require_once( 'ezc/Base/src/base.php' );
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
  foreach ( $p_metrics as $t_metric_row ) {
    switch ( $t_metric_row[1] ) {
      case ACTYPE_NORMAL:
        $t_activity = new GanttBar( $t_metric_row[0], bug_format_summary( $t_metric_row[2], SUMMARY_FIELD ), $t_metric_row[3], $t_metric_row[4], " $t_metric_row[2]" . $t_metric_row[5] );
?>
  <tr>
    <td><?php echo $t_metric_row[0];?></td>
    <td><?php echo bug_format_summary( $t_metric_row[2], SUMMARY_FIELD );?></td>
    <td><?php echo $t_metric_row[3];?></td>
    <td><?php echo $t_metric_row[4];?></td>
    <td><?php echo " $t_metric_row[2]" . $t_metric_row[5];?></td>
  </tr>
<?php
        break;
      case ACTYPE_MILESTONE:
        $t_milestone = new MileStone( $t_metric_row[0], bug_format_summary( $t_metric_row[2], SUMMARY_FIELD ), $t_metric_row[3], " $t_metric_row[2] (not started)" . $t_metric_row[5] );
?>
  <tr>
    <td><?php echo $t_metric_row[0];?></td>
    <td><?php echo bug_format_summary( $t_metric_row[2], SUMMARY_FIELD );?></td>
    <td><?php echo $t_metric_row[3];?></td>
    <td><?php echo "";?></td>
    <td><?php echo " $t_metric_row[2] (not started)" . $t_metric_row[5];?></td>
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
//   $graph->title->Set("Legend");
  
  if ( $p_show ){
    // Display the graph
    $graph->Stroke();
  } else {
    // Return the graph
    return $graph;
  }
}

function gantt_chart_get_height( $p_gantt_chart ){
  
  
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
  
	$t_gantt_chart_max_rows = plugin_config_get( 'rows_max' );
  error_check( is_array( $p_metrics ) ? count( $p_metrics ) : 0, $p_title . " (" . $p_subtitle . ")" );
  
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
  
  
  

  foreach ( $p_metrics as $t_metric_row ) {
    switch ( $t_metric_row[1] ) {
      case ACTYPE_NORMAL:
        $t_activity = new GanttBar( $t_metric_row[0] % $t_gantt_chart_max_rows, bug_format_summary( $t_metric_row[2], SUMMARY_FIELD ), $t_metric_row[3], $t_metric_row[4], " $t_metric_row[2]" . $t_metric_row[5] );
        if ( null != gantt_get_resolution_date( $t_metric_row[2] ) ){
          $t_activity->SetPattern( BAND_RDIAG, get_status_color( bug_get_field( $t_metric_row[2], 'status' ) ) );
        }
        $t_activity->SetFillColor( get_status_color( bug_get_field( $t_metric_row[2], 'status' ) ) );
//         $t_activity->SetCSIMTarget( string_get_bug_view_url( $t_metric_row[2] ), bug_format_summary( $t_metric_row[2], SUMMARY_FIELD ) );
//         $t_activity->title->SetCSIMTarget( string_get_bug_view_url( $t_metric_row[2] ), bug_format_summary( $t_metric_row[2], SUMMARY_FIELD ) );
        
        $graph->add( $t_activity );
        break;
      case ACTYPE_MILESTONE:
        $t_milestone = new MileStone( $t_metric_row[0] % $t_gantt_chart_max_rows, bug_format_summary( $t_metric_row[2], SUMMARY_FIELD ), $t_metric_row[3], " $t_metric_row[2] (not started)" . $t_metric_row[5] );
        $t_milestone->mark->SetType( MARK_FILLEDCIRCLE );
        $t_milestone->mark->SetWidth( 5 );
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
    $t_graph_offset_max = $t_minmax[1] + (2 * $t_week_in_seconds);
    $graph->SetDateRange( graph_date_format( $t_graph_offset_min ), graph_date_format( $t_graph_offset_max ) );
    
    // Add a vertical line for today if in the range of GetBarMinMax() (retruns an arry ($min, $max) ):
    $t_minmax = $graph->GetBarMinMax();
    $t_now = date( config_get( 'short_date_format' ) );
    if ( $t_now >= graph_date_format( $t_graph_offset_min ) && $t_now <= graph_date_format( $t_graph_offset_max ) ){
      $t_today = new GanttVLine( $t_now , "Today" , "darkred", 2, "solid");
      $t_today->SetDayOffset(0.5);
      $graph->add( $t_today );
    }
    
//     $t_today = new GanttVLine( "2011-03-01" , "" , "darkred", 2, "solid");//
//     $t_today->SetDayOffset(0.5);
//     $graph->add( $t_today );
  
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

  $t_bug_table = db_get_table( 'mantis_bug_table' );

  $t_project_id = db_prepare_int( $p_project_id );
  $t_version_name = db_prepare_string( $p_version_name );
  $query = "SELECT COUNT(*)
             FROM $t_bug_table
             WHERE project_id=" . $t_project_id . " AND 
                  target_version='$t_version_name'";
                  
  $result = db_query( $query );
  return db_result( $result );
}


# --------------------
# Gives the durations of all the issues concerned by a couple project/version:
function gantt_create_summary( $p_project_id, $p_version_id, $p_inherited ){

  $t_bug_table = db_get_table( 'mantis_bug_table' );
  $t_metrics = array();
  $t_results = array();
  $t_i = 0;

  $t_project_id = db_prepare_int( $p_project_id );
  $t_version_name = version_full_name( $p_version_id, /* showProject */ $p_inherited, $t_project_id );
  $t_version_name = db_prepare_string( $t_version_name );
  $query = "SELECT id, summary, date_submitted, handler_id, due_date
             FROM $t_bug_table
             WHERE project_id=" . $t_project_id . " AND 
                  target_version='$t_version_name'
             ORDER BY priority DESC, severity DESC, status DESC, last_updated DESC";
                  
  $result = db_query( $query );
  while( $row = db_fetch_array( $result ) ) {
    $t_results[] = $row;
  }
  
  
  
  foreach ($t_results as $t_bug_data) {
    $t_bug_id = $t_bug_data['id'];
    $t_title_id = $t_bug_data['summary'];
    $t_creation_date = $t_bug_data['date_submitted'];
    $t_handler_id = $t_bug_data['handler_id'];
    $t_extra = "";
    $t_start_date = gantt_get_start_date( $t_bug_id, $t_creation_date );
    $t_due_date = gantt_get_resolution_date( $t_bug_id );
    $t_actype = ACTYPE_NORMAL;
    $t_duration_in_seconds = null;
    
    if ( null == $t_due_date ) {
      //The issue is still opened:
      if ( OFF == plugin_config_get( 'use_due_date_field' ) ) {
        // i.e. Custom field is used:
        //TRY TO GET THE DURATION
        $t_field_id = plugin_config_get( 'custom_field_id_for_duration' ); // Duration
        $t_duration = custom_field_get_value( $t_field_id, $t_bug_id );
        if (!is_blank($t_duration)) {
          //convert days into seconds:
          $t_duration_in_seconds = $t_duration * 24 * 3600;
        }
        //END OF GETTING DAYS FOR CORRECTION
      }
      
      //GET THE END DATE:
      if ( null != gantt_get_assigned_date( $t_bug_id ) ) {
        $t_due_date = gantt_get_end_date( $t_bug_id, $t_start_date, $t_duration_in_seconds );
        $t_extra = " (" . user_get_name( $t_handler_id ) . ")";
      } else {
        $t_due_date = gantt_get_end_date( $t_bug_id, $t_creation_date, $t_duration_in_seconds );
      }
      
      if ( null == $t_due_date ) {
        $t_actype = ACTYPE_MILESTONE;
      }
    }

    switch ( $t_actype ) {
      case ACTYPE_NORMAL:
        $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, graph_date_format( $t_start_date ), graph_date_format( $t_due_date ), $t_extra );
        break;
      case ACTYPE_MILESTONE:
        $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, graph_date_format( $t_start_date ), null, $t_extra );
        break;
    }
    $t_i++;
  }
  return $t_metrics;
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
             WHERE handler_id='$t_user_id'";
  $t_and = '';
             
  if ( null != $p_project_id && ALL_PROJECTS != $p_project_id ) {
    $t_and .= " AND target_version='$t_version_name'";
  }
  
  $query .= $t_and;
                  
  $result = db_query( $query );
  while( $row = db_fetch_array( $result ) ) {
    $t_results[] = $row;
  }
  
  
  $t_metrics = array();
  $t_i = 0;
  
  foreach ($t_results as $t_bug_data) {
    $t_bug_id = $t_bug_data['id'];
    $t_title_id = $t_bug_data['summary'];
    $t_creation_date = $t_bug_data['date_submitted'];
    $t_handler_id = $t_bug_data['handler_id'];
    $t_extra = "";
    $t_start_date = gantt_get_start_date( $t_bug_id, $t_creation_date );
    $t_due_date = gantt_get_resolution_date( $t_bug_id );
    $t_actype = ACTYPE_NORMAL;
    $t_duration_in_seconds = null;
    
    if ( null == $t_due_date ) {
      //The issue is still opened:
      if ( OFF == plugin_config_get( 'use_due_date_field' ) ) {
        // i.e. Custom field is used:
        //TRY TO GET THE DURATION
        $t_field_id = plugin_config_get( 'custom_field_id_for_duration' ); // Duration
        $t_duration = custom_field_get_value( $t_field_id, $t_bug_id );
        if (!is_blank($t_duration)) {
          //convert days into seconds:
          $t_duration_in_seconds = $t_duration * 24 * 3600;
        }
        //END OF GETTING DAYS FOR CORRECTION
      }
      
      //GET THE END DATE:
      if ( null != gantt_get_assigned_date( $t_bug_id ) ) {
        $t_due_date = gantt_get_end_date( $t_bug_id, $t_start_date, $t_duration_in_seconds );
        $t_extra = " (" . user_get_name( $t_handler_id ) . ")";
      } else {
        $t_due_date = gantt_get_end_date( $t_bug_id, $t_creation_date, $t_duration_in_seconds );
      }
      
      if ( null == $t_due_date ) {
        $t_actype = ACTYPE_MILESTONE;
      }
    }

    switch ( $t_actype ) {
      case ACTYPE_NORMAL:
        $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, graph_date_format( $t_start_date ), graph_date_format( $t_due_date ), $t_extra );
        break;
      case ACTYPE_MILESTONE:
        $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, graph_date_format( $t_start_date ), null, $t_extra );
        break;
    }
    $t_i++;
  }
  return $t_metrics;
}


# --------------------
# Gives the durations of all the issues provided by the filter:
function gantt_create_summary_from_bug_list( $p_bugslist ){
  $t_metrics = array();
	if ( $p_bugslist ) {
		$t_bugslist = explode( ',', $p_bugslist );		
		
    $t_i = 0;
    
    foreach ( $t_bugslist as $t_bug_id ) {
      $t_title_id = bug_get_field( $t_bug_id, 'summary' );
      $t_creation_date = bug_get_field( $t_bug_id, 'date_submitted' );
      $t_handler_id = bug_get_field( $t_bug_id, 'handler_id' );
      $t_extra = "";
      $t_start_date = gantt_get_start_date( $t_bug_id, $t_creation_date );
      $t_due_date = gantt_get_resolution_date( $t_bug_id );
      $t_actype = ACTYPE_NORMAL;
      $t_duration_in_seconds = null;
      
      if ( null == $t_due_date ) {
        //The issue is still opened:
        
        if ( OFF == plugin_config_get( 'use_due_date_field' ) ) {
          // i.e. Custom field is used:
          //TRY TO GET THE DURATION
          $t_field_id = plugin_config_get( 'custom_field_id_for_duration' ); // Duration
          $t_duration = custom_field_get_value( $t_field_id, $t_bug_id );
          if (!is_blank($t_duration)) {
            //convert days into seconds:
            $t_duration_in_seconds = $t_duration * 24 * 3600;
          }
          //END OF GETTING DAYS FOR CORRECTION
        }
        
        //GET THE END DATE:
        if ( null != gantt_get_assigned_date( $t_bug_id ) ) {
          $t_due_date = gantt_get_end_date( $t_bug_id, $t_start_date, $t_duration_in_seconds );
          $t_extra = " (" . user_get_name( $t_handler_id ) . ")";
        } else {
          $t_due_date = gantt_get_end_date( $t_bug_id, $t_creation_date, $t_duration_in_seconds );
        }
        
        if ( null == $t_due_date ) {
          $t_actype = ACTYPE_MILESTONE;
        }
      }
  
      switch ( $t_actype ) {
        case ACTYPE_NORMAL:
          $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, graph_date_format( $t_start_date ), graph_date_format( $t_due_date ), $t_extra );
          break;
        case ACTYPE_MILESTONE:
          $t_metrics[] = array( $t_i, $t_actype, $t_bug_id, graph_date_format( $t_start_date ), null, $t_extra );
          break;
      }
      $t_i++;
    }
		
  }
  return $t_metrics;
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
# Gives the end_date of an issue according to the plugin settings:
function gantt_get_start_date( $p_bug_id, $p_creation_date ){
  $t_assignment_date = gantt_get_assigned_date( $p_bug_id );
  
  if ( plugin_config_get ( 'use_start_date_field' ) && ( plugin_config_get ( 'custom_field_id_for_start_date' ) > 0 ) ){
    $t_custom_start_date = custom_field_get_value( plugin_config_get ( 'custom_field_id_for_start_date' ), $p_bug_id );
    if ( !is_blank( $t_custom_start_date ) ){
      //Start date has been set for the issue: we can use its value.
      //We don't care if the Start Date is greater than the creation date or not!
      $t_start_date = $t_custom_start_date;
    } else {
      //Start date has not been filled yet. Use the assignement date instead.
      if ( null == $t_assignment_date ) {
        $t_start_date = $p_creation_date;
      } else {
        $t_start_date = $t_assignment_date;
      }
    }
  } else {
    //use only the assignment date
    if ( null == $t_assignment_date ) {
      $t_start_date = $p_creation_date;
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
  define ( GANTT_CHART_DAY_SUNDAY, 0);
  define ( GANTT_CHART_DAY_SATURDAY, 6);
  $t_one_day_in_seconds = 1 * 24 * 3600;
  $t_day = strftime( "%w", $p_date );
  
  switch ( $t_day ){
    case GANTT_CHART_DAY_SUNDAY:
      $t_adjusted_days = 1;
      break;
    case GANTT_CHART_DAY_SATURDAY:
      $t_adjusted_days = 2;
      break;
    default:
      $t_adjusted_days = 0;
      break;
  }
  
  return $p_date + ($t_adjusted_days * $t_one_day_in_seconds);
}


// # ----------------------------------------------------
// # Check that there is enough data to create graph
// # ----------------------------------------------------
// function error_check( $bug_count, $title ) {
//  if( 0 == $bug_count ) {
//    $t_graph_font = graph_get_font();
// 
//    error_text( $title, plugin_lang_get( 'not_enough_data' ) );
//  }
// }
// 
// function error_text( $title, $text ) { 
//    if( OFF == plugin_config_get( 'eczlibrary' ) ) {
//      $graph = new CanvasGraph( 300, 380 );
// 
//      $txt = new Text( $text, 150, 100 );
//      $txt->Align( "center", "center", "center" );
//      $txt->SetFont( $t_graph_font, FS_BOLD );
//      $graph->title->Set( $title );
//      $graph->title->SetFont( $t_graph_font, FS_BOLD );
//      $graph->AddText( $txt );
//      $graph->Stroke();
//    } else {
//      $im = imagecreate(300, 300);
//      /* @todo check: error graphs dont support utf8 */
//      $bg = imagecolorallocate($im, 255, 255, 255);
//      $textcolor = imagecolorallocate($im, 0, 0, 0);
//      imagestring($im, 5, 0, 0, $text, $textcolor);
//      header('Content-type: image/png');
//      imagepng($im);
//      imagedestroy($im);
//    }
//  die;
// }