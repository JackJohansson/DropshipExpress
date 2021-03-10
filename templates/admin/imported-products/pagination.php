<?php
	/**
	 * Template to output pagination for
	 * imported products
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	// No need to output pagination if there's only 1 page
	if ( $total < 2 ) {
		return;
	}

?>
<!-- Begin Pagination -->
<div id="dse-publish-pagination" class="row dse-pagination">
	<div class="col-xl-12">

		<div class="dse-box">
			<div class="dse-box-body">

				<!--begin: Pagination-->
				<div class="dse-pagination dse-pagination-main">
					<ul class="dse-pagination-links">
						<?php
							if ( $current > 1 ) {
								?>
								<li class="dse-pagination__link--first">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=dse-view-imported' ) ) ?>"><i class="fa fa-angle-double-left dse-font-main"></i></a>
								</li>
								<li class="dse-pagination__link--next">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=dse-view-imported&current_page=' . intval( $current - 1 ) ) ) ?>"><i class="fa fa-angle-left dse-font-main"></i></a>
								</li>
								<?php
							}

							for ( $x = ( $current - 3 ); $x < ( $current + 4 ); $x++ ) {
								if ( $x > 0 && $x <= $total ) {
									if ( $x == $current - 3 ) {
										echo '<li><a>...</a></li>';
									}
									if ( $x == $current ) {
										echo "<li class='dse-pagination-link-active'><a>{$x}</a></li>";
									} else {
										echo "<li><a href='" . esc_url( admin_url( 'admin.php?page=dse-view-imported&current_page=' . intval( $x ) ) ) . "'>$x</a></li>";
									}
									if ( $x == $current + 3 ) {
										echo '<li><a>...</a></li>';
									}
								}
							}

							if ( $current + 1 < $total ) {
								?>
								<li class="dse-pagination-link-previous">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=dse-view-imported&current_page=' . intval( $current + 1 ) ) ) ?>"><i class="fa fa-angle-right dse-font-main"></i></a>
								</li>
								<li class="dse-pagination-link-last">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=dse-view-imported&current_page=' . intval( $total ) ) ) ?>"><i class="fa fa-angle-double-right dse-font-main"></i></a>
								</li>
								<?php
							}
						?>
					</ul>
				</div>

				<!--end: Pagination-->
			</div>
		</div>

	</div>
</div>
<!-- End Pagination -->