<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_GZD_Email_Customer_Trusted_Shops' ) ) :

/**
 * eKomi Review Reminder Email
 *
 * This Email is being sent after the order has been marked as completed to transfer the eKomi Rating Link to the customer.
 *
 * @class 		WC_GZD_Email_Customer_Ekomi
 * @version		1.0.0
 * @author 		Vendidero
 */
class WC_GZD_Email_Customer_Trusted_Shops extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'customer_trusted_shops';
		$this->title 			= _x( 'Trusted Shops Review Reminder', 'trusted-shops', 'woocommerce-germanized' );
		$this->description		= _x( 'This E-Mail is being sent to a customer to remind him about the possibility to leave a review at Trusted Shops.', 'trusted-shops', 'woocommerce-germanized' );

		$this->heading 			= _x( 'Please rate your Order', 'trusted-shops', 'woocommerce-germanized' );
		$this->subject      	= _x( 'Please rate your {site_title} order from {order_date}', 'trusted-shops', 'woocommerce-germanized' );

		$this->template_html 	= 'emails/customer-trusted-shops.php';
		$this->template_plain  	= 'emails/plain/customer-trusted-shops.php';


		// Triggers for this email
		add_action( 'woocommerce_germanized_trusted_shops_review_notification', array( $this, 'trigger' ) );

		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $order_id ) {

		if ( $order_id ) {
			$this->object 		= wc_get_order( $order_id );
			$this->recipient	= $this->object->billing_email;

			$this->find['order-date']      = '{order_date}';
			$this->find['order-number']    = '{order_number}';
			
			$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
			$this->replace['order-number'] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'order' 		=> $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false
		) );
		return ob_get_clean();
	}

}

endif;

return new WC_GZD_Email_Customer_Trusted_Shops();