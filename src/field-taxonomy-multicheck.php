<?php

namespace PhilipNewcomer\UM_Taxonomy_Multicheck;

/**
 * Register the Taxonomy Multicheck field.
 */
add_filter( 'um_core_fields_hook', function( $fields ) {

	$fields['taxonomy_multicheck'] = [
		'name'     => 'Taxonomy Multicheck',
		'col1'     => [ '_title', '_taxonomy_select', '_metakey', '_help', '_visibility' ],
		'col2'     => [ '_label', '_public', '_roles' ],
		'col3'     => [ '_required', '_editable', '_icon' ],
		'validate' => [
			'_title'   => [
				'mode'  => 'required',
				'error' => __( 'You must provide a title', 'ultimatemember' ),
			],
			'_metakey' => [
				'mode' => 'unique',
			],
		],
	];

	return $fields;
} );

/**
 * Render the Taxonomy Multicheck field in view mode.
 */
add_filter( 'um_view_field_value_taxonomy_multicheck', function( $output, $data ) {

	$terms = get_terms( [
		'hide_empty' => false,
		'include'    => $output,
		'taxonomy'   => $data['taxonomy_select'],
	] );

	$output = render_hierarchical_term_list( $terms, 0, 'view', $data );

	return $output;
}, 10, 2 );

/**
 * Render the Taxonomy Multicheck field in edit mode.
 */
add_filter( 'um_edit_field_profile_taxonomy_multicheck', function( $output, $data ) {
	global $ultimatemember;
	$key = $data['metakey'] ?? null;

	$terms = get_terms( [
		'hide_empty' => false,
		'taxonomy'   => $data['taxonomy_select'],
	] );

	ob_start();

	?>
	<div class="um-field <?php echo esc_attr( $data['classes'] ?? null ); ?>" data-key="<?php echo esc_attr( $key ); ?>">
		<?php
		if ( ! empty( $data['label'] ) ) {
			echo $ultimatemember->fields->field_label( $data['label'], $key, $data );
		}
		?>
		<div class="um-field-area">
			<?php echo render_hierarchical_term_list( $terms, 0, 'edit', $data ); ?>
			<div class="um-clear"></div>
		</div>

		<?php
		if ( $ultimatemember->fields->is_error( $key ) ) {
			echo $ultimatemember->fields->field_error( $ultimatemember->fields->show_error( $key ) );
		}
		?>
	</div>
	<?php

	return ob_get_clean();
}, 10, 2 );

/**
 * Renders an HTML list of hierarchical terms.
 *
 * @param array  $terms     The terms to render.
 * @param int    $parent_id The parent term ID of the current level.
 * @param string $context   The current context (view/edit).
 * @param array  $data      The Ultimate Member field data.
 *
 * @return string The rendered HTML list.
 */
function render_hierarchical_term_list( $terms = [], $parent_id = 0, $context = 'view', $data = [] ) {
	global $ultimatemember;
	$current_function = __FUNCTION__;
	$list_items_html       = null;

	foreach ( $terms as $term ) {

		if ( $parent_id !== $term->parent ) {
			continue;
		}

		ob_start();

		?>
		<li>
			<?php if ( 'edit' === $context ) : ?>
				<?php $is_selected = $ultimatemember->fields->is_selected( $data['metakey'], $term->term_id, $data ); ?>
				<label class="um-field-checkbox <?php echo $is_selected ? 'active' : null; ?>">
					<input type="checkbox" name="<?php echo esc_attr( $data['metakey'] ); ?>[]" value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo $is_selected ? 'checked' : null; ?>>

					<span class="um-field-checkbox-state">
						<i class="<?php echo $is_selected ? 'um-icon-android-checkbox-outline' : 'um-icon-android-checkbox-outline-blank'; ?>"></i>
					</span>

					<span class="um-field-checkbox-option">
						<?php echo esc_html( $term->name ); ?>
					</span>
				</label>
			<?php else : ?>
				<?php echo esc_html( $term->name ); ?>
			<?php endif; ?>

			<?php echo $current_function( $terms, $term->term_id, $context, $data ); ?>
		</li>
		<?php

		$list_items_html .= ob_get_clean();
	}

	if ( ! empty( $list_items_html ) ) {
		$inline_style = null;
		if ( 'edit' === $context ) {
			$inline_style = 'list-style: none;';
			if ( 0 === $parent_id ) {
				$inline_style .= ' margin-left: 0; padding-left: 0;';
			}
		}
		return sprintf( '<ul style="%s">%s</ul>',
			esc_attr( $inline_style ),
			$list_items_html
		);
	}

	return null;
}
