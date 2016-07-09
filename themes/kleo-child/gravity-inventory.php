<?php
/**
 * Gravity Wiz // Gravity Forms // Better Inventory with Gravity Forms
 *
 * Implements the concept of "inventory" with Gravity Forms by allowing the specification of a limit determined by the
 * sum of a specific field, typically a quantity field.
 *
 * @version   2.7
 * @author    David Smith <david@gravitywiz.com>
 * @license   GPL-2.0+
 * @link      http://gravitywiz.com/2012/09/19/better-inventory-with-gravity-forms/
 * @copyright 2014 Gravity Wiz
 */
class GW_Inventory {

    public $_args;

    public function __construct( $args ) {

        // make sure we're running the required minimum version of Gravity Forms
        if( ! property_exists( 'GFCommon', 'version' ) || ! version_compare( GFCommon::$version, '1.8', '>=' ) )
            return;

        $this->_args = $this->parse_args( $args );

        extract( $this->_args ); // we need $form_id, $approved_payments_only, $enable_notifications

        add_filter( "gform_pre_render_{$form_id}", array( $this, 'limit_by_field_values' ) );
        add_filter( "gform_validation_{$form_id}", array( $this, 'limit_by_field_values_validation' ) );

	    // add 'sum' action for [gravityforms] shortcode
	    add_filter( 'gform_shortcode_sum', array( $this, 'shortcode_sum' ), 10, 2 );
	    add_filter( 'gform_shortcode_remaining', array( $this, 'shortcode_remaining' ), 10, 2 );

	    add_action( 'gwinv_before_get_sum', array( $this, 'before_get_sum' ) );
	    add_action( 'gwinv_after_get_sum', array( $this, 'after_get_sum' ) );

        if( $enable_notifications ) {
	        $this->enable_notifications();
        }

    }

    public function parse_args( $args ) {

        $args = wp_parse_args( $args, array(
            'form_id'                  => false,
            'field_id'                 => false,
            'input_id'                 => false,
            'stock_qty'                => false,
            'out_of_stock_message'     => __( 'Sorry, this item is out of stock.' ),
            'not_enough_stock_message' => __( 'You ordered %1$s of this item but there are only %2$s of this item left.' ),
            'approved_payments_only'   => false,
            'hide_form'                => false,
            'enable_notifications'     => true,
            'field_group'              => array()
        ) );

        extract( $args );

        if( ! $stock_qty && isset( $limit ) ) {
            $args['stock_qty'] = $limit;
            unset( $args['limit'] );
        }

        if( isset( $limit_message ) ) {
            $args['out_of_stock_message'] = $limit_message;
            unset( $args['limit_message'] );
        }

        if( isset( $validation_message ) ) {
            $args['not_enough_stock_message'] = $validation_message;
            unset( $args['validation_message'] );
        }

        if( ! $args['input_id'] ) {
            $args['input_id'] = $args['field_id'];
            unset( $args['field_id'] );
        }

        if( $field_group && ! is_array( $field_group ) ) {
            $args['field_group'] = array( $field_group );
        }

        return $args;
    }

    public function enable_notifications() {

        if( ! class_exists( 'GW_Notification_Event' ) ) {

            _doing_it_wrong( 'GW_Inventory::$enable_notifications', __( 'Inventory notifications require the \'GW_Notification_Event\' class.' ), '1.0' );

        } else {

            $event_slug = "gwinv_out_of_stock_{$this->_args['input_id']}";
            $event_name = GFForms::get_page() == 'notification_edit' ? $this->get_notification_event_name() : __( 'Event name is only populated on Notification Edit view; saves a DB call to get the form on every ' );

            $this->_notification_event = new GW_Notification_Event( array(
                'form_id'    => $this->_args['form_id'],
                'event_name' => $event_name,
                'event_slug' => $event_slug,
                'trigger'    => array( $this, 'notification_event_listener' )
            ) );

        }

    }

    public function limit_by_field_values( $form ) {

        if( $this->is_in_stock() )
            return $form;

        if( $this->_args['hide_form'] ) {
            add_filter( "gform_get_form_filter_{$form['id']}", create_function( '', 'return "' . $this->_args['out_of_stock_message'] . '";' ) );
        } else if( empty( $this->_args['field_group'] ) ) {
            add_filter( 'gform_field_input', array( $this, 'hide_field' ), 10, 2 );
        }

        return $form;
    }

