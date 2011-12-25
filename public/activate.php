<?php
//
// Description
// -----------
// This function will activate a business, allowing access.
//
// Arguments
// ---------
// api_key:
// auth_token:
// id: 			The ID of the business to activate.
//
// Returns
// -------
// <rsp stat="ok" />
//
function ciniki_businesses_activate($ciniki) {
	//
	// Find all the required and optional arguments
	//
	require_once($ciniki['config']['core']['modules_dir'] . '/core/private/prepareArgs.php');
	$rc = ciniki_core_prepareArgs($ciniki, 'no', array(
		'id'=>array('required'=>'yes', 'blank'=>'no', 'errmsg'=>'No business specified'), 
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$args = $rc['args'];

	//
	// Check access 
	//
	require_once($ciniki['config']['core']['modules_dir'] . '/businesses/private/checkAccess.php');
	$rc = ciniki_businesses_checkAccess($ciniki, 0, 'ciniki.businesses.activate', $args['id']);
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	require_once($ciniki['config']['core']['modules_dir'] . '/core/private/dbQuoteRequestArg.php');
	require_once($ciniki['config']['core']['modules_dir'] . '/core/private/dbUpdate.php');
	$strsql = "UPDATE ciniki_businesses SET status = 1 "
		. "WHERE id = '" . ciniki_core_dbQuote($ciniki, $args['id']) . "'";
	return ciniki_core_dbUpdate($ciniki, $strsql, 'businesses');
}
?>
