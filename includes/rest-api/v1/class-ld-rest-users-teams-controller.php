<?php
/**
 * ebox REST API V1 Users Teams Controller.
 *
 * @since 2.5.8
 * @package ebox\REST\V1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( ! class_exists( 'LD_REST_Users_Teams_Controller_V1' ) ) && ( class_exists( 'LD_REST_Posts_Controller_V1' ) ) ) {

	/**
	 * Class ebox REST API V1 Users Teams Controller.
	 *
	 * @since 2.5.8
	 */
	class LD_REST_Users_Teams_Controller_V1 extends LD_REST_Posts_Controller_V1 /* phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound */ {

		/**
		 * Supported Collection Parameters.
		 *
		 * @since 2.5.8
		 *
		 * @var array $supported_collection_params.
		 */
		private $supported_collection_params = array(
			'exclude'  => 'post__not_in',
			'include'  => 'post__in',
			'offset'   => 'offset',
			'order'    => 'order',
			'orderby'  => 'orderby',
			'per_page' => 'posts_per_page',
			'page'     => 'paged',
			'search'   => 's',
			'fields'   => 'fields',
		);

		/**
		 * Public constructor for class
		 *
		 * @since 2.5.8
		 */
		public function __construct() {
			$this->post_type  = 'teams';
			$this->taxonomies = array();

			parent::__construct( $this->post_type );
			$this->namespace = ebox_REST_API_NAMESPACE . '/' . $this->version;
			$this->rest_base = ebox_Settings_Section::get_section_setting( 'ebox_Settings_Section_General_REST_API', 'users' );
		}

		/**
		 * Registers the routes for the objects of the controller.
		 *
		 * @since 2.5.8
		 *
		 * @see register_rest_route() in WordPress core.
		 */
		public function register_routes() {

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/(?P<id>[\d]+)/teams',
				array(
					'args'   => array(
						'id' => array(
							'description' => esc_html__( 'User ID', 'ebox' ),
							'required'    => true,
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_user_teams' ),
						'permission_callback' => array( $this, 'get_user_teams_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_user_teams' ),
						'permission_callback' => array( $this, 'update_user_teams_permissions_check' ),
						'args'                => array(
							'team_ids' => array(
								// translators: team.
								'description' => sprintf( esc_html_x( '%s IDs to add to User.', 'placeholder: g roup', 'ebox' ), ebox_get_custom_label( 'team' ) ),
								'required'    => true,
								'type'        => 'array',
								'items'       => array(
									'type' => 'integer',
								),
							),
						),
					),
					array(
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => array( $this, 'delete_user_teams' ),
						'permission_callback' => array( $this, 'delete_user_teams_permissions_check' ),
						'args'                => array(
							'team_ids' => array(
								// translators: team.
								'description' => sprintf( esc_html_x( '%s IDs to remove from User.', 'placeholder: team', 'ebox' ), ebox_get_custom_label( 'team' ) ),
								'required'    => true,
								'type'        => 'array',
								'items'       => array(
									'type' => 'integer',
								),
							),
						),
					),
					'schema' => array( $this, 'get_schema' ),
				)
			);
		}

		/**
		 * Gets the user teams schema.
		 *
		 * @since 2.5.8
		 *
		 * @return array
		 */
		public function get_schema() {
			$schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'user-team',
				'type'       => 'object',
				'properties' => array(
					'id'        => array(
						'description' => __( 'Unique identifier for the object.', 'ebox' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'team_ids' => array(
						'description' => sprintf(
							// translators: placeholder: team.
							esc_html_x(
								'The %s IDs.',
								'placeholder: team',
								'ebox'
							),
							ebox_get_custom_label_lower( 'team' )
						),
						'type'        => 'array',
						'items'       => array(
							'type' => 'integer',
						),
						'context'     => array( 'view', 'edit' ),
					),
				),
			);

			return $schema;
		}

		/**
		 * Check Users Teams Read Permissions.
		 *
		 * @since 2.5.8
		 *
		 * @param object $request WP_REST_Request instance.
		 */
		public function get_user_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} elseif ( get_current_user_id() == $request['id'] ) {
				return true;
			}
		}

		/**
		 * Check Users Teams Update Permissions.
		 *
		 * @since 2.5.8
		 *
		 * @param object $request WP_REST_Request instance.
		 */
		public function update_user_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} elseif ( get_current_user_id() == $request['id'] ) {
				return true;
			}
		}

		/**
		 * Check Users Teams Delete Permissions.
		 *
		 * @since 2.5.8
		 *
		 * @param object $request WP_REST_Request instance.
		 */
		public function delete_user_teams_permissions_check( $request ) {
			if ( ebox_is_admin_user() ) {
				return true;
			} elseif ( get_current_user_id() == $request['id'] ) {
				return true;
			}
		}

		/**
		 * Update Users Teams.
		 *
		 * @since 2.5.8
		 *
		 * @param object $request WP_REST_Request instance.
		 */
		public function update_user_teams( $request ) {
			$user_id = $request['id'];
			if ( empty( $user_id ) ) {
				return new WP_Error( 'rest_post_invalid_id', esc_html__( 'Invalid User ID.', 'ebox' ) . ' ' . __CLASS__, array( 'status' => 404 ) );
			}

			$team_ids = $request['team_ids'];
			if ( ( ! is_array( $team_ids ) ) || ( empty( $team_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					),
					array( 'status' => 404 )
				);
			} else {
				$team_ids = array_map( 'intval', $team_ids );
			}

			foreach ( $team_ids as $team_id ) {
				ld_update_team_access( $user_id, $team_id, false );
			}

			$data = array();

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Delete Users Teams.
		 *
		 * @since 2.5.8
		 *
		 * @param object $request WP_REST_Request instance.
		 */
		public function delete_user_teams( $request ) {
			$user_id = $request['id'];
			if ( empty( $user_id ) ) {
				return new WP_Error( 'rest_post_invalid_id', esc_html__( 'Invalid User ID.', 'ebox' ) . ' ' . __CLASS__, array( 'status' => 404 ) );
			}

			$team_ids = $request['team_ids'];
			if ( ( ! is_array( $team_ids ) ) || ( empty( $team_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Team.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Team',
							'ebox'
						),
						ebox_Custom_Label::get_label( 'team' )
					),
					array( 'status' => 404 )
				);
			} else {
				$team_ids = array_map( 'intval', $team_ids );
			}

			foreach ( $team_ids as $team_id ) {
				ld_update_team_access( $user_id, $team_id, true );
			}

			$data = array();

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get Users Teams.
		 *
		 * @since 2.5.8
		 *
		 * @param object $request WP_REST_Request instance.
		 */
		public function get_user_teams( $request ) {
			$user_id = $request['id'];
			if ( empty( $user_id ) ) {
				return new WP_Error( 'rest_user_invalid_id', esc_html__( 'Invalid user ID. #1', 'ebox' ), array( 'status' => 404 ) );
			}

			if ( is_user_logged_in() ) {
				$current_user_id = get_current_user_id();
			} else {
				$current_user_id = 0;
			}

			// Ensure a search string is set in case the orderby is set to 'relevance'.
			if ( ! empty( $request['orderby'] ) && 'relevance' === $request['orderby'] && empty( $request['search'] ) ) {
				return new WP_Error( 'rest_no_search_term_defined', __( 'You need to define a search term to order by relevance.', 'ebox' ), array( 'status' => 400 ) );
			}

			// Ensure an include parameter is set in case the orderby is set to 'include'.
			if ( ! empty( $request['orderby'] ) && 'include' === $request['orderby'] && empty( $request['include'] ) ) {
				return new WP_Error( 'rest_orderby_include_missing_include', __( 'You need to define an include parameter to order by include.', 'ebox' ), array( 'status' => 400 ) );
			}

			// Retrieve the list of registered collection query parameters.
			$registered = $this->get_collection_params();
			$args       = array();

			/*
			 * For each known parameter which is both registered and present in the request,
			 * set the parameter's value on the query $args.
			 */
			foreach ( $this->supported_collection_params as $api_param => $wp_param ) {
				if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
					$args[ $wp_param ] = $request[ $api_param ];
				}
			}

			// Check for & assign any parameters which require special handling or setting.
			$args['date_query'] = array();

			// Set before into date query. Date query must be specified as an array of an array.
			if ( isset( $registered['before'], $request['before'] ) ) {
				$args['date_query'][0]['before'] = $request['before'];
			}

			// Set after into date query. Date query must be specified as an array of an array.
			if ( isset( $registered['after'], $request['after'] ) ) {
				$args['date_query'][0]['after'] = $request['after'];
			}

			// Ensure our per_page parameter overrides any provided posts_per_page filter.
			if ( isset( $registered['per_page'] ) ) {
				$args['posts_per_page'] = $request['per_page'];
			}

			// Force the post_type argument, since it's not a user input variable.
			$args['post_type'] = $this->post_type;

			$args['post__in'] = array( 0 );
			$team_ids        = ebox_get_users_team_ids( $user_id );
			if ( ! empty( $team_ids ) ) {
				$args['post__in'] = $team_ids;
			}

			if ( ! isset( $args['fields'] ) ) {
				$args['fields'] = 'ids';
			} elseif ( 'ids' != $args['fields'] ) {
				unset( $args['fields'] );
			}

			/**
			 * Filters the query arguments for user teams REST request.
			 *
			 * Enables adding extra arguments or setting defaults for a post collection request.
			 *
			 * @since 2.5.8
			 *
			 * @link https://developer.wordpress.org/reference/classes/wp_query/
			 *
			 * @param array           $args    An array of query arguments for getting user teams.
			 * @param WP_REST_Request $request The REST request object.
			 */
			$args       = apply_filters( 'ebox_rest_user_teams_query', $args, $request );
			$query_args = $this->prepare_items_query( $args, $request );

			$posts_query  = new WP_Query();
			$query_result = $posts_query->query( $query_args );

			// Allow access to all password protected posts if the context is edit.
			if ( 'edit' === $request['context'] ) {
				add_filter( 'post_password_required', '__return_false' );
			}

			if ( ( ! isset( $args['fields'] ) ) || ( 'post' == $args['fields'] ) ) {
				$posts = array();

				foreach ( $query_result as $post ) {
					if ( ! $this->check_read_permission( $post ) ) {
						continue;
					}

					$data    = $this->prepare_item_for_response( $post, $request );
					$posts[] = $this->prepare_response_for_collection( $data );
				}

				$response = rest_ensure_response( $posts );

			} else {
				$response = rest_ensure_response( $query_result );
			}

			// Reset filter.
			if ( 'edit' === $request['context'] ) {
				remove_filter( 'post_password_required', '__return_false' );
			}

			$page        = (int) $query_args['paged'];
			$total_posts = $posts_query->found_posts;

			if ( $total_posts < 1 ) {
				// Out-of-bounds, run the query again without LIMIT for total count.
				unset( $query_args['paged'] );

				$count_query = new WP_Query();
				$count_query->query( $query_args );
				$total_posts = $count_query->found_posts;
			}

			$max_pages = ceil( $total_posts / (int) $posts_query->query_vars['posts_per_page'] );

			if ( $page > $max_pages && $total_posts > 0 ) {
				return new WP_Error( 'rest_post_invalid_page_number', __( 'The page number requested is larger than the number of pages available.', 'ebox' ), array( 'status' => 400 ) );
			}

			$response->header( 'X-WP-Total', (int) $total_posts );
			$response->header( 'X-WP-TotalPages', (int) $max_pages );

			$request_params = $request->get_query_params();
			$base           = add_query_arg( $request_params, rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

			if ( $page > 1 ) {
				$prev_page = $page - 1;

				if ( $prev_page > $max_pages ) {
					$prev_page = $max_pages;
				}

				$prev_link = add_query_arg( 'page', $prev_page, $base );
				$response->link_header( 'prev', $prev_link );
			}
			if ( $max_pages > $page ) {
				$next_page = $page + 1;
				$next_link = add_query_arg( 'page', $next_page, $base );

				$response->link_header( 'next', $next_link );
			}

			return $response;
		}

		/**
		 * Get Collection parameters
		 *
		 * @since 2.5.8
		 */
		public function get_collection_params() {
			$query_params_default = parent::get_collection_params();

			$query_params_default['context']['default'] = 'view';

			$query_params            = array();
			$query_params['context'] = $query_params_default['context'];
			$query_params['fields']  = array(
				'description' => __( 'Returned values.', 'ebox' ),
				'type'        => 'string',
				'default'     => 'ids',
				'enum'        => array(
					'ids',
					'objects',
				),
			);
			foreach ( $this->supported_collection_params as $external_key => $internal_key ) {
				if ( isset( $query_params_default[ $external_key ] ) ) {
					$query_params[ $external_key ] = $query_params_default[ $external_key ];
				}
			}
			return $query_params;
		}
	}
}
