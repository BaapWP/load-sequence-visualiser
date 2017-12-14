# Load Sequence Visualiser

## Concept

A granular visual sequence that shows everything from client request to WordPress load sequence to server response. Would be very useful for absolute beginners just picking up web development.

 * Request Headers
 * File Includes
 * Global Variables
 * Constants
 * Hooks

**Code Strategy**
Hook into the 'all' hook and through that generate and refine a *nested* load sequence in some form.

At each such hook, `get_icluded_files()`, `get_defined_constants()`, `__FILE__`, `__FUNCTION__`, `__METHOD__`, `__CLASS__`, `current_filter()`, `func_get_args()`, etc should give us enough raw data.

By comparing with data already in memory, it should be possible to create a nested ui that displays all such information in a collapsible manner.
