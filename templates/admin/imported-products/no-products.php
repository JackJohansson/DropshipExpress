<?php
	/**
	 * Render the "No products" page for imported list
	 *
	 */
	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}
?>

<div class="dse-box">
	<div class="dse-box-header">
		<div class="dse-box-header-wrapper">
			<h3 class="dse-box-header-title"><?php esc_html_e( 'Oops!', 'dropshipexpress' ); ?></h3>
		</div>
	</div>
	<div class="dse-box-body">

		<div class="dse-box-3">
			<p>
				<?php
					printf(
						wp_kses(
							/* translators: %1$s is replaced with the product's URL */
							__( 'No product has been imported yet, or perhaps all the imported products have been published. Try importing some new products <a href="%1$s">here.</a>', 'dropshipexpress' ),
							[
								'a' => [
									'href' => [],
								],
							]
						),
						esc_url( admin_url( 'admin.php?page=dse-import-products' ) )
					);
				?>
			</p>
		</div>
	</div>
</div>
