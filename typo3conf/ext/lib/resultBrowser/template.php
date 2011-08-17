<?php if (!defined ('TYPO3_MODE'))     die ('Access denied.'); ?>

<ul class="resultBrowser">

<?php if($this->get('previousViewIsVisible')): ?>
	<li class="previous"><a href="<?php print $this->get('previousViewUrl'); ?>">&lt;</a></li>
<?php endif; ?>

<?php if($this->get('firstViewIsVisible')): ?>
	<li class="first"><a href="<?php print $this->get('firstViewUrl'); ?>"><?php print $this->get('firstViewNumber'); ?></a></li>
<?php endif; ?>

<?php if($this->get('precedingDotsAreVisible')): ?> 
	<li class="dots">... </li>
<?php endif; ?>

<?php print $this->get('precedingViewsString'); ?>

<?php if($this->get('precedingViewsAreVisible')): ?>
	<?php foreach($this->get('precedingViews') as $view): ?>
		<li class="preceding"><a href="<?php print $view['url']; ?>"><?php print $view['number']; ?></a></li>
	<?php endforeach; ?>
<?php endif; ?>

<?php if($this->get('currentViewIsVisible')): ?>
	<li class="current"><?php print $this->get('currentViewNumber'); ?></li>
<?php endif; ?>

<?php if($this->get('succeedingViewsAreVisible')): ?>
	<?php foreach($this->get('succeedingViews') as $view): ?>
		<li class="succeeding"><a href="<?php print $view['url']; ?>"><?php print $view['number']; ?></a></li>
	<?php endforeach; ?>
<?php endif; ?>

<?php if($this->get('succeedingDotsAreVisible')): ?>
	<li class="dots">... </li>
<?php endif; ?>

<?php if($this->get('lastViewIsVisible')): ?>
	<li class="last"><a href="<?php print $this->get('lastViewUrl'); ?>"><?php print $this->get('lastViewNumber'); ?></a></li>
<?php endif; ?>

<?php if($this->get('nextViewIsVisble')): ?>
	<li class="next"><a href="<?php print $this->get('nextViewUrl'); ?>">&gt;</a></li>
<?php endif; ?>

</ul>
<br class="clearer"/>
