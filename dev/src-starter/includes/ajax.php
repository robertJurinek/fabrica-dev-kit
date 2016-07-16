<?php
/* =========================================================================
   Ajax calls and handlers
   ========================================================================= */

namespace yww\devkit;

require_once('project.php');

class Ajax extends Singleton {

	function __construct() {
		// Namespaced tags
		$this->postNonce = Project::getInstance()->projectNamespace . '-post-nonce';
		$this->varsTag = Project::getInstance()->projectNamespace . '_ajax_script_vars';

		add_filter($this->varsTag, array($this, 'updateScriptVars'));

		// AJAX handler functions as required
		add_action('wp_ajax_nopriv_ajax-ACTION', array($this, 'ajaxHandler'));
		add_action('wp_ajax_ajax-ACTION', array($this, 'ajaxHandler'));
	}

	// Send script variables to front end
	function updateScriptVars($scriptVars = array()) {

		// Non-destructively merge script variables according to page or query conditions
		if (is_single()) {
			$scriptVars = array_merge($scriptVars, array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'postNonce' => wp_create_nonce($this->postNonce),
			));
		}
		return $scriptVars;

	}

	// Handle AJAX requests
	function ajaxHandler() {

		if (isset($_POST['postNonce'])) {
			$nonce = $_POST['postNonce'];
		} else {
			$this->sendAjaxResponse(array('success' => false, 'error' => "Couldn't retrieve nonce."));
		}

		if (!wp_verify_nonce($nonce, $this->postNonce)) {
			$this->sendAjaxResponse(array('success' => false, 'error' => 'Invalid nonce.'));
		}

		// Retrieve submitted data
		// $postID = $_POST['postID'];

		// Act on it

		// Add data to response + send!
		$this->sendAjaxResponse(array('success' => true));

	}

	// Send AJAX responses
	function sendAjaxResponse($response) {

		header('Content-Type: application/json');
		echo json_encode($response);
		exit;

	}
}

// Create a singleton instance of Ajax
Ajax::getInstance();
