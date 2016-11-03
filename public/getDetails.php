<?php
//
// Description
// -----------
// This function will get detail values for a business.  These values
// are used many places in the API and MOSSi.
//
// Info
// ----
// Status: beta
//
// Arguments
// ---------
// api_key:
// auth_token:
// business_id:         The ID of the business to get the details for.
// keys:                The comma delimited list of keys to lookup values for.
//
// Returns
// -------
// <details>
//      <business name='' tagline='' />
//      <contact>
//          <person name='' />
//          <phone number='' />
//          <fax number='' />
//          <email address='' />
//          <address street1='' street2='' city='' province='' postal='' country='' />
//          <tollfree number='' restrictions='' />
//      </contact>
// </details>
//
function ciniki_businesses_getDetails($ciniki) {
    //
    // Find all the required and optional arguments
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'prepareArgs');
    $rc = ciniki_core_prepareArgs($ciniki, 'no', array(
        'business_id'=>array('required'=>'yes', 'blank'=>'no', 'name'=>'Business'), 
        'keys'=>array('required'=>'no', 'blank'=>'yes', 'name'=>'Keys'),
        ));
    if( $rc['stat'] != 'ok' ) {
        return $rc;
    }
    $args = $rc['args'];
    
    //
    // Check access to business_id as owner, or sys admin
    //
    ciniki_core_loadMethod($ciniki, 'ciniki', 'businesses', 'private', 'checkAccess');
    $ac = ciniki_businesses_checkAccess($ciniki, $args['business_id'], 'ciniki.businesses.getDetails');
    if( $ac['stat'] != 'ok' ) {
        return $ac;
    }

    // Split the keys, if specified
    if( isset($args['keys']) && $args['keys'] != '' ) {
//  if( isset($ciniki['request']['args']['keys']) && $ciniki['request']['args']['keys'] != '' ) {
        $detail_keys = preg_split('/,/', $args['keys']);
    } else {
        return array('stat'=>'fail', 'err'=>array('code'=>'ciniki.businesses.46', 'msg'=>'No keys specified'));
    }

    $rsp = array('stat'=>'ok', 'details'=>array());

    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbQuote');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbHashQuery');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQuery');
    ciniki_core_loadMethod($ciniki, 'ciniki', 'core', 'private', 'dbDetailsQueryDash');
    foreach($detail_keys as $detail_key) {
        if( $detail_key == 'business' ) {
            $strsql = "SELECT name, category, sitename, tagline FROM ciniki_businesses "
                . "WHERE id = '" . ciniki_core_dbQuote($ciniki, $args['business_id']) . "' ";
            $rc = ciniki_core_dbHashQuery($ciniki, $strsql, 'ciniki.businesses', 'business');
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            $rsp['details']['business.name'] = $rc['business']['name'];
            $rsp['details']['business.category'] = $rc['business']['category'];
            $rsp['details']['business.sitename'] = $rc['business']['sitename'];
            $rsp['details']['business.tagline'] = $rc['business']['tagline'];
        } elseif( in_array($detail_key, array('contact', 'ciniki')) ) {
            $rc = ciniki_core_dbDetailsQuery($ciniki, 'ciniki_business_details', 'business_id', $args['business_id'], 'ciniki.businesses', 'details', $detail_key);
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            if( $rc['details'] != null ) {
                $rsp['details'] += $rc['details'];
            }
        } elseif( in_array($detail_key, array('social')) ) {
            $rc = ciniki_core_dbDetailsQueryDash($ciniki, 'ciniki_business_details', 'business_id', $args['business_id'], 'ciniki.businesses', 'details', $detail_key);
            if( $rc['stat'] != 'ok' ) {
                return $rc;
            }
            if( $rc['details'] != null ) {
                $rsp['details'] += $rc['details'];
            }
        }
    }

    return $rsp;
}
?>
