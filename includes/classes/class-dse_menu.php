<?php
	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Used to create the menus and options required
	 * for the plugin
	 *
	 * Class DSE_Menu
	 */
	class DSE_Menu {

		/*
		 * Hold an instance of the class
		 */
		private static $instance;

		/**
		 * DSE_Menu constructor.
		 *
		 * Add the root settings page
		 *
		 */
		public function __construct() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = $this;
			}

			// Register the hooks used by the plugin
			add_action( 'admin_menu', [ __CLASS__, 'Admin_Menu_Hook' ] );

		}

		/**
		 * Hooked into the admin_menu action hook
		 *
		 */
		public static function Admin_Menu_Hook() {

			/**
			 * Add the settings root page.
			 */
			add_menu_page(
				esc_html__( 'DropshipExpress', 'dropshipexpress' ),
				esc_html__( 'DropshipExpress', 'dropshipexpress' ),
				'manage_options',
				'dropship-express',
				[
					'DSE_Settings',
					'General_Options_CB',
				],
				'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iMTkyMC4wMDAwMDBwdCIgaGVpZ2h0PSIxOTIwLjAwMDAwMHB0IiB2aWV3Qm94PSIwIDAgMTkyMC4wMDAwMDAgMTkyMC4wMDAwMDAiCiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBtZWV0Ij4KCjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLDE5MjAuMDAwMDAwKSBzY2FsZSgwLjEwMDAwMCwtMC4xMDAwMDApIgpmaWxsPSIjMDAwMDAwIiBzdHJva2U9Im5vbmUiPgo8cGF0aCBkPSJNOTE0NSAxOTE4OSBjLTI0MDMgLTExOCAtNDYzOCAtMTEwOSAtNjMzNSAtMjgwOSAtMTgwNyAtMTgwOSAtMjgxMAotNDIzMSAtMjgxMCAtNjc4NSAwIC0xNzk2IDQ5OSAtMzU0NiAxNDQzIC01MDY1IDEwNDMgLTE2NzggMjYwMiAtMzAxNCA0NDEyCi0zNzgwIDkzOCAtMzk3IDE4NzUgLTYyOSAyOTM1IC03MjcgMjg2IC0yNiAxMzQ0IC0yNiAxNjMwIDAgMTU2MSAxNDUgMjk2Mwo2MTAgNDI1MCAxNDEwIDI0NTkgMTUyOSA0MTA2IDQwOTMgNDQ2NCA2OTUyIDU2IDQ1MiA2MSA1NDQgNjEgMTIxMCAwIDY2NiAtNQo3NTggLTYxIDEyMTAgLTI2NCAyMTA5IC0xMjI1IDQwNjcgLTI3MzkgNTU4MCAtMTgxOCAxODE5IC00MjYzIDI4MjcgLTY4MjUKMjgxMyAtMTQ2IDAgLTMzNyAtNSAtNDI1IC05eiBtMTUxOSAtNjkwMCBjNDI1IC0zMiA3NzIgLTExOCAxMDY3IC0yNjYgbDE0NwotNzMgMTU4IC0xNjMgYzIyMSAtMjI3IDMyOCAtMzY1IDQxMiAtNTMyIDYzIC0xMjYgOTggLTI2NCAxMTcgLTQ2NSBsNiAtNjUKLTY0IC02MyAtNjQgLTYzIC0yNTQgLTEwIGMtMTQwIC02IC01MjggLTEyIC04NjQgLTEyIC01NjcgLTIgLTYxNCAtMSAtNjYyIDE2Ci02MiAyMiAtOTEgNTMgLTE0MyAxNTYgLTIxIDQyIC00OSA4NiAtNjIgOTggLTYzIDU5IC0xODQgNzUgLTMzNiA0NCAtMTM0IC0yOAotMTk5IC01MyAtMjUwIC05NyAtMTI0IC0xMDYgLTQyIC0yNTYgMTg0IC0zMzQgNjMgLTIyIDM4OCAtMTA5IDg5MiAtMjM5IDQ2NgotMTIxIDY0NSAtMTgyIDkwMyAtMzExIDQwOSAtMjA1IDY2MiAtNDcyIDc3OSAtODIwIDExMCAtMzI3IDEwMyAtNzcyIC0xNwotMTE0NSAtMTI5IC00MDAgLTM2MCAtNjcwIC03NjkgLTg5OSAtMzQ4IC0xOTUgLTg3MCAtMzIwIC0xNTMzIC0zNjcgLTM4OSAtMjgKLTk3MCA0NiAtMTM1OCAxNzIgLTU1MiAxODAgLTg2OSA0NDcgLTEwNjQgOTAwIC03MCAxNjEgLTEzOSA0NTkgLTEzOSA1OTggMAo1NSAzNyA4NSA5MyA3NiAyMSAtNCA0NDAgMSA5MzIgMTEgODczIDE3IDg5NCAxNyA5MzUgLTEgNTEgLTIzIDgzIC02OSAxNDAKLTE5NyA2NyAtMTU1IDE0NiAtMjE3IDMwNyAtMjQwIDIxMiAtMzEgNDM0IDcxIDQ5MiAyMjcgMjggNzMgMjcgMTU1IDAgMjEyCi0zMCA2MSAtMTE5IDEzNiAtMjI0IDE4OCAtMTU2IDc2IC00ODQgMTY3IC04NDAgMjMwIC00NjMgODMgLTkzOCAyNjQgLTEyNTUKNDc5IC0zNTEgMjM3IC01MjAgNTU0IC01MzcgMTAwNyAtMTkgNDc4IDEzOCA5MjIgNDM3IDEyNDIgMzYwIDM4NCAxMDIwIDY1MQoxNzQ1IDcwNiAxNjQgMTIgNTI5IDEyIDY4OSAweiBtLTU3MDkgLTE1MiBjNTk4IC01OCA5OTQgLTE1NCAxMzUwIC0zMjcgMjI4Ci0xMTEgMzc2IC0yMTkgNTQ1IC0zOTcgMTUwIC0xNTkgMzM1IC00NjkgNDQ0IC03NDUgODUgLTIxNyAxNjAgLTU1MiAxODcgLTg0MQoxNyAtMTg5IDcgLTYwNCAtMjAgLTc4MiAtNTYgLTM2NiAtMTc2IC03MjUgLTMzNyAtMTAwNSAtMzM2IC01ODMgLTkwNyAtMTAxNwotMTU1NCAtMTE4MCAtMzI3IC04MyAtNTE1IC05MiAtMTMyOSAtNjkgLTMyNCAxMCAtNTM3IDkgLTEwNjUgLTIgLTM2NCAtNwotNzA0IC0xNCAtNzU2IC0xNCBsLTk1IDAgLTM1IDkwIC0zNSA5MCAtMyAyNTM0IGMtMiAyNDA3IC0xIDI1MzUgMTUgMjU1NCAzMgozNSA5MSA1NyAyMTAgNzggMTY5IDMwIDM0NiAzNCAxMzc4IDM0IDc5OSAtMSA5NDQgLTMgMTEwMCAtMTh6IG0xMjIyNSAtNzAwCmwwIC02NjIgLTI0IC00NSBjLTEzIC0yNSAtMzkgLTU0IC01NyAtNjUgLTMzIC0yMCAtNTAgLTIwIC02OTQgLTE4IC0zNjMgMAotNzc1IDYgLTkxNSAxMSAtMTQwIDYgLTI1NiAxMCAtMjU2IDkgLTcgLTggLTQ3IC0zNTUgLTUxIC00MzkgbC02IC0xMDggODY3IDAKYzkxMiAwIDkwOSAwIDk0NSAtNDQgMTEgLTEzIDE0IC03MSAxNiAtMjc0IDEgLTE0MSA2IC00MDUgMTAgLTU4NyA2IC0yODUgNQotMzMzIC04IC0zNTAgLTEzIC0xNiAtMjkgLTE5IC0xMjMgLTIxIC02MCAtMiAtMjYwIC0xMCAtNDQ0IC0xOCAtMzU1IC0xNwotOTU3IC0zNiAtMTEyNyAtMzYgbC0xMDMgMCAwIC0yNTEgMCAtMjUwIDE0MyA1IGM3OCAzIDIyNSA4IDMyNyAxMSAxMDIgMyAzMzQKMTMgNTE1IDIxIDE4MiA4IDQ2MSAxNiA2MjAgMTcgMzEyIDIgMzQ5IC0yIDQwMyAtNDggbDMwIC0yNSA4IC01NjcgYzUgLTMxMyA5Ci02MTUgOCAtNjczIDAgLTExMSAtMTAgLTE0NSAtNTQgLTE4NSAtMjIgLTIwIC00MSAtMjAgLTEwMTQgLTI3IC01NDUgLTMKLTEyNjUgLTEzIC0xNjAxIC0yMiAtMzM1IC04IC03ODMgLTE4IC05OTUgLTIyIGwtMzg2IC03IC01MSA1OCBjLTQ4IDUzIC01Mgo2MiAtNTkgMTI0IC03IDY3IC0xIDExOTYgMTYgMjc1MSA1IDQ3MCA5IDEyMDMgMTAgMTYyOCBsMCA3NzIgMjAyNSAwIDIwMjUgMAowIC02NjN6Ii8+CjxwYXRoIGQ9Ik00MjI4IDEwODk3IGwtNTcgLTEwMyAtNiAtODI1IGMtMTAgLTExNjAgMTIgLTE2ODIgODEgLTE4OTAgMjQgLTc2CjM4IC04MyAxNDYgLTc1IDM1NCAyOCA1OTUgMTQxIDc1NyAzNTYgMTQ4IDE5NyAyMjggNDQ2IDI2MCA4MTUgMTcgMTg1IDE0IDcwNgotNCA4NjAgLTI1IDIxMCAtNjUgMzUwIC0xNDggNTEwIC0zMSA2MCAtNjQgMTAzIC0xMzIgMTcxIC03OSA4MCAtMTAzIDk4IC0xOTUKMTQzIC0xNjAgNzggLTMyMyAxMTcgLTU2NSAxMzYgbC04MCA2IC01NyAtMTA0eiIvPgo8L2c+Cjwvc3ZnPgo='
			);


			/**
			 * Add the general settings menu item
			 */
			add_submenu_page(
				'dropship-express',
				esc_html__( 'General Options', 'dropshipexpress' ),
				esc_html__( 'General Options', 'dropshipexpress' ),
				'manage_options',
				'dropship-express',
				[
					'DSE_Settings',
					'General_Options_CB',
				],
				1
			);

			/**
			 * Create a menu item for importing products
			 */
			add_submenu_page(
				'dropship-express',
				esc_html__( 'Import New Product', 'dropshipexpress' ),
				esc_html__( 'Search / Import', 'dropshipexpress' ),
				DSE_Settings::Who_Can( 'permission_import_access' ),
				'dse-import-products',
				[
					'DSE_Import',
					'Output_Import_Menu_CB',
				],
				2
			);

			/**
			 * Create a menu item to view the imported products
			 *
			 */
			add_submenu_page(
				'dropship-express',
				esc_html__( 'View Imported Products', 'dropshipexpress' ),
				esc_html__( 'Imported Products', 'dropshipexpress' ),
				DSE_Settings::Who_Can( 'permission_publish_access' ),
				'dse-view-imported',
				[
					'DSE_Import',
					'View_Imported_Products_CB',
				],
				3
			);

			/**
			 * Create a menu item to render the import accounts
			 *
			 */
			add_submenu_page(
				'dropship-express',
				esc_html__( 'Import Rules', 'dropshipexpress' ),
				esc_html__( 'Import Rules', 'dropshipexpress' ),
				DSE_Settings::Who_Can( 'permission_automation_access' ),
				'dse-import-rules',
				[
					'DSE_Import',
					'Render_Import_Rules',
				],
				4
			);

			/**
			 * Create a menu item to view the statistics
			 * todo: complete in future updates
			 *
			 * add_submenu_page(
			 * 'dropship-express',
			 * esc_html__( 'Statistics', 'dropshipexpress' ),
			 * esc_html__( 'Statistics', 'dropshipexpress' ),
			 * DSE_Settings::Who_Can( 'view_stats' ),
			 * 'dse-statistics',
			 * [
			 * 'DSE_Import',
			 * 'Render_Statistics_CB',
			 * ]
			 * );
			 */

			/**
			 * Create a menu item to view the logs
			 *
			 */
			add_submenu_page(
				'dropship-express',
				esc_html__( 'Logs', 'dropshipexpress' ),
				esc_html__( 'Logs', 'dropshipexpress' ),
				DSE_Settings::Who_Can( 'permission_log_access' ),
				'dse-logs',
				[
					'DSE_Import',
					'Render_Logs',
				],
				5
			);

			/**
			 * Create a menu item to view the upgrade button. Only
			 * show if the plugin is not upgraded
			 *
			 */
			if ( ! DSE_Settings::Is_Premium() ) {
				add_submenu_page(
					'dropship-express',
					esc_html__( 'Upgrade to Pro', 'dropshipexpress' ),
					esc_html__( 'Upgrade to Pro', 'dropshipexpress' ),
					'manage_options',
					'dse-upgrade',
					[
						'DSE_Settings',
						'Upgrade_Page_CB',
					],
					6
				);
			}
			/**
			 * Create a page to output the plugin activation
			 */
			add_submenu_page(
				'dropship-express',
				esc_html__( 'Plugin Activation', 'dropshipexpress' ),
				esc_html__( 'Plugin Activation', 'dropshipexpress' ),
				'manage_options',
				'dse-activation',
				[
					'DSE_Settings',
					'Activation_Page_CB',
				]
			);

		}

	}