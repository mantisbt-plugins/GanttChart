GanttChart
================

Gantt Chart Plugin for Mantis
By Alain D'EURVEILHER (alain.deurveilher@gmail.com)


This plugin is based on the Mantis Graph plugin. The jpgraph library is required to make it work.
The Gantt Chart plugin for mantis indeed uses the jpgraph_gantt.php lib.

TODO: Complete the README file.
TODO: update the documentation.
TODO: Comment the source code

+------------------------------------------------------------------------------+
Version 1.2.1:

Add link in the Roadmap and Changelog page (requires the following events to be added in these pages: 
EVENT_VIEW_ROADMAP_EXTRA
EVENT_VIEW_CHANGELOG_EXTRA

of type EVENT_TYPE_CHAIN
)
+------------------------------------------------------------------------------+
Version 1.2.0:

Add the possibility to specify the unit of the duration, in order to increase compatibility with other plugins:
Days, Hours
+------------------------------------------------------------------------------+
Version 1.1:

Add boundaries to the graph to limit to configured range of rows, and weeks
Updated the configuration page
Add a small description and documentation
+------------------------------------------------------------------------------+
Version 1.0:

Creation of the plugin
+------------------------------------------------------------------------------+