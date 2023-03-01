<?php

namespace CPNB\Controllers;

use ChurchPlugins\Controllers\Controller;
use ChurchPlugins\Exception;

class NotificationBar extends Controller{

	/**
	 * constructor.
	 *
	 * @param $id
	 * @param bool $use_origin whether or not to use the origin id
	 *
	 * @throws Exception
	 */
	public function __construct( $id, $use_origin = true ) {
		$classname = explode( '\\', get_called_class() );
		$namespace = array_shift( $classname );
		$classname = array_pop( $classname );
		$classname = $namespace . '\Models\\' . $classname;

		/** @var $class \ChurchPlugins\Models\Table */
		$class = $classname;

		if ( class_exists( $classname ) ) {
			$this->model = $class::get_instance_from_origin( $id );
		} else {
			throw new Exception( 'No model found for ' . $class );
		}

		$this->post = get_post( $id );
	}

	/**
	 * Get the text for the notification bar
	 *
	 * @param $raw
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_text( $raw = false ) {
		$content = $this->text;
		if ( ! $raw ) {
			$content = apply_filters( 'the_content', $content );
		}

		return $this->filter( wp_kses_post( $content ), __FUNCTION__ );
	}

	/**
	 * Return action url for this notification bar
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_url() {
		return $this->filter( $this->url, __FUNCTION__ );
	}

	/**
	 * Get button text for this notification bar
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_button_text() {
		return $this->filter( $this->button_text, __FUNCTION__ );
	}

	/**
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_expiration_date() {
		return $this->filter( $this->expiration_date, __FUNCTION__ );
	}

	/**
	 * If this bar has an action or is it just text
	 *
	 * @return bool
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function has_action() {
		return ! empty( $this->get_url() );
	}

	/**
	 * If this bar has a button
	 *
	 * @return bool
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function has_button() {
		return ( ! empty( $this->get_button_text() ) && $this->has_action() );
	}

	/**
	 * If the whole bar should be clickable
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function is_clickable() {
		return $this->filter( $this->has_action() && ! $this->has_button(), __FUNCTION__ );
	}

	/**
	 * If the action is for a local url or external url
	 *
	 * @return bool
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function is_local_action() {
		if ( ! $this->has_action() ) {
			return false;
		}

		$url = $this->get_url();

		if ( str_starts_with( $url, '/' ) ) {
			return true;
		}

		return str_contains( $url, get_home_url() );
	}

	/**
	 * Schedule the expiration if applicable
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function schedule_expiration() {
		$action = cp_notification_bars()->setup->post_types->notification_bar->expiration_action;

		$timestamp = wp_next_scheduled( $action, [ $this->post->ID ] );

		if ( $expiration_date = $this->get_expiration_date() ) {
			$expiration_date -= (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		}

		// no expiration and no existing scheduled deletion for this bar
		if ( empty( $timestamp ) && empty( $expiration_date ) ) {
			return;
		}

		// the current schedule already matches the expiration date
		if ( $timestamp == $expiration_date ) {
			return;
		}

		// we have a timestamp that doesn't match the expiration date... remove it
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $action, [ $this->post->ID ] );
		}

		// we have an expiration date that hasn't already been scheduled... create it
		if ( $expiration_date ) {
			wp_schedule_single_event( $expiration_date, $action, [ $this->post->ID ] );
		}
	}

}
