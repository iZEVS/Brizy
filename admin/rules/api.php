<?php


class Brizy_Admin_Rules_Api extends Brizy_Admin_AbstractApi {

	const nonce = 'brizy-rule-api';
	const CREATE_RULES_ACTION = 'brizy_add_rules';
	const CREATE_RULE_ACTION = 'brizy_add_rule';
	const DELETE_RULE_ACTION = 'brizy_delete_rule';
	const LIST_RULE_ACTION = 'brizy_list_rules';

	/**
	 * @var Brizy_Admin_Rules_Manager
	 */
	private $manager;


	/**
	 * Brizy_Admin_Rules_Api constructor.
	 *
	 * @param Brizy_Admin_Rules_Manager $manager
	 */
	public function __construct( $manager ) {
		$this->manager = $manager;

		parent::__construct();
	}

	/**
	 * @return Brizy_Admin_Rules_Api
	 */
	public static function _init() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self( new Brizy_Admin_Rules_Manager() );
		}

		return $instance;
	}

	protected function getRequestNonce() {
		return $this->param( 'hash' );
	}

	protected function initializeApiActions() {
		add_action( 'wp_ajax_' . self::CREATE_RULE_ACTION, array( $this, 'actionCreateRule' ) );
		add_action( 'wp_ajax_' . self::CREATE_RULES_ACTION, array( $this, 'actionCreateRules' ) );
		add_action( 'wp_ajax_' . self::DELETE_RULE_ACTION, array( $this, 'actionDeleteRule' ) );
		add_action( 'wp_ajax_' . self::LIST_RULE_ACTION, array( $this, 'actionGetRuleList' ) );
	}


	/**
	 * @return null|void
	 */
	public function actionGetRuleList() {

		$this->verifyNonce( self::nonce );

		$postId = (int) $this->param( 'post' );

		if ( ! $postId ) {
			wp_send_json_error( (object) array( 'message' => 'Invalid template' ), 400 );
		}

		$rules = $this->manager->getRules( $postId );

		wp_send_json_success( $rules, 200 );

		return null;
	}

	public function actionCreateRule() {

		$this->verifyNonce( self::nonce );

		$postId = (int) $this->param( 'post' );

		if ( ! $postId ) {
			wp_send_json_error( (object) array( 'message' => 'Invalid template' ), 400 );
		}

		$ruleData = file_get_contents( "php://input" );
		$ruleJson = json_decode( $ruleData );
		$rule     = Brizy_Admin_Rule::createFromJsonObject( $ruleJson );

		// validate rule
		$ruleSet = $this->manager->getAllRulesSet();

		foreach ( $ruleSet->getRules() as $arule ) {

			if ( $rule->isEqual( $arule ) ) {
				wp_send_json_error( (object) array(
					'message' => 'The rule is already used in one template',
					'rule'    => $arule->getId()
				), 400 );
			}
		}

		$this->manager->addRule( $postId, $rule );

		wp_send_json_success( $rule, 200 );

		return null;
	}

	public function actionCreateRules() {
		$this->verifyNonce( self::nonce );

		$postId = (int) $this->param( 'post' );
		$postType = get_post_type( $postId );

		if ( ! $postId ) {
			wp_send_json_error( (object) array( 'message' => 'Invalid template' ), 400 );
		}

		$rulesData = file_get_contents( "php://input" );
		$rulesJson = json_decode( $rulesData );
		$rules     = array();
		foreach ( $rulesJson as $ruleJson ) {
			$rules[] = Brizy_Admin_Rule::createFromJsonObject( $ruleJson );

		}
		// validate rule
		$ruleSet = $this->manager->getAllRulesSet(array(), $postType);
		$errors  = array();
		foreach ( $ruleSet->getRules() as $arule ) {
			foreach ( $rules as $newRule ) {
				if ( $newRule->isEqual( $arule ) ) {
					$errors[] = (object) array(
						'message' => 'The rule is already used',
						'rule'    => $arule->getId()
					);
				}
			}
		}

		if ( count( $errors ) > 0 ) {
			wp_send_json_error( $errors, 400 );
		}

		foreach ( $rules as $newRule ) {
			$this->manager->addRule( $postId, $newRule );
		}

		wp_send_json_success( $rules, 200 );

		return null;
	}

	public function actionDeleteRule() {

		$this->verifyNonce( self::nonce );

		$postId = (int) $this->param( 'post' );
		$ruleId = $this->param( 'rule' );

		if ( ! $postId || ! $ruleId ) {
			wp_send_json_error( null, 400 );
		}

		$this->manager->deleteRule( $postId, $ruleId );

		wp_send_json_success( null, 200 );
	}

}