<?php foreach ($options['custom_urls'] as $lang => $urls): ?>

<h3><?php echo $lang; ?></h3>

<?php
foreach ($urls as $key => $value) {
	$keyGenerate = sprintf('%s-%s-%s', $lang, $key, $value); ?>
	<div style="display:flex" id="<?php echo $keyGenerate; ?>">
		<div>
			<label>
				URL de base :
			</label>
			<input type="text" value="<?php echo $value ?>" class="base-url base-url-<?php echo $keyGenerate; ?>" data-key="<?php echo $keyGenerate; ?>" name="<?php echo sprintf( '%s[%s][%s][%s]', WEGLOT_SLUG, 'custom_urls', $lang, $key ); ?>" data-lang="<?php echo $lang; ?>" />
		</div>
		<div>
			<label>
				URL Custom :
			</label>
			<input type="text" value="<?php echo $key; ?>" data-key="<?php echo $keyGenerate; ?>" class="custom-url custom-<?php echo $keyGenerate; ?>" data-lang="<?php echo $lang; ?>" />
		</div>

		<div>
			<button class="js-btn-remove" data-key="<?php echo $keyGenerate; ?>">
				<span class="dashicons dashicons-minus"></span>
			</button>
		</div>

	</div>

	<?php
}

?>

<script>
	document.addEventListener('DOMContentLoaded', function(){
		const $ = jQuery

		$('.custom-url').on('keyup', function(e){
			const key = $(this).data('key')
			const lang = $(this).data('lang')
			console.log(key)
			$('.base-url-' + key).attr('name', 'weglot-translate[custom_urls][' + lang + '][' + e.target.value + ']')
		})

		$('.js-btn-remove').on('click', function(e){
			e.preventDefault();

			$('#' + $(this).data('key')).remove()
		})
	})
</script>

<?php endforeach;
settings_fields( WEGLOT_OPTION_GROUP );
submit_button();?>
