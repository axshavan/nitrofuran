<? foreach($items['items'] as &$item): ?>
	<div class="item">
		<h2><a href="<?= h($item['href']) ?>" target="_blank"><?= h($item['title']) ?></a></h2>
		<?= h($item['description']) ?>
	</div>
<? endforeach; ?>
