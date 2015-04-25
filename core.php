<?php
/**
 * Plugin Name: Caldera Forms - Formatted summary mailer template
 * Plugin URI:  https://calderawp.com
 * Description: Formats the {summary} tag to be a neat table
 * Version:     1.0.0
 * Author:      David Cramer
 * Author URI:  https://calderawp.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */



// add filters
add_filter('caldera_forms_do_magic_tag', 'cf_format_summary_table', 10, 2);
function cf_format_summary_table( $magic_tag, $tag ){
	global $form;

	if( $tag == '{summary}' && !empty($form['fields'])){
		$out = array();
		$out[] = '<table width="99%" style="background:#e7e7e7;" cellpadding="0" cellspacing="0" border="0">';
		foreach($form['fields'] as $field_id=>$field){
			if( in_array( $field['type'], array('button', 'recaptcha', 'html' ) ) ){
				continue;
			}
			// filter the field to get field data
			$field = apply_filters( 'caldera_forms_render_get_field', $field, $form);
			$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field['type'], $field, $form);
			$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field['slug'], $field, $form);

			$field_values = (array) Caldera_Forms::get_field_data($field_id, $form);

			if( isset( $field_values['label'] ) ){
				$pre_field_values = apply_filters( 'caldera_forms_view_field_' . $field['type'], $field_values['value'], $field, $form);
				if( $pre_field_values != $field_values['label'] ){
					//$pre_field_values = $field_values['label'] .' (' . $pre_field_values . ')';
				}
				$field_values = $pre_field_values;
			}else{
				foreach( $field_values as &$field_value ){
					if( is_array( $field_value ) && isset( $field_value['label'] ) ){												
						$pre_field_value = apply_filters( 'caldera_forms_view_field_' . $field['type'], $field_value['value'], $field, $form);
						if( $pre_field_value != $field_value['label'] ){
							$pre_field_value = $field_value['label'] .' (' . $field_value . ')';
						}
						$field_value = $pre_field_value;
					}else{
						//$field_value = apply_filters( 'caldera_forms_view_field_' . $field['type'], $field_value, $field, $form);
					}
					
				}
			}

			$field_value = implode(', ', (array) $field_values);
			
			if($field_value !== null && strlen($field_value) > 0){
				$out[] = '<tr>';
				$out[] = '<td colspan="2" style="background:#eaeaea;font-weight:bold;padding:4px 4px 4px 4px;"><b>' . $field['label'] . '</b></td>';
				$out[] = '</tr>';
				$out[] = '<tr>';
				$out[] = '<td style="background:#ffffff;" width="25">&nbsp;</td>';
				$out[] = '<td style="background:#ffffff;padding:4px 4px 4px 4px">%' . $field['slug'] . '%</td>';
				$out[] = '</tr>';
			}

		}
		// vars
		if( !empty( $form['variables'] ) ){
			foreach( $form['variables']['keys'] as $var_key=>$var_label ){
				if( $form['variables']['types'][ $var_key ] == 'entryitem' ){
					$label = ucfirst( str_replace('_', ' ', $var_label ) );
					$out[] = '<tr>';
					$out[] = '<td colspan="2" style="background:#eaeaea;font-weight:bold;padding:4px 4px 4px 4px;"><b>' . $label . '</b></td>';
					$out[] = '</tr>';
					$out[] = '<tr>';
					$out[] = '<td style="background:#ffffff;" width="25">&nbsp;</td>';
					$out[] = '<td style="background:#ffffff;padding:4px 4px 4px 4px">' . $form['variables']['values'][ $var_key ] . '</td>';
					$out[] = '</tr>';
				}
			}
		}

		$out[] = '</table>';

		if(!empty($out)){
			$magic_tag = implode("\r\n", $out);
		}

	



	}

	return $magic_tag;

}
