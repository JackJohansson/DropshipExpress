<?php
	/**
	 * Template to render the imported products
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}
?>

<!-- Begin Imported Products List -->
<div class="dse-content dse-grid-item dse-grid-item--fluid dse-grid dse-grid-horizontal">
	<div class="dse-container dse-container-fluid  dse-grid-item dse-grid-item-fluid">

		<!-- Begin Header -->
		<h3 class="dse-m-b-20"><?php esc_html_e( 'Imported Products', 'dropshipexpress' ); ?>
			<span id="dse-imported-count-badge" class="title-count theme-count"><?php echo DSE_Import::Get_Imported_Count(); ?></span>
		</h3>
		<!-- End Header -->

		<!-- Begin Imported List -->
		<?php DSE_Import::Get_Imported_List(); ?>
		<!-- End Imported List -->

	</div>
</div>
<!-- End Imported Products List -->
