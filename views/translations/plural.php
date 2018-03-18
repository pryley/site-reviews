<tr class="glsr-string-tr">
	<td class="glsr-string-td1 column-primary">
		<p>{{ data.s1 }}</p>
		<p>{{ data.p1 }}</p>
		<p class="row-actions">
			<span class="delete"><a href="#{{ data.index }}" class="delete" aria-label="<?= __( 'Delete translation string', 'site-reviews' );?>"><?= __( 'Delete', 'site-reviews' ); ?></a></span>
		</p>
		<button type="button" class="toggle-row">
			<span class="screen-reader-text"><?= __( 'Show custom translation', 'site-reviews' ); ?></span>
		</button>
	</td>
	<td class="glsr-string-td2">
		<input type="hidden" name="{{ data.prefix }}[settings][strings][{{ data.index }}][id]" value="{{ data.id }}" data-id>
		<input type="hidden" name="{{ data.prefix }}[settings][strings][{{ data.index }}][s1]" value="{{ data.s1 }}">
		<input type="hidden" name="{{ data.prefix }}[settings][strings][{{ data.index }}][p1]" value="{{ data.p1 }}">
		<input type="text" name="{{ data.prefix }}[settings][strings][{{ data.index }}][s2]" placeholder="<?= __( 'singular', 'site-reviews' ); ?>" value="{{ data.s2 }}">
		<input type="text" name="{{ data.prefix }}[settings][strings][{{ data.index }}][p2]" placeholder="<?= __( 'plural', 'site-reviews' ); ?>" value="{{ data.p2 }}">
		<span class="description">{{ data.desc }}</span>
	</td>
</tr>