    public function limit_by_field_values_validation( $validation_result ) {

        $input_id           = $this->_args['input_id'];
        $limit              = $this->get_stock_quantity();
        $validation_message = $this->_args['not_enough_stock_message'];

        $form = $validation_result['form'];
        $exceeded_limit = false;

        foreach( $form['fields'] as &$field ) {

            if( $field['id'] != intval( $input_id ) ) {
	            continue;
            }

	        $requested_qty = rgpost( 'input_' . str_replace( '.', '_', $input_id ) );
            $field_sum = $this->get_sum();

            if( rgblank( $requested_qty ) || $field_sum + $requested_qty <= $limit ) {
	            continue;
            }

            $exceeded_limit = true;
            $stock_left     = $limit - $field_sum >= 0 ? $limit - $field_sum : 0;

            $field['failed_validation'] = true;
            $field['validation_message'] = sprintf( $validation_message, $requested_qty, $stock_left );

        }

	    if( $exceeded_limit && ! empty( $this->_args['field_group'] ) ) {
		    foreach( $form['fields'] as &$field ) {
			    if( in_array( $field->id, $this->_args['field_group'] ) ) {
				    $field['failed_validation'] = true;
				    $field['validation_message'] = sprintf( $validation_message, $requested_qty, $stock_left );
			    }
		    }
	    }

        $validation_result['form'] = $form;
        $validation_result['is_valid'] = ! $validation_result['is_valid'] ? false : ! $exceeded_limit;

        return $validation_result;
    }

	public function get_stock_quantity() {

		$stock = $this->_args['stock_qty'];

		if( is_callable( $stock ) ) {
			$stock = call_user_func( $stock );
		}

		return $stock;
	}

    public function is_in_stock() {
        $count = self::get_field_values_sum( $this->_args['form_id'], $this->_args['input_id'] );
        return $count < $this->get_stock_quantity();
    }

    public function hide_field( $field_content, $field ) {

        if( $field['id'] == intval( $this->_args['input_id'] ) )
            return "<div class=\"ginput_container\">{$this->_args['out_of_stock_message']}</div>";

        return $field_content;
    }

    public function notification_event_listener() {

        // really is no better hook to use to send custom notifications just yet
        add_filter( "gform_confirmation_{$this->_args['form_id']}", array( $this, 'send_out_of_stock_notifications' ), 10, 3 );

    }

    public function send_out_of_stock_notifications( $return, $form, $entry ) {

        // if product is still in stock or the entry is spam, don't sent notification
        if( $this->is_in_stock() || $entry['status'] == 'spam' )
            return $return;

        // if product is out of stock and no qty of the product is in current order, assume that out of stock notifications have already been sent
        $requested_qty = intval( rgar( $entry, (string) $this->_args['input_id'] ) );
        if( $requested_qty <= 0 )
            return $return;

        $this->_notification_event->send_notifications( $this->_notification_event->get_event_slug(), $form, $entry );

        return $return;
    }

    public function get_notification_event_name() {

        $form = GFAPI::get_form( $this->_args['form_id'] );
        $field = GFFormsModel::get_field( $form, $this->_args['input_id'] );

        $event_name = sprintf( __( '%s: Out of Stock' ), GFCommon::get_label( $field ) );

        return $event_name;
    }

    public function limit_by_field_group( $query, $form_id, $input_id ) {
        global $wpdb;

        if( $input_id != $this->_args['input_id'] ) {
            return $query;
        }

        $form = GFAPI::get_form( $form_id );
        $join = $where = array();
        $select = $from = '';

        foreach( $this->_args['field_group'] as $index => $field_id ) {

            $field   = GFFormsModel::get_field( $form, $field_id );
            $alias   = sprintf( 'fgld%d', $index + 1 );

            if( $index == 0 ) {
                $select  = "SELECT DISTINCT {$alias}.lead_id";
                $from    = "FROM {$wpdb->prefix}rg_lead_detail {$alias}";
                $value   = $field->get_value_save_entry( GFFormsModel::get_field_value( $field ), $form, null, null, null );
                $where[] = $wpdb->prepare( "( {$alias}.form_id = %d and CAST( {$alias}.field_number as unsigned ) = %d and {$alias}.value = %s )", $form_id, $field_id, $value );
            } else {
                $join[]  = "INNER JOIN {$wpdb->prefix}rg_lead_detail {$alias} ON ld.lead_id = {$alias}.lead_id";
            }

        }

        $field_group_query = array(
            'select' => $select,
            'from'   => $from,
            'join'   => implode( ' ', $join ),
            'where'  => sprintf( 'WHERE %s', implode( "\nand ", $where ) )
        );

        $query['where'] .= sprintf( ' AND l.id IN( %s )', implode( "\n", $field_group_query ) );

        return $query;
    }

