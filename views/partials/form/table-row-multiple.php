<?php defined( 'WPINC' ) || die; ?>

<tr class="glsr-field {{ class }}">
	<th scope="row">{{ label }}</th>
	<td>
		<fieldset data-depends='{{ depends_on }}'>
			<legend class="screen-reader-text"><span>{{ legend }}</span></legend>
			{{ field }}
		</fieldset>
	</td>
</tr>
