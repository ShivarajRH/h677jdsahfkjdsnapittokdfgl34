<?php
/**
 * Application Status Settings 
 */

/*
 * Transaction status flags
 */
$config ['trans_status'] = array ();
$config ['trans_status'] [0] = 'Pending';
$config ['trans_status'] [1] = 'Partially Invoiced';
$config ['trans_status'] [2] = 'Invoiced';
$config ['trans_status'] [3] = 'Partially Shipped';
$config ['trans_status'] [4] = 'Shipped';
$config ['trans_status'] [5] = 'Closed';
$config ['trans_status'] [6] = 'Cancelled';


/*
 * Order status flags 
 */
$config ['order_status'] = array ();
$config ['order_status'] [0] = 'Pending';
$config ['order_status'] [1] = 'Invoiced';
$config ['order_status'] [2] = 'Outscanned';
$config ['order_status'] [3] = 'Shipped';
$config ['order_status'] [4] = 'Delivered';
$config ['order_status'] [5] = 'Returned';
$config ['order_status'] [6] = 'Cancelled';


$config['task_status']=array();
$config['task_status'][0]='';
$config['task_status'][1]='Pending';
$config['task_status'][2]='Complete';
$config['task_status'][3]='Closed';


$config['task_for']=array();
$config['task_for'][0]='';
$config['task_for'][1]='Existing Franchise';
$config['task_for'][2]='New Franchise';

$config['return_cond']=array();
$config['return_cond'][1]='Good Condition';
$config['return_cond'][2]='Duplicate product';
$config['return_cond'][3]='UnOrdered Product';
$config['return_cond'][4]='Late Shipment';
$config['return_cond'][5]='Address not found';
$config['return_cond'][6]='Faulty and needs service';


$config['return_request_cond'] = array();
$config['return_request_cond'][0] = 'Pending';
$config['return_request_cond'][1] = 'Updated';
$config['return_request_cond'][2] = 'Closed';

$config['return_process_cond'] = array();
$config['return_process_cond'][0] = 'Pending';
$config['return_process_cond'][1] = 'Out for Service';
$config['return_process_cond'][2] = 'Move to Warehouse Stock';
$config['return_process_cond'][3] = 'Ready to ship';

$config['order_by']=array();
$config['order_by'][0]='Storeking';
$config['order_by'][1]='Snapittoday';
$config['order_by'][2]='Partner';