	public function shortcode_sum( $output, $atts ) {

		$atts = shortcode_atts( array(
			'id' => false,
			'input_id' => false
		), $atts );

		extract( $atts ); // gives us $id, $input_id

		return intval( self::get_field_values_sum( $id, $input_id ) );
	}

	public function shortcode_remaining( $output, $atts ) {

		$atts = shortcode_atts( array(
			'id' => false,
			'input_id' => false,
			'limit'    => false
		), $atts );

		extract( $atts ); // gives us $id, $input_id

		$remaining = $limit - intval( self::get_field_values_sum( $id, $input_id ) );

		return max( 0, $remaining );
	}

	public function get_sum() {

		if( $this->_args['approved_payments_only'] ) {
			add_filter( 'gwinv_query', array( $this, 'limit_by_approved_payments_only' ) );
		}

		if( ! empty( $this->_args['field_group'] ) ) {
			add_filter( 'gwinv_query', array( $this, 'limit_by_field_group' ), 10, 3 );
		}

		$sum = self::get_field_values_sum( $this->_args['form_id'], $this->_args['input_id'] );

		remove_filter( 'gwinv_query', array( $this, 'limit_by_approved_payments_only' ) );
		remove_filter( 'gwinv_query', array( $this, 'limit_by_field_group' ) );

		return $sum;
	}

	public static function get_field_values_sum( $form_id, $input_id ) {
		global $wpdb;

		$query = array(
			'select' => 'SELECT sum( ld.value )',
			'from'   => "FROM {$wpdb->prefix}rg_lead_detail ld",
			'join'   => "INNER JOIN {$wpdb->prefix}rg_lead l ON l.id = ld.lead_id",
			'where'  => $wpdb->prepare( "
                WHERE ld.form_id = %d
                AND CAST( ld.field_number as unsigned ) = %d
                AND l.status = 'active'",
				$form_id, $input_id
			)
		);

		$query  = apply_filters( 'gwlimitbysum_query',                 $query, $form_id, $input_id );
		$query  = apply_filters( 'gwinv_query',                        $query, $form_id, $input_id );
		$query  = apply_filters( "gwinv_query_{$form_id}",             $query, $form_id, $input_id );
		$query  = apply_filters( "gwinv_query_{$form_id}_{$input_id}", $query, $form_id, $input_id );

		$sql    = implode( ' ', $query );
		$result = $wpdb->get_var( $sql );

		return intval( $result );
	}

	public function limit_by_approved_payments_only( $query ) {
		$valid_statuses = array( 'Approved' /* old */, 'Paid', 'Active' );
		$query['where'] .= sprintf( ' AND l.payment_status IN ( %s ) OR l.payment_status IS NULL', self::prepare_strings_for_mysql_in_statement( $valid_statuses ) );
		return $query;
	}

	public static function prepare_strings_for_mysql_in_statement( $strings ) {
		$wrapped = array();
		foreach( $strings as $string ) {
			$wrapped[] = sprintf( '"%s"', $string );
		}
		return implode( ', ', $wrapped );
	}

}

class GWLimitBySum extends GW_Inventory { }

# Configuration

new GW_Inventory( array(
    'form_id'                  => 9,
    //'field_id'                 => 2.3,
    'field_group'              => array( 6, 7 ),
    'stock_qty'                => 1,
    'out_of_stock_message'     => 'Sorry, there are no more tickets available!',
    'not_enough_stock_message' => 'You ordered %1$s tickets. There are only %2$s tickets left.',
    'approved_payments_only'   => false,
    'hide_form'                => false,
    'enable_notifications'     => true
) );