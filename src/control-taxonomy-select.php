<?php

namespace PhilipNewcomer\UM_Taxonomy_Multicheck;

/**
 * Ultimate Member Taxonomy Select Control
 */
add_action( 'um_admin_field_edit_hook_taxonomy_select', function( $value = null ) {

	$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

	?>
	<p>
		<label for="_taxonomy_select">
			Taxonomy
			<span class="um-admin-tip">
				<span class="um-admin-tipsy-w" title="Select the taxonomy whose terms should be used to populate the options.">
					<i class="dashicons dashicons-editor-help"></i>
				</span>
			</span>
		</label>

		<select name="_taxonomy_select" id="_taxonomy_select" class="umaf-selectjs" style="width: 100%">
			<?php
			foreach ( $taxonomies as $taxonomy_slug => $taxonomy_object ) {
				printf( '<option value="%s" %s>%s</option>',
					esc_attr( $taxonomy_slug ),
					selected( $taxonomy_slug, $value, false ),
					esc_html( $taxonomy_object->labels->singular_name )
				);
			}
			?>
		</select>
	</p>
	<?php
} );
