<?php
//
// Description
// -----------
// This method will return a list of owners and employee's for a business.
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id: 			The ID of the business to lock.
//
// Returns
// -------
// <users>
// 	<user id="1" display_name="Andrew" />
// </users>
//
function ciniki_businesses_employees($ciniki) {
	//
	// Find all the required and optional arguments
	//
	require_once($ciniki['config']['core']['modules_dir'] . '/core/private/prepareArgs.php');
	$rc = ciniki_core_prepareArgs($ciniki, 'no', array(
		'business_id'=>array('required'=>'yes', 'blank'=>'no', 'errmsg'=>'No business specified'), 
		));
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}
	$args = $rc['args'];

	//
	// Check access 
	//
	require_once($ciniki['config']['core']['modules_dir'] . '/businesses/private/checkAccess.php');
	$rc = ciniki_businesses_checkAccess($ciniki, $args['business_id'], 'ciniki.businesses.employees');
	if( $rc['stat'] != 'ok' ) {
		return $rc;
	}

	//
	// Get the list of users who have access to this business
	//
	$strsql = "SELECT ciniki_business_users.user_id AS id, ciniki_users.display_name "
		. "FROM ciniki_business_users, ciniki_users "
		. "WHERE ciniki_business_users.business_id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' " 
		. "AND ciniki_business_users.user_id = ciniki_users.id "
		. "ORDER BY display_name "
		. "";
	ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbRspQuery');
	$rc = ciniki_core_dbRspQuery($ciniki, $strsql, 'businesses', 'users', 'user', array('stat'=>'ok', 'users'=>array()));

	return $rc;
}
?>