<?php if ($config->google_analytics) {
    ?>
	<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo $config->google_analytics;
    ?>']);
	<?php if ($config->google_analytics_domain) {
    ?>
		_gaq.push(['_setDomainName', '<?php echo $config->google_analytics_domain;
    ?>']);
	<?php
}
    ?>
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	</script>
<?php
} ?>
